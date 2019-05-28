<?php

namespace AppBundle\Model\VO;

use AppBundle\AppBundle;
use AppBundle\Entity\VO\VoDisciplines;
use AppBundle\Entity\VO\VoMailingList;
use Doctrine\ORM\Repository;
use Doctrine\ORM\EntityManager;


use Lavoisier\Hydrators\EntriesHydrator;
use Lavoisier\Query;
use Symfony\Component\DependencyInjection\ContainerInterface;

use AppBundle\Entity\VO\Vo;
use AppBundle\Entity\VO\VoHeader;
use AppBundle\Entity\VO\VoStatus;
use AppBundle\Entity\VO\VoScope;
use AppBundle\Entity\VO\VoAcknowledgmentStatements;
use AppBundle\Entity\VO\VoRessources;
use AppBundle\Entity\VO\VoVomsGroup;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use DateTime;



/**
 * Class VOModel
 * @package AppBundle\Model
 */
class ModelVO
{

    /**
     * @var $container ContainerInterface from when we get the doctrine config
     */
    protected $container;

    /**
     * @var $repositoryVO : repository for VO entity
     */
    protected $repositoryVO;

    /**
     * @var $repositoryVOHeader : repository for VOHeader entity
     */
    protected $repositoryVOHeader;

    /**
     * @var $repositoryStatus : repository for VOStatus entity
     */
    protected $repositoryStatus;

    /**
     * @var $repositoryScope : repository for VOScope entity
     */
    protected $repositoryScope;

    /**
     * @var $repositoryVOAcknowledgment : repository for VoAcknowledgmentStatements entity
     */
    protected $repositoryVOAcknowledgment;

    /**
     * @var $repositoryVOResources : repository for VOResources entity
     */
    protected $repositoryVOResources;


    /**
     * @var $repositoryVODisciplines : repository for VODisciplines entity
     */
    protected $repositoryVODisciplines;

    /**
     * @var $repositoryVOYearly : repository for VOYearlyValidation entity
     */
    protected $repositoryVOYearly;

    /**
     * @var $repositoryVOMailingList : repository for VOMailingList entity
     */
    protected $repositoryVOMailingList;


    /**
     * @var $vo Vo : the vo to get info from
     */
    protected $vo;

    /**
     * @var $voHeader VoHeader : the voheader to get info from
     */
    protected $voHeader;


    /**
     * @var $voResources VoRessources: the voresources to get info from
     */
    protected $voResources;


    /**
     * @var $voMailingList VoMailingList
     */
    protected $voMailingList;


    /**
     * @var $voYearlyValidation \AppBundle\Entity\VO\VoYearlyValidation
     */
    protected $voYearlyValidation;

    /**
     * @var $voDisicplines VoDisciplines
     */
    protected $voDisicplines;

    /**
     * @var $em EntityManager : the entity manager
     */
    protected $em;

    /**
     * @var $lavoisier : the lavoisier url
     */
    protected $lavoisier;


    public function __construct(ContainerInterface $container, $voSerial)
    {
        $this->container = $container;

        $this->repositoryVO = $this->container->get('doctrine')->getRepository('AppBundle:VO\Vo');
        $this->repositoryVOHeader = $this->container->get('doctrine')->getRepository('AppBundle:VO\VoHeader');
        $this->repositoryStatus = $this->container->get('doctrine')->getRepository('AppBundle:VO\VoStatus');
        $this->repositoryScope = $this->container->get('doctrine')->getRepository('AppBundle:VO\VoScope');
        $this->repositoryVOAcknowledgment = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoAcknowledgmentStatements");
        $this->repositoryVOResources = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoRessources");
        $this->repositoryVODisciplines =  $this->container->get("doctrine")->getRepository("AppBundle:VO\VoDisciplines");
        $this->repositoryVOMailingList =  $this->container->get("doctrine")->getRepository("AppBundle:VO\VoMailingList");
        $this->repositoryVOYearly = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoYearlyValidation");

        if ($voSerial != null) {
            $this->vo = $this->repositoryVO->findOneBy(array("serial" => $voSerial));
            $this->voHeader = $this->repositoryVOHeader->findOneBy(array("id" => $this->vo->getHeaderId()));
            $this->voResources = $this->repositoryVOResources->findOneBy(array("id" => $this->vo->getRessourcesId()));
            $this->voDisicplines = $this->repositoryVODisciplines->findOneBy(array("vo_id" => $this->vo->getSerial()));
            $this->voMailingList = $this->repositoryVOMailingList->findOneBy(array("id" => $this->vo->getMailingListId()));
            $this->voYearlyValidation = $this->repositoryVOYearly->findOneBy(array("serial" => $this->vo->getSerial()));

        }

        $this->em = $this->container->get("doctrine")->getManager();
        $this->lavoisierUrl = $this->container->getParameter("lavoisierurl");

    }

    /**
     * get the general tab part for "Other VO tab"
     * @param $voSerial
     * @return array
     * @throws \Exception
     * @throws \Lavoisier\Exceptions\HTTPStatusException
     */
    public function constructVODetailGeneral()
    {


        $vostatus = $this->repositoryStatus->find($this->voHeader->getStatusId())->getStatus();
        $voscope = $this->repositoryScope->find($this->voHeader->getScopeId())->getScope();

        //lavoisier call
        $lquery = new Query($this->lavoisierUrl, 'VoDisciplinesEntries_VO', 'lavoisier');
        $lquery->setMethod('POST');
        $lquery->setPostFields(array("vo" => $this->vo->getName()));
        $hydrator = new EntriesHydrator();

        $lquery->setHydrator($hydrator);
        $result = $lquery->execute();
        $disciplines = $result->getArrayCopy();


        return array("serial" => $this->vo->getSerial(),
            "name" => $this->vo->getName(),
            "scope" => $voscope,
            "status" => $vostatus,
            "validationDate" => $this->voHeader->getValidationDate(),
            "disciplines" => $disciplines,
            "supportedServices" => $this->getMiddleWareList(),
            "enrollmentUrl" => $this->voHeader->getEnrollmentUrl(),
            "homepageUrl" => $this->voHeader->getHomepageUrl(),
            "supportProcedureUrl" => $this->voHeader->getSupportProcedureUrl(),
            "ggus" => $this->vo->getNeedGgusSupport() == 1 ? "Yes" : "No",
            "voms" => $this->vo->getNeedVomsHelp() == 1 ? "Yes " . ($this->vo->getVomsTicketId() != 0 ? "(" . $this->vo->getVomsTicketId() . ")" : "(N.A)") : "No");

    }


    /**
     * set Last change column with actual date (UTC format)
     *
     */
    public function setLastChangeDate()
    {
        $em = $this->container->get('doctrine')->getManager();
        date_default_timezone_set('UTC');
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $this->vo->setLastChange($date);
        $em->persist($this->vo);
        $em->flush();
        date_default_timezone_set('Europe/Paris');

        return \Doctrine\ORM\UnitOfWork::STATE_MANAGED === $em->getUnitOfWork()->getEntityState($this->vo);
    }

    /**
     * get the description part for "Other VO tab"
     * @return mixed
     */
    public function constructVODetailDescription()
    {

        return $this->voHeader->getDescription();

    }

    /**
     * get the AUP part for "Other VO tab"
     * @param bool|true $format
     * @param bool|false $escapeJS
     * @return string
     */
    public function getAUP($format = true, $escapeJS = false)
    {

        $aup_type = $this->voHeader->getAupType();
        $aup_value = $this->voHeader->getAup();

        return array("type" => $aup_type, "val" => $aup_value);
    }


    /**
     * construct Acknowledgment part for "Other VO tab"
     * @return array|string
     */
    public function constructVODetailAcknowledgments()
    {
        /** @var  $voAcknowledgments \AppBundle\Entity\VO\VoAcknowledgmentStatements */
        $voAcknowledgments = $this->repositoryVOAcknowledgment->findOneBy(array("serial" => $this->vo->getSerial()));
        if (isset($voAcknowledgments) && $voAcknowledgments != null) {
            $asRelationShip = $voAcknowledgments->getRelationship();
            return array(
                "grantid" => $voAcknowledgments->getGrantId(),
                "scientificpub" => $voAcknowledgments->getPublicationUrl(),
                "suggested" => $voAcknowledgments->getSuggested(),
                "asrelationship" => $asRelationShip
            );
        } else {
            return "No Acknowledgment Statement";
        }
    }

    /**
     * construct resources par for "Other VO tab"
     * @return array
     */
    public function constructVODetailResources()
    {
        return array(
            "ram386" => $this->voResources->getRam386(),
            "ram64" => $this->voResources->getRam64(),
            "jobscratchspace" => $this->voResources->getJobScratchSpace(),
            "jobmaxcpu" => $this->voResources->getJobMaxCpu(),
            "jobmaxwall" => $this->voResources->getJobMaxWall(),
            "cvmfs" => unserialize($this->voResources->getCvmfs()),

            "number_cores" => $this->voResources->getNumberCores(),
            "minimum_ram" => $this->voResources->getMinimumRam(),
            "scratch_space_values" => $this->voResources->getScratchSpaceValues()
        );
    }

    /**
     * construct cloud part for "other VO tab"
     */
    public function constructVODetailCloud()
    {
        return array(
            "cpucore" => $this->voResources->getCpuCore(),
            "vmram" => $this->voResources->getVmRam(),
            "storagesize" => $this->voResources->getStorageSize()
        );
    }

    /**
     * construct other requirements part for "other VO tab"
     */
    public function constructVODetailOtherReq()
    {
        return $this->voResources->getOtherRequirements();
    }

    /**
     * construct contact part for "other VO tab"
     * @return array
     */
    public function getVOContacts()
    {
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select(array("c.id, c.first_name, c.last_name, c.dn, c.email, v.comment, p.profile"))
            ->from("AppBundle:VO\VoContacts", "c")
            ->innerJoin("AppBundle:VO\VoContactHasProfile", 'v', "WITH", "v.contact_id = c.id")
            ->innerJoin("AppBundle:VO\VoUserProfile", "p", "WITH", "p.id = v.user_profile_id")
            ->where('v.serial = :serial');
        $qb->setParameter("serial", $this->vo->getSerial());

        $query = $qb->getQuery();
        $contacts = $query->getResult();

        $arrayContact = array();

        foreach ($contacts as $contact) {
            $arrayContact[] = $contact;
        }

        return $arrayContact;
    }


    /**
     * construct mailing list part for "other VO tab"
     */
    public function constructVODetailsMailingList()
    {
        $repositoryMailingList = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoMailingList");

        /** @var  $voMailingList \AppBundle\Entity\VO\VoMailingList */
        $voMailingList = $repositoryMailingList->findOneBy(array("serial" => $this->vo->getSerial()), array("insert_date" => "DESC"));

        return array("admins" => $voMailingList->getAdminsMailingList(),
            "operations" => $voMailingList->getOperationsMailingList(),
            "usersupport" => $voMailingList->getUserSupportMailingList(),
            "users" => $voMailingList->getUsersMailingList(),
            "security" => $voMailingList->getSecurityContactMailingList());
    }


    /**
     *
     * @param null $vomsHost
     * @param bool|false $nbVoms
     * @return mixed
     * @throws \Exception
     * @throws \Lavoisier\Exceptions\HTTPStatusException
     */
    public function getVOMSList($vomsHost = null, $nbVoms = false)
    {
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        //get list of vomsserver with doctrine 2 call
        $qb->select("v")
            ->from("AppBundle:VO\VoVomsServer", "v")
            ->where("v.serial = ".$this->vo->getSerial());

        if (isset($vomsHost) && !is_null($vomsHost)) {
            $qb->andWhere('v.hostname = :hostname')->setParameter("hostname", $vomsHost);
        }

        $results = $qb->getQuery()->getArrayResult();


        if (!$nbVoms) {

            // retrieve voms cert infos from Lavoisier view

            $lquery = new \Lavoisier\Query($this->lavoisierUrl, 'voms-endpoint', 'lavoisier');
            $vomss = simplexml_load_string($lquery->execute());

            $tmp = array();
            foreach ($vomss as $voms) {

                $tmp[(string)$voms->HOSTNAME] = $voms;
            }

            $i = 0;

            //construct result tab with lavoisier infos
            foreach ($results as $result) {

                try {
                    //cosntruct hostname
                    $hostname = $result["hostname"];

                    if (!isset($tmp[$hostname])) {
                        throw new \exception("unable to get Voms hostname");
                    }
                    if (!isset($tmp[$hostname]->SITENAME)) {
                        throw new \exception("unable to get Voms sitename");
                    }

                    //construct site and gocurl
                    $results[$i]["site"] = (string)$tmp[$hostname]->SITENAME;
                    $results[$i]["gocurl"] = (string)$tmp[$hostname]->GOCDB_PORTAL_URL;


                    //construct voms certificate information
                    $lquery = new \Lavoisier\Query($this->lavoisierUrl, 'voms-certificate-url', 'lavoisier');
                    $lquery->setMethod('POST');
                    $lquery->setPostFields(array("key" => $hostname));
                    $xml = $lquery->execute();


                    $dom = new \DOMDocument();
                    $dom->loadXML($xml);

                    $certs = $dom->getElementsByTagName("X509Cert");

                    //validity of certificate
                    foreach ($certs as $cert) {
                        $results[$i]["expiry"] = $cert->getAttribute("expiry");
                        $results[$i]["expired"] = false;
                        if (strtotime($cert->getAttribute("expiry")) < time()) {
                            $results[$i]["expired"] = true;
                        }
                    }

                    //public key of certificate (detail)
                    $keys = $dom->getElementsByTagName("X509PublicKey");
                    foreach ($keys as $key) {
                        $results[$i]["cert"] = $key->nodeValue;
                    }

                    //dn (detail)
                    $dns = $dom->getElementsByTagName("DN");
                    foreach ($dns as $dn) {
                        $results[$i]["dn"] = $dn->nodeValue;
                    }

                    //serial number (detail)
                    $serials = $dom->getElementsByTagName("SerialNumber");
                    foreach ($serials as $serial) {
                        $results[$i]["serial_number"] = $serial->nodeValue;
                    }

                    //ca_dn (detail)
                    $cadns = $dom->getElementsByTagName("CA_DN");
                    foreach ($cadns as $cadn) {
                        $results[$i]["ca_dn"] = $cadn->nodeValue;
                    }

                    // retreive voms status (code and description)
                    if ($this->voHeader->getStatusId() == 2) {
                        $lquery = new \Lavoisier\Query($this->lavoisierUrl, 'vo-urlsCheck_AGGR', 'lavoisier');
                        $lquery->setMethod('POST');
                        $lquery->setPostFields(array("vo" => $this->vo->getName()));
                        $lquery->setPath("/root/voname/CheckVoUrls/VomsListMembers/Url[@Id='" . $hostname . "']");
                        $xmlStream = $lquery->execute();

                        $dom = new \DOMDocument();
                        $dom->loadXML($xmlStream);

                        $codes = $dom->getElementsByTagName("code");
                        foreach ($codes as $code) {
                            $results[$i]["urlcheck_code"] = $code->nodeValue;

                        }

                        $descriptions = $dom->getElementsByTagName("description");
                        foreach ($descriptions as $description) {
                            $results[$i]["urlcheck_description"] = $description->nodeValue;

                        }
                    }
                } catch (\Exception $e) {
                    $results[$i]["site"] = "NA";
                    $results[$i]["cert"] = "NA";
                    $results[$i]["dn"] = "NA";
                    $results[$i]["expiry"] = "NA";
                    $results[$i]["serial_number"] = "NA";
                    $results[$i]["ca_dn"] = "NA";
                }
                $i++;
            }
        } else {
            $results = $qb->select("count(v)")->getQuery()->getSingleScalarResult();
        }

        return $results;
    }

    /**
     * construct VOMS info part for "other VO tab"
     * @return array
     */
    public function getVOMSGroup()
    {

        $repositoryVomsGroup = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoVomsGroup");

        $voVomsGroup = $repositoryVomsGroup->findBy(array("serial" => $this->vo->getSerial()));

        $arrayVomsGroup = array();

        foreach ($voVomsGroup as $group) {
            $arrayVomsGroup[] = array(
                "id" => $group->getId(),
                "grouprole" => $group->getGroupRole(),
                "grouptype" => $group->getGroupType(),
                'description' => $group->getDescription(),
                "allocatedressources" => $group->getAllocatedRessources(),
                "isgroupused" => $group->getIsGroupUsed());
        }

        return $arrayVomsGroup;
    }


    /**
     * set the VO date of validation
     */
    public function setVoValidation()
    {
        try {

            //set the new validation date on today
            $repositoryVoYearlyValidation = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoYearlyValidation");

            $voYearlyValidation = $repositoryVoYearlyValidation->findOneBy(array("serial" => $this->vo->getSerial()));
            $voYearlyValidation->setVoValidation();

            //save validation date
            $this->em->flush();

            //call to lavoisier VoYearly to notify date
            $lquery = new \Lavoisier\Query($this->lavoisierUrl, 'VoYearly', 'notify');
            $lquery->execute();

            $return = "<p>The VO validation date has been updated successfully : " . $voYearlyValidation->getDateValidation()->format('Y-m-d H:i:s') . ".<br> The page will be reload in <strong>10 seconds</strong> approximatively.</p> <div class='ball'></div><div class='ball1'></div>";
            $isSuccess = 1;
        //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $return = "Error on updating VO validation date... [" . $e->getMessage() . "]";
            $isSuccess = 0;
        }
        //@codeCoverageIgnoreEnd

        return array("res" => $return, "isSuccess" => $isSuccess);
    }

    /**
     * get a voname by serial
     * @param $serial
     * @return mixed
     */
    public function getVONameBySerial($serial)
    {
        return $this->repositoryVO->findOneBy(array("serial" => $serial))->getName();
    }

    /**
     * get a vo by its name
     * @param $name
     * @return mixed
     */
    public function getVoByName($name)
    {
        return $this->repositoryVO->findOneBy(array("name" => $name));
    }


    /**
     * set type of middleware
     * @param $voHeader
     * @return VoHeader
     */
    public function setTypageMiddleWare(VoHeader $voHeader, $type)
    {
        if ($type == "bool") {
            $voHeader->setArcSupported((bool)$voHeader->getArcSupported());
            $voHeader->setGliteSupported((bool)$voHeader->getGliteSupported());
            $voHeader->setGlobusSupported((bool)$voHeader->getGlobusSupported());
            $voHeader->setUnicoreSupported((bool)$voHeader->getUnicoreSupported());
            $voHeader->setCloudComputingSupported((bool)$voHeader->getCloudComputingSupported());
            $voHeader->setCloudStorageSupported((bool)$voHeader->getCloudStorageSupported());
            $voHeader->setDesktopGridSupported((bool)$voHeader->getDesktopGridSupported());
        } else {
            if ($type == "integer") {
                $voHeader->setArcSupported((integer)$voHeader->getArcSupported());
                $voHeader->setGliteSupported((integer)$voHeader->getGliteSupported());
                $voHeader->setGlobusSupported((integer)$voHeader->getGlobusSupported());
                $voHeader->setUnicoreSupported((integer)$voHeader->getUnicoreSupported());
                $voHeader->setCloudComputingSupported((integer)$voHeader->getCloudComputingSupported());
                $voHeader->setCloudStorageSupported((integer)$voHeader->getCloudStorageSupported());
                $voHeader->setDesktopGridSupported((integer)$voHeader->getDesktopGridSupported());
            }
        }
        return $voHeader;
    }

    /**
     * set Header
     */
    public function setVoHeader(VoHeader $voHeader){
        $voHeader = $this->setTypageMiddleWare($voHeader, "integer");
        if ($voHeader->getStatusId() == null) {
            $voHeader->setStatusId(1);
        }

        $voHeader->setDisciplineId(0);
        if ($voHeader->getAlias() === null) {
            $voHeader->setAlias($voHeader->getName());
        }

        if ($voHeader->getEnrollmentUrl() == null) {
            $voHeader->setEnrollmentUrl("");
        }

        if ($voHeader->getSupportProcedureUrl() == null) {
            $voHeader->setSupportProcedureUrl("");
        }

        if ($voHeader->getValidationDate() == null) {
            $voHeader->setValidationDate(new DateTime("0000-00-00 00:00:00"));
        }

        $voHeader->setInsertDate(new DateTime());
        $voHeader->setGridId("");
        $voHeader->setSerial(0);
        $voHeader->setValidated(0);
        $voHeader->setRejectReason("");
        return $voHeader;
    }

    /**
     * set type of middleware
     */
    public function setNotifySites($entity)
    {
        $entity->setNotifySites((bool)$entity->getNotifySites());
        return $entity;
    }


    /**
     * @param VoHeader $voHeader
     * @param Form $headerForm
     * @return Form
     */
    public function addAupFields(VoHeader $voHeader, Form $headerForm)
    {
        $aupFileHandler = new AUPFileHandler($voHeader->getName(), $this->container->getParameter('aupUrl'));

        $aupFileList = array();
        if ($aupFileHandler->getFileNames()) {
            $aupFileList = $aupFileHandler->getFileNames();
        }


        switch ($voHeader->getAupType()) {
            case "text":
                $headerForm->get('VoHeader')->get('aup_type')->setData("Text"); // set default value

                $headerForm->get('VoHeader')->get('aupText')->setData($voHeader->getAup()); // set default value
                // create aupFile field
                $headerForm->get('VoHeader')->add('aupFile', ChoiceType::class, array(
                    'attr' => array(
                        'class' => 'form-control input-sm'
                    ),
                    'choices' => $aupFileList,
                    'required' => false,
                    'mapped' => false
                ));
                break;
            case "url":
                $headerForm->get('VoHeader')->get('aup_type')->setData("Url"); // set default value

                $headerForm->get('VoHeader')->get('aupUrl')->setData($voHeader->getAup()); // set default value
                // create aupFile field
                $headerForm->get('VoHeader')->add('aupFile', ChoiceType::class, array(
                    'attr' => array(
                        'class' => 'form-control input-sm'
                    ),
                    'choices' => $aupFileList,
                    'required' => false,
                    'mapped' => false
                ));
                break;
            case "file":
                $headerForm->get('VoHeader')->get('aup_type')->setData("File"); // set default value

                // create aupFile field and set default value
                $headerForm->get('VoHeader')->add('aupFile', ChoiceType::class, array(
                    'attr' => array(
                        'class' => 'form-control input-sm'
                    ),
                    'choices' => $aupFileList,
                    'required' => false,
                    'mapped' => false,
                    'choice_value' => $voHeader->getAup()
                ));
                break;
            default:

                $headerForm->get('VoHeader')->get('aup_type')->setData("Text"); // set default value

                $headerForm->get('VoHeader')->get('aupText')->setData($voHeader->getAup()); // set default value
                // create aupFile field
                $headerForm->get('VoHeader')->add('aupFile', ChoiceType::class, array(
                    'attr' => array(
                        'class' => 'form-control input-sm'
                    ),
                    'choices' => $aupFileList,
                    'required' => false,
                    'mapped' => false
                ));
                break;
        }


        return $headerForm;
    }

    /**
     * delete a discipline
     */
    public function deleteDiscipline()
    {
        try {
            $em = $this->container->get('doctrine')->getManager();
            $disciplines = $this->container->get('doctrine')->getRepository('AppBundle:VO\VoDisciplines')->findBy(array("vo_id" => $this->vo->getSerial()));
            foreach ($disciplines as $discipline) {
                $em->remove($discipline);
            }
            $em->flush();
            return "OK";
        //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        //@codeCoverageIgnoreEnd
    }


    /*
     * @param array $listDisiplines
     */
    public function saveDiscipline($listDiscplines)
    {
        try {
            $em = $this->container->get('doctrine')->getManager();
            foreach ($listDiscplines as $disciplineId => $disciplineLabel) {
                $discipline = new VoDisciplines();
                $discipline->setVoId($this->vo->getSerial());
                $discipline->setDisciplineId($disciplineId);

                $em->persist($discipline);
            }

            $em->flush();
            return "OK";
        //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        //@codeCoverageIgnoreEnd
     }

    /**
     * update scope in voHeader
     * @param $scopeId
     *
     */
    public function updateScope($scopeId)
    {
        $status = "Scope updated";
        try {
            $this->voHeader->setScopeId($scopeId);
            $em = $this->container->get('doctrine')->getManager();
            $em->persist($this->voHeader);
            $em->flush();
        //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $status = $e->getCode() . " - " . $e->getMessage();
        }
        //@codeCoverageIgnoreEnd

        return $status;
    }

    /**
     * update status in voHeader
     * @param $tatusId
     *
     */
    public function updateStatus($statusId)
    {
        $status = "Status updated";
        try {
            $this->voHeader->setStatusId($statusId);
            $em = $this->container->get('doctrine')->getManager();
            $em->persist($this->voHeader);
            $em->flush();
        //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $status = $e->getCode() . " - " . $e->getMessage();
        }
        //@codeCoverageIgnoreEnd

        return $status;
    }

    /**
     * update status to prod in voHeader
     * update validation date in vo and voHeader
     * @param $statusId
     * @param $scopeId
     * @return string
     */
    public function updateVOToProd($statusId)
    {
        date_default_timezone_set('UTC');
        $date = new \DateTime(date('Y-m-d H:i:s')); // current date time
        date_default_timezone_set('Europe/Paris');

        $status = "Status updated";

        try {
            //update vo header
            $this->voHeader->setStatusId($statusId);
            $this->voHeader->setValidated("1");
            $this->voHeader->setValidationDate($date);

            $em = $this->container->get('doctrine')->getManager();
            $em->persist($this->voHeader);

            //update vo
            $this->vo->setValidationDate($date);

            $em->persist($this->vo);

            $em->flush();
        //@codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $status = $e->getCode() . " - " . $e->getMessage();
        }
        //@codeCoverageIgnoreEnd

        return $status;
    }


    /**
     * get manager information for a vo
     * @return array
     */
    public function getVoManageInfo()
    {

        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select("vchp.serial, vc.first_name, vc.last_name, vc.email, vc.dn")
            ->from("AppBundle:VO\VoContacts", "vc")
            ->innerJoin("AppBundle:VO\VoContactHasProfile", 'vchp', "WITH", "vchp.contact_id = vc.id")
            ->innerJoin("AppBundle:VO\VoUserProfile", "vup", "WITH", "vup.id = vchp.user_profile_id")
            ->where('vchp.serial = :serial')
            ->andWhere('vup.profile = :voprofile')
            ->setParameter("voprofile", "VO MANAGER")
            ->setParameter("serial", $this->vo->getSerial());

        $query = $qb->getQuery();
        $managers = $query->getResult();

        $voManager = $managers[0];

        return array("manager" => $voManager["first_name"] . " " . $voManager["last_name"],
            "manager_email" => $voManager['email']);
    }


    /**
     * find a manageer by vo serial
     * @return mixed
     */
    public function getManagerBySerial()
    {
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('c as VoContacts')
            ->from("AppBundle:VO\VoContacts", "c")
            ->innerJoin("AppBundle:VO\VoContactHasProfile", 'v', "WITH", "v.contact_id = c.id")
            ->innerJoin("AppBundle:VO\VoUserProfile", "p", "WITH", "p.id = v.user_profile_id")
            ->where('v.serial = :serial')
            ->andWhere('p.profile = :profile')
            ->setParameter('profile', 'VO MANAGER')
            ->setParameter("serial", $this->vo->getSerial())
            ->distinct("c");


        $query = $qb->getQuery();
        $managers = $query->getArrayResult();

        return $managers;
    }

    /**
     * get list of ngi managers
     * @return mixed
     */
    public function getNGIManagers()
    {
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('v.serial, vh.id, vs.roc, vh.scope_id')
            ->from("AppBundle:VO\Vo", "v")
            ->innerJoin("AppBundle:VO\VoHeader", 'vh', "WITH", "vh.id = v.header_id")
            ->innerJoin("AppBundle:VO\VoScope", "vs", "WITH", "vh.scope_id = vs.id")
            ->where('v.serial = :serial')
            ->setParameter("serial", $this->vo->getSerial());

        $query = $qb->getQuery();


        $result = $query->getSingleResult();


        $rocScope = $result['roc'];

        return $rocScope;
    }


    /**
     * get middleware list for a voheader
     * @return array
     */
    public function getMiddleWareList()
    {

        $middleWaresList = array();

        if ($this->voHeader->getArcSupported() == 1) {
            $middleWaresList[] = 'Arc';
        }

        if ($this->voHeader->getGliteSupported() == 1) {
            $middleWaresList[] = 'gLite';
        }

        if ($this->voHeader->getUnicoreSupported() == 1) {
            $middleWaresList[] = 'Unicore';
        }

        if ($this->voHeader->getGlobusSupported() == 1) {
            $middleWaresList[] = 'Globus';
        }

        if ($this->voHeader->getCloudComputingSupported() == 1) {
            $middleWaresList[] = 'Cloud Computing Resources';
        }

        if ($this->voHeader->getCloudStorageSupported() == 1) {
            $middleWaresList[] = 'Cloud Storage Resources';
        }


        if (count($middleWaresList) == 0) {
            $middleWaresList[] = 'N.A.';
        }

        return $middleWaresList;

    }

    /**
     * get securityMailingList and voMangaer by VO
     */
    public function getVoSecurityMailingList()
    {

        $securityList = $this->findSecurityMailingList();

        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('vchp.serial, vc.email')
            ->from("AppBundle:VO\VoContacts", "vc")
            ->innerJoin("AppBundle:VO\VoContactHasProfile", 'vchp', "WITH", "vchp.contact_id = vc.id")
            ->innerJoin('AppBundle:VO\VoUserProfile', 'vup', 'WITH', 'vup.id = vchp.user_profile_id')
            ->where('vup.profile = :profile')
            ->setParameter('profile', 'VO MANAGER');


        $query = $qb->getQuery();

        $managers = $query->getArrayResult();

        foreach ($securityList as $key => $security) {
            foreach ($managers as $manager) {
                if ($securityList[$key]['serial'] == $manager['serial']) {
                    if (empty($securityList[$key]['contact_manager'])) {
                        $securityList[$key]['contact_manager'] = $manager['email'];
                    } else {
                        $securityList[$key]['contact_manager'] = $securityList[$key]['contact_manager'] . " , " . $manager['email'];
                    }
                }
            }
        }

        return $securityList;
    }

    /**
     * SecurityMailingList and voManager by vo
     */
    public function findSecurityMailingList()
    {
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('v.name ,vm.security_contact_mailing_list, vm.id,v.serial')
            ->from('AppBundle:VO\Vo', 'v')
            ->leftJoin("AppBundle:VO\VoMailingList", "vm", "WITH", "vm.serial = v.serial")
            ->leftJoin("AppBundle:VO\VoHeader", "vh", "WITH", "vh.id = v.header_id")
            ->where("vh.status_id = 2");


        $query = $qb->getQuery();

        return $query->getArrayResult();
    }

    /**
     *  Set default ressources values for registration
     */
    public function setRessources(VoRessources $voRessources)
    {
//        if ($voRessources->getCpuCore() === null){
//            $voRessources->setCpuCore(0);
//        }
//        if ($voRessources->getJobMaxCpu() === null){
//            $voRessources->setJobMaxCpu(0);
//        }
//        if ($voRessources->getJobScratchSpace() === null){
//            $voRessources->setJobScratchSpace(0);
//        }
//        if ($voRessources->getJobMaxWall() === null){
//            $voRessources->setJobMaxWall(0);
//        }
//        if ($voRessources->getPublicIp() === null){
//            $voRessources->setPublicIp(0);
//        }
//        if ($voRessources->getRam64() === null){
//            $voRessources->setRam64(0);
//        }
//        if ($voRessources->getRam386() === null){
//            $voRessources->setRam386(0);
//        }
//        if ($voRessources->getStorageSize() === null){
//            $voRessources->setStorageSize(0);
//        }
//        if ($voRessources->getVmRam() === null){
//            $voRessources->setVmRam(0);
//        }
//        if ($voRessources->getSerial() === null){
//            $voRessources->setSerial(0);
//        }
        if ($voRessources->getValidated() === null){
            $voRessources->setValidated(1);
        }
        if ($voRessources->getOtherRequirements() === null){
            $voRessources->setOtherRequirements("");
        }
        if ($voRessources->getInsertDate() === null){
            $voRessources->setInsertDate(new DateTime('now'));
        }
        if ($voRessources->getRejectReason() === null){
            $voRessources->setRejectReason("");
        }
//        if ($voRessources->getNumberCores() === null){
//            $voRessources->setNumberCores(0);
//        }
//        if ($voRessources->getScratchSpaceValues() === null){
//            $voRessources->setScratchSpaceValues(0);
//        }
//        if ($voRessources->getMinimumRam() === null){
//            $voRessources->setMinimumRam(0);
//        }

        if ($voRessources->getCvmfs() != null) {
            $tabEndpoints = explode(",",$voRessources->getCvmfs());
            $voRessources->setCvmfs(serialize($tabEndpoints));
        }
        return $voRessources;
    }

    /**
     * Set default mainling list values for registraiton
     */
    public function setMailingList(VoMailingList $voMailingList)
    {
        if ($voMailingList->getOperationsMailingList() === null){
            $voMailingList->setOperationsMailingList("");
        }
        if ($voMailingList->getUserSupportMailingList() === null){
            $voMailingList->setUserSupportMailingList("");
        }
        if ($voMailingList->getSerial() === null){
            $voMailingList->setSerial(0);
        }
        if ($voMailingList->getValidated() === null){
            $voMailingList->setValidated(1);
        }
        if ($voMailingList->getInsertDate() === null){
            $voMailingList->setInsertDate(new DateTime("0000-00-00 00:00:00"));
        }
        if ($voMailingList->getRejectReason() === null){
            $voMailingList->setRejectReason("");
        }


        return $voMailingList;
    }


    /**
     * find all vo names for vo with status production
     * @return array
     *
     */
    public function findAllVONames() {

        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('v.name')
            ->from('AppBundle:VO\Vo','v')
            ->leftJoin("AppBundle:VO\VoHeader", "vh", "WITH", "vh.id = v.header_id")
            ->where("vh.status_id = 2");

        $query = $qb->getQuery();

        $arrayReturn = array();

        foreach($query->getArrayResult() as $subarr) {
            foreach($subarr as $id => $value) {
                $arrayReturn[] = $value;
            }
        }

        return $arrayReturn;
    }

    /**
     * find robot certificate for a vo name
     */
    public function getVoRobot($voName, $rbDN) {

        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('vrc')
            ->from('AppBundle:VO\VoRobotCertificate','vrc')
            ->leftJoin("AppBundle:VO\Vo", "v", "WITH", "v.name = vrc.vo_name")
            ->where("v.name = :name")
            ->andWhere("vrc.robot_dn = :rbDN")
            ->setParameter("name", $voName)
            ->setParameter("rbDN", $rbDN);

        $query = $qb->getQuery();

        $arrayReturn = array();

        foreach($query->getArrayResult() as $subarr) {
            foreach($subarr as $id => $value) {
                $arrayReturn[$id] = $value;
            }
        }


        return $arrayReturn;
    }


    /**
     * list of nb users by disciplines (for metrics)
     * @param null $discipline
     * @return array
     */
    public function getNbUsersbyDiscipline($discipline=NULL)
    {

        $tab_vos = $this->getVosByDiscipline($discipline);

        $countVo = array();
        $tabvonames = array();

        $listvo = array();
        $tabusers = array();
        $tabtotalusers = array();


        foreach ($tab_vos as $vo) {
            if (!in_array($vo["name"], $tabvonames)) {
                array_push($tabvonames, $vo["name"]);
                $discipline = $vo["discipline_label"];
                $listvo[$discipline][] = $vo["name"];
                if (isset($countVo[$discipline]))
                    $countVo[$discipline]++;
                else
                    $countVo[$discipline] = 1;
            }
        }


        foreach ($listvo as $key => $value) {

            $results = self::getNbUsersMonth(NULL, NULL, $listvo[$key]);
            $tabusers[$key] = $results[0];
            $tabtotalusers[$key] = $results[1];

        }

        return (array($tabusers, $countVo, $tabtotalusers));
    }


    /**
     * get list of vo by discipline (for metrics)
     * @param null $discipline
     * @param bool|false $oldDiscipline
     * @return array
     */
    public function getVosByDiscipline($discipline = NULL, $oldDiscipline = false)
    {

        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('v.serial,v.name, d.discipline_label , vd.discipline_id')
            ->from("AppBundle:VO\Vo", "v")
            ->innerJoin("AppBundle:VO\VoDisciplines",  "vd", "WITH", "vd.vo_id = v.serial")
            ->innerJoin("AppBundle:VO\Disciplines", "d", "WITH", "vd.discipline_id = d.discipline_id");

        if ($oldDiscipline) {
            $qb->select('v.name,vd.discipline,vh.id')
                ->from("AppBundle:VO\Vo", "v")
                ->innerJoin("AppBundle:VO\VoHeader",  "vh", "WITH", "vh.serial = v.serial")
                ->innerJoin("AppBundle:VO\VoDiscipline",  "vd", "WITH", "vh.discipline_id = vd.id")
                ->where('vh.status_id= ', ':statusId')
                ->setParameter("statusId", "2");
        }


        if (isset($discipline)) {
            $qb->andWhere('vd.discipline_id=?', $discipline);
        }

        $query = $qb->getQuery();

        $arrayReturn = array();


        foreach($query->getArrayResult() as $subarr) {
            foreach($subarr as $id => $value) {
                $arrayReturn[$id] = $value;
            }
        }

        return $query->getArrayResult();
    }

    /**
     * get nb users of a vo by month (for metrics)
     * @param null $month
     * @param null $year
     * @param array|NULL $voList
     * @return array
     */
    public function getNbUsersMonth($month=NULL,$year=NULL,array $voList=NULL)
    {

        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

           $qb->select("vh")
            ->from("AppBundle:VO\VoUsersHistory", "vh")
            ->where("vh.u_year<> 2010")
            ->orderBy("vh.u_year desc,vh.u_month desc,vh.vo");

        if (isset($month)&&isset($year))
        {
            $qb->andWhere("vh.u_month=",":month");
            $qb->setParameter("month",$month);
            $qb->andWhere("vh.u_year=".$year);
        }

        $nbTotalbyVO=0;

        if (isset($voList))
        {
            $qb->andWhere("vh.vo IN (:volist)")
            ->setParameter("volist", array_values($voList));
            $nbTotalbyVO= $this->getUsersNumber($voList);

        }

        $query = $qb->getQuery();

        $arrayReturn = array();

        foreach($query->getArrayResult() as $subarr) {
            foreach($subarr as $id => $value) {
                $arrayReturn[$id] = $value;
            }
        }


        return (array($arrayReturn,$nbTotalbyVO));
    }


    /**
     * get number of users for a list of vo (for metrics)
     * @param array $voList
     * @param bool|false $datefixe
     * @return mixed
     */
    public function getUsersNumber(array $voList,$datefixe=false) {

        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();
        $qb->select('COUNT(DISTINCT v.uservo), v.vo')
            ->from('AppBundle:VO\VoUsers', 'v')
            ->groupBy('v.vo')
            ->Where('v.vo IN (:voList)')
            ->setParameter("voList", $voList);

        if ($datefixe)
        {
            $qb->andWhere('v.last_update>=',':lastUpdate');
            $qb->setParameter("lastUpdate",$datefixe);
            $qb->andWhere('v.first_update<=','firstUpdate');
            $qb->setParameter("lastUpdate",$datefixe);
        }
        else {
            $qb->andWhere('DATE_DIFF(CURRENT_DATE(),v.last_update)<5');
        }

        $arrayReturn = $qb->getQuery()->getArrayResult();


        return $arrayReturn;
    }


    /**
     * get number of vo between 2 given dates (for metrics)
     * @param $date
     * @param null $dateStart
     * @return array
     */
    public function getCountVo($date, $dateStart = null)
    {
        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

       $qb->select('COUNT(distinct v.serial) as NbVO')
        ->from('AppBundle:VO\Vo', 'v')
        ->where("v.validation_date <= '". trim($date)."'")
        ->andWhere('MONTH(v.validation_date)<>'. 0);

        if ($dateStart) {
            $qb->andWhere("v.validation_date >= '". trim($dateStart)."'");
        }

        $query = $qb->getQuery();

        $tabnbnewvos = array();

        foreach($query->getArrayResult() as $subarr) {
            foreach($subarr as $id => $value) {
                $tabnbnewvos[$id] = $value;
            }
        }

        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('COUNT(distinct v.serial) as NbVO')
        ->from('AppBundle:VO\VoHeader', 'vh')
        ->innerJoin('AppBundle:VO\Vo',' v ', 'WITH', 'vh.id = v.header_id')
            ->where("v.validation_date <= '". trim($date)."'")
        ->andWhere('MONTH(v.validation_date)<>'. 0)
        ->andWhere('vh.scope_id = '. 2);
        if ($dateStart) {
            $qb->andWhere("v.validation_date >= '". trim($dateStart)."'");
        }

        $query = $qb->getQuery();

        $tabnbnewvos_i = array();

        foreach($query->getArrayResult() as $subarr) {
            foreach($subarr as $id => $value) {
                $tabnbnewvos_i[$id] = $value;
            }
        }
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('COUNT(distinct v.serial) as NbVO')
            ->from('AppBundle:VO\VoHeader', 'vh')
            ->innerJoin('AppBundle:VO\Vo',' v ', 'WITH', 'vh.id = v.header_id')
            ->where("v.validation_date <= '". trim($date)."'")
            ->andWhere('MONTH(v.validation_date)<>'. 0)
            ->andWhere('vh.scope_id != '. 2);
        if ($dateStart) {
            $qb->andWhere("v.validation_date >= '". trim($dateStart)."'");
        }

        $query = $qb->getQuery();

        $tabnbnewvos_nat = array();

        foreach($query->getArrayResult() as $subarr) {
            foreach($subarr as $id => $value) {
                $tabnbnewvos_nat[$id] = $value;
            }
        }

        return array($tabnbnewvos["NbVO"], $tabnbnewvos_i["NbVO"], $tabnbnewvos_nat["NbVO"]);

    }


    /**
     *
     * get the created vo
     */
    public function getVoCreated($date_begin, $date_end)
    {

        /** @var  $qb  \Doctrine\ORM\QueryBuilder */
        $qb = $this->container->get("doctrine")->getManager()->createQueryBuilder();

        $qb->select('v.serial, v.name, v.validation_date,s.status,vs.scope')
            ->from('AppBundle:VO\Vo', 'v')
            ->innerJoin('AppBundle:VO\VoHeader',' vh', 'WITH', 'vh.id = v.header_id')
            ->innerJoin('AppBundle:VO\VoStatus',' s', 'WITH', 's.id = vh.status_id')
            ->innerJoin('AppBundle:VO\VoScope',' vs', 'WITH', 'vs.id = vh.scope_id')
            ->where("v.validation_date>= '".trim($date_begin)."'")
            ->andWhere("v.validation_date<='". $date_end. "'");

        $query = $qb->getQuery();


        return $query->getArrayResult();

    }

    /**
     * delete vo, and voHeader/voMailingList/VoResources related to
     * @param $voToDelete \AppBundle\Entity\VO\Vo
     */
    public function deleteVo() {

        try {

            if ($this->voYearlyValidation != null) {
                $this->em->remove($this->voYearlyValidation);
                $this->em->flush();
            }

            if ($this->voResources != null) {
                $this->em->remove($this->voResources);
                $this->em->flush();
            }

            if ($this->voMailingList != null) {
                $this->em->remove($this->voMailingList);
                $this->em->flush();
            }

            if ($this->voDisicplines != null) {
                $this->em->remove($this->voDisicplines);
                $this->em->flush();
            }

            if ($this->voHeader != null) {
                $this->em->remove($this->voHeader);
                $this->em->flush();
            }

            if ($this->vo != null) {
                $this->em->remove($this->vo);
                $this->em->flush();
            }

            return "OK";

        } catch (\Exception $e) {
             return $e->getMessage();
        }
    }

    /**
     * get Vo Manager global mail
     */
    public function getVoAdminsMailingList() {
        return $this->voMailingList->getAdminsMailingList();
    }


}

