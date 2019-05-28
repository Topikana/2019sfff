<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 31/07/17
 * Time: 13:11
 */

namespace AppBundle\Entity;


use AppBundle\Services\OpsLavoisier\Exceptions\OpsLavoisierServiceException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Lavoisier\Hydrators\EntriesHydrator;
use Lavoisier\Query;

use AppBundle\Services\OpsLavoisier\Exceptions;

/**
 * @ORM\Entity()
 */
class User implements UserInterface, \Serializable
{

    const LAVOISIER_ROLES_VIEW = 'OPSCORE_user';
    const EGI_SECURITY_OFFICER = "EGI CSIRT Officer";
    const NGI_SECURITY_OFFICER = "NGI Security Officer";
    const SITE_SECURITY_OFFICER = "Site Security Officer";

    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $dn;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $email;
    
    /**
     * @var string
     * @ORM\Column(type="string")
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Notepad", inversedBy="last_modifer")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $username;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $roles;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $opRoles;

    /**
     * One User has One Setting.
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Settings", mappedBy="user",cascade={"persist"})
     */
    protected $setting;

    /**
     * @return User
     */
    public function SetId($username)
    {
        $this->id= $username;
        
        return $this;
    }
    

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     *
     * @return array
     */
    public function getOpRoles()
    {
        return $this->opRoles;
    }



    /**
     * User constructor.
     * @param string $dn
     * @param string password
     * @param string salt
     * @param array $roles
     * @param array $opRoles
     */
    public function __construct($dn, $password, $salt = null, array $roles=null, array $opRoles = null)
    {
        $arrayDn = $this->explodeDn($dn);

        if (($password == "fakeDn") || (substr_count($dn, "/") > 2)) {
            $this->dn = $dn; // Dn "/" format
        } else {
            $this->dn = $arrayDn["dn"]; // Get Dn with "/" format
        }
        $this->username = $arrayDn["cn"];
        $this->roles = $roles;
        $this->opRoles = $opRoles;




    }

    /*
   * Parse dn and format it
   */
    function explodeDn($dn)
    {
        $strDn = $this->multiexplode(array(",", ";", "/"), $dn);
        $dn = "";
        $cn = '';
        for ($i = count($strDn) - 1; $i >= 0; $i--) {
            $dn .= "/" . $strDn[$i];

            //Parse CN
            $start = 'CN=';
            $ini = strpos($strDn[$i], $start);

            if (isset($ini) && $ini !== false) {
                $cn = substr($strDn[$i], $ini + 3);
            }

        }

        return array("dn" => $dn, "cn" => $cn);
    }


    /**
     *
     * @param array $delimiters : list of delimiter for parse => array(";", "/", ":", ...)
     * @param string $string
     *
     */
    function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }


    /**
     *
     * @param $lavoisier
     * @param null $dn
     */
    public function setOpRoles($lavoisier,$dn=null)
    {

        try {
            if (!isset($dn))
            $dn = $this->getDn();
            if (isset($lavoisier) && $lavoisier != null) {

                /**
                 *      Get roles in lavoisier view : OPSCORE_users
                 *      without xpath
                 */
                $lquery = new Query($lavoisier, $this::LAVOISIER_ROLES_VIEW, 'lavoisier');
                $lquery->setMethod('POST');
                $array_POST = array('DN' => $dn);
                $lquery->setPostFields($array_POST);

                $hydrator = new EntriesHydrator();
                $lquery->setHydrator($hydrator);

                $results = $lquery->execute()->getArrayCopy();

                /*
                 *      Create array roles
                 *
                 * $sRole[1] = type // vo | ngi/site | project
                 * $sRole[2] = entity // voName | ngiName/site/Name | projectName
                 * $sRole[0] = role in entity
                 */
                $this->opRoles = array();
                if(!empty($results) && isset($results[$dn]["ROLES"])) {
                    $allRoles = $results[$dn]["ROLES"];

                    foreach ($allRoles as $role) {
                        $sRole = explode("::", $role);
                        $this->opRoles[$sRole[1]][$sRole[2]][] = $sRole[0];

                    }
                }
            } else {
                $e = new \Exception('Lavoisier url is not setUp');
                throw new OpsLavoisierServiceException("Query null",$e );
            }
        } catch (\Exception $e) {
            echo "<div class='container'> <div id='lavoisierRoleErrorDiv' class='panel panel-danger'>
                        <div class='panel-heading'>Lavoisier roles view error</div>
                        <div class='panel-body'".$e->getMessage()."</div>
                   </div></div>";
        }
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setOpRolesAAI($roles)
    {
        $this->opRoles = $roles;
        return $this;
    }



    
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }


 
    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dn
     *
     * @param string $dn
     *
     * @return User
     */
    public function setDn($dn)
    {
        $this->dn = $dn;

        return $this;
    }

    /**
     * Get dn
     *
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

 
    
    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    // -------------------------------------------

    /**
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([$this->id, $this->username, $this->roles, $this->dn, $this->email, $this->opRoles, $this->setting]);
    }

    /**
     * @return void
     */
    public function unserialize($serialized)
    {
        list($this->id, $this->username, $this->roles, $this->dn, $this->email, $this->opRoles, $this->setting) = unserialize($serialized);
    }

    public function isSuUser()
    {
        $isSuUser = false;
        if (isset($this->opRoles["project"]["EGI"]) || isset($this->opRoles["ngi"]["EGI.eu"])) {
            $isSuUser = true;
        }
        return $isSuUser;
    }

    public function canModifyVO($voName)
    {
        $canModify = false;
        if ($this->isSuUser() || (isset($this->opRoles["vo"][$voName])

            )) {
            $canModify = true;
        }
        return $canModify;
    }

    /**
     * @param $entityType : project|ngi|site
     * @param $entity : default = null => Search in all $entityType
     */
    public function isSecurityOfficer($roleType, $name = "all")
    {
        $isSO = false;

        if ($this->isSuUser()) {
            // Is Super User
            $isSO = true;
        } else {
            if (isset($this->opRoles[$roleType])) { // if user has roleType
                switch ($roleType) { // get const by roleType
                    case "ngi":
                        $roleSecurity = User::NGI_SECURITY_OFFICER;
                        break;
                    case "site":
                        $roleSecurity = User::SITE_SECURITY_OFFICER;
                        break;
                    default:
                        $roleSecurity = User::EGI_SECURITY_OFFICER;
                        break;
                }

                if ($name != "all") {
                    if (isset($this->opRoles[$roleType][$name]) && in_array($roleSecurity, $this->opRoles[$roleType][$name])) {
                        $isSO = true;
                    }
                } else {
                    foreach ($this->opRoles[$roleType] as $ngi) {
                        if (in_array($roleSecurity, $ngi)) {
                            $isSO = true;
                        }
                    }
                }
            }
        }
        return $isSO;
    }


    public function getMyVo() {
        $arrayVO = array();
        foreach($this->getOpRoles()["vo"] as $vo => $roles) {
            if ($this->canModifyVO($vo)) {
                $arrayVO[] = $vo;
            }
        }

        return $arrayVO;
    }

    public function getNgiToString(){
        return (!empty($this->getOpRoles()['ngi']))? implode(',', array_keys($this->getOpRoles()['ngi'])) : '';
    }


    /**
     * Set setting
     *
     * @param \AppBundle\Entity\Settings $setting
     *
     * @return User
     */
    public function setSetting(\AppBundle\Entity\Settings $setting = null)
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * Get setting
     *
     * @return \AppBundle\Entity\Settings
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * Set default setting
     *
     * @return User
     */
    public function setDefaultSetting(){
        $this->setting = new Settings();

        return $this;
    }

    /**
     * get all site where role is security Officer
     * @param $lavoisierUrl
     */

    public function getSiteSecurityOfficer($lavoisierUrl){

        if(!$this->isSecuOfficer()){
            $ngiSecurity = []; $siteSecurity = [];
            $result = [];
            if (isset($this->getOpRoles()['ngi'])) {
                foreach ($this->getOpRoles()['ngi'] as $ngi => $role){
                    if ($role === self::EGI_SECURITY_OFFICER || $role === self::NGI_SECURITY_OFFICER || $role === self::SITE_SECURITY_OFFICER ){
                        array_push($ngiSecurity, $ngi);
                    }
                }
                $ngiSecurity = implode(',', $ngiSecurity);
                $result = $this->getListSiteByNGI($lavoisierUrl, $ngiSecurity);
            }
            if (isset($this->getOpRoles()['site'])) {

                foreach ($this->getOpRoles()['site'] as $site => $role){
                    if ($role === self::EGI_SECURITY_OFFICER || $role === self::NGI_SECURITY_OFFICER || $role === self::SITE_SECURITY_OFFICER ){
                        array_push($siteSecurity, $site);
                    }
                }
                $result = array_merge($result, $siteSecurity);
                $result = implode(',', array_keys($result));

            }else{
                $result = implode(',', array_keys($result));
            }

            return $result;
        }

        return 'egi';
    }

    /**
     * @param $lavoisierUrl
     * @param string $ngiToString
     * @return array
     */
    public function getListSiteByNGI($lavoisierUrl, $ngiToString = null) : array {

//        $lavoisierUrl = $this->getParameter('lavoisierUrl');

        $ngi = (!empty($ngiToString))? $ngiToString : $this->getNgiToString();

        if(!empty($ngi)){
            try{

                $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_SITE', 'lavoisier');
                if($ngi != null){
                    $lquery->setPath("/e:entries/e:entries[e:entry[@key='NGI']/text()='{$ngi}'][e:entry[@key='CERTIFICATION_STATUS']/text()='Certified'][e:entry[@key='PRODUCTION_INFRASTRUCTURE']/text()='Production']");
                }
                $hydrator = new EntriesHydrator();
                $lquery->setHydrator($hydrator);
                $result = $lquery->execute();
                $siteList = $result->getArrayCopy();

            }catch (\Exception $e) {
                $this->addFlash(
                    "danger",
                    "ROD Dashboard- Can't get List of Site by NGI  .. Lavoisier call failed - ".$e->getMessage()
                );
            }

            return $siteList;
        }

        return [];
    }

    public function isSecuOfficer(): bool {

        if($this->isSuUser()){
            return true;
        }else{
            if (isset($this->getOpRoles()['ngi'])) {
                foreach ($this->getOpRoles()['ngi'] as $ngi => $role){
                    if ($role === self::NGI_SECURITY_OFFICER){
                        return true;
                    }
                }
            }
            if (isset($this->getOpRoles()['site'])) {
                foreach ($this->getOpRoles()['site'] as $site => $role){
                    if ($role === self::SITE_SECURITY_OFFICER ){
                        return true;
                    }
                }
            }
            return false;
        }
    }
}
