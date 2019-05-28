<?php
// src/AppBundle/Security/User/UserCreator.php
namespace AppBundle\Security\User;

use AppBundle\Entity\User;

use AppBundle\Listener\DoctrineExtensionListener;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Tests\Models\Cache\Token;
use function GuzzleHttp\Promise\exception_for;
use Lavoisier\Hydrators\EntriesHydrator;
use Lavoisier\Query;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;
use LightSaml\SpBundle\Security\User\UsernameMapperInterface;
use AppBundle\Entity\User\MyAttributeMapper;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


use AppBundle\Services\OpsLavoisier\Exceptions;

class UserCreator implements UserCreatorInterface
{
    const LAVOISIER_ROLES_VIEW = 'OPSCORE_user';

    /** @var ObjectManager */
    private $objectManager;

    /** @var UsernameMapperInterface */
    private $usernameMapper;



    public function __construct(ObjectManager $objectManager, UsernameMapperInterface $usernameMapper ){
        $this->objectManager = $objectManager;
        $this->usernameMapper = $usernameMapper;


    }


    /**
     * @param Response $response
     *
     * @return UserInterface|null
     */
    public function createUser(Response $response )
    {


        $reader = $response->getFirstAssertion();
        $responseAttr = array();
        $Roles = array();

        $username = "anonymous";
        $email = "anonymous@anonymous.com";
        $tabDn=array();
        $lavoisierUrl="cclavoisier01";


        $debug = "";
        foreach ($reader->getFirstAttributeStatement()->getAllAttributes() as $attribute) {

            $debug .= "-" . $attribute->getName() . "\\" . $attribute->getFirstAttributeValue();

            if ($attribute->getName() == 'urn:oid:1.3.6.1.4.1.5923.1.1.1.13') {
                $id = $attribute->getFirstAttributeValue();
            }


            if ($attribute->getName() == 'urn:oid:0.9.2342.19200300.100.1.3') {
                $email = $attribute->getFirstAttributeValue();
            }


            if ($attribute->getName() == 'urn:oid:2.5.4.49') {
                $dn = $attribute->getFirstAttributeValue();
                $tabDn[]=$dn;
                $Roles["infos"]["certificates"][]=$dn;
            }
             if ($attribute->getName() == 'urn:oid:1.3.6.1.4.1.11433.2.2.1.9') {
                 $dn2 = $attribute->getFirstAttributeValue();
                 $Roles["infos"]["certificates"][]=$dn2;
             }


            if ($attribute->getName() == 'urn:oid:2.16.840.1.113730.3.1.241') {
                $username = $attribute->getFirstAttributeValue();
            }

            if ($attribute->getName() == 'urn:oid:2.5.4.42') {
                $firstname = $attribute->getFirstAttributeValue();
            }

            if ($attribute->getName() == 'urn:oid:2.5.4.4') {
                $lastname = $attribute->getFirstAttributeValue();
            }


            if ($attribute->getName() == 'urn:oid:1.3.6.1.4.1.5923.1.1.1.7') {
                $responseAttr[] = $attribute->getAllAttributeValues();
                foreach ($attribute->getAllAttributeValues() as $key => $attributeValue) {

                    if (strstr($attributeValue, ":goc.egi.eu:")) {
                        $attributeValue2 = explode(":goc.egi.eu:", $attributeValue);
                        $tabRoles = explode(":", $attributeValue2[1]);
                        $entityName = $tabRoles[1];
                        $Role = str_replace("+", " ", substr($tabRoles[2], 0, strpos($tabRoles[2], '@egi.eu')));
                        $tabentityRole = explode("+", $tabRoles[2]);
                        $entityType = strtolower($tabentityRole[0]);

                        if (!(in_array($Role,$Roles[$entityType][$entityName])))
                        $Roles[$entityType][$entityName][] = $Role;

                    }
                    if (strstr($attributeValue, "urn:geant:eudat.eu:group:")) {
                        $attributeValue2 = explode("urn:geant:eudat.eu:group:", $attributeValue);
                        $role=explode($attributeValue2[1],"#");
                        $Roles["project"]["EUDAT"][] =$role[0];
                    }
                    if (strstr($attributeValue, "urn:mace:egi.eu:aai.egi.eu:member@")) {
                        $attributeValue2 = explode("urn:mace:egi.eu:aai.egi.eu:member@", $attributeValue);
                        $entityName = $attributeValue2[1];
                        $Roles["vo"][$entityName][] ="member";
                    }



                }

            }

        }


        if ($username = "anonymous") {
            if (isset($firstname) and isset($lastname)) {
                $username = $firstname . " " . $lastname;
            }
            else
                throw new \Exception('The authentication has failed.');
        }

        if (!(isset($dn)))
            $dn = '/O=AAI/CN=' . $username . '/Id=' . $id;

        $user = new User($dn, 'EGI SSO', null, array('ROLE_USER'));
        $user
            ->setUsername($username)
            ->setEmail($email);

        $lquery = new Query($lavoisierUrl, $this::LAVOISIER_ROLES_VIEW, 'lavoisier');
        $lquery->setMethod('POST');
        $array_POST = array('DN' => $dn);
        $lquery->setPostFields($array_POST);

        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);

        $results = $lquery->execute()->getArrayCopy();


        if(!empty($results) && isset($results[$dn]["ROLES"])) {
            $allRoles = $results[$dn]["ROLES"];

            foreach ($allRoles as $roleLav) {
                $sRole = explode("::", $roleLav);
                if (!(in_array($sRole[0],$Roles[$sRole[1]][$sRole[2]])))
                $Roles[$sRole[1]][$sRole[2]][] = $sRole[0];
            }
        }




        $user->setOpRolesAAI($Roles);


 //       if ($this->objectManager->getRepository(User::class)->findOneBy(['dn' => $dn]))
  //          $user = $this->objectManager->getRepository(User::class)->findOneBy(['dn' => $dn]);
    //    else {

            $this->objectManager->persist($user);
            $this->objectManager->flush();
      //  }



        return $user;
    }

}