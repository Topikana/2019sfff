<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 18/01/16
 * Time: 17:04
 */

namespace Tests\Model\VO;

use AppBundle\Entity\VO\VoHeader;
use AppBundle\Entity\VO\VoVomsServer;

use AppBundle\Model\VO\ModelVO;
use AppBundle\Entity\VO\Vo;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;




class ModelVOTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;


    private $container;

    public function __construct()
    {
        parent:: __construct();

        $kernel = new \AppKernel('dev', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->container = $kernel->getContainer();
    }


    /**
     * test general tab
     */
    public function testConstructVODetailGeneral() {

        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);

        //test content of array result
        $this->assertEquals(304,$modelVO->constructVODetailGeneral()["serial"], "The VO serial must be set [expected : 304]");
        $this->assertEquals("vo.cictest.fr",$modelVO->constructVODetailGeneral()["name"], "The VO name must be set [expected : vo.cictest.fr]");
        $this->assertEquals("National - NGI_France",$modelVO->constructVODetailGeneral()["scope"],  "The VO scope must be set [expected : National - NGI_France] instead ".$modelVO->constructVODetailGeneral()["scope"]);
        $this->assertEquals("Production",$modelVO->constructVODetailGeneral()["status"],  "The VO status must be set [expected : Production]");
        $this->assertNotNull($modelVO->constructVODetailGeneral()["validationDate"], "The VO validation date must be set");
        $this->assertEquals("http://vo.cictest.fr",$modelVO->constructVODetailGeneral()["enrollmentUrl"],  "The VO enrollment URL must be set [expected : http://vo.cictest.fr]");
        $this->assertEquals("http://vo.cictest.fr",$modelVO->constructVODetailGeneral()["homepageUrl"],  "The home page URL must be set [expected : http://vo.cictest.fr ]");
        $this->assertEquals("No", $modelVO->constructVODetailGeneral()["ggus"], "The VO ggus must be set [expected : NO]");
        $this->assertEquals("Yes (N.A)",$modelVO->constructVODetailGeneral()["voms"],  "The VO voms must be set [expected : Yes (N.A)]");
        $this->assertTrue(isset($modelVO->constructVODetailGeneral()["disciplines"]) && is_array($modelVO->constructVODetailGeneral()["disciplines"]));
        $this->assertTrue(isset($modelVO->constructVODetailGeneral()["supportedServices"]) && is_array($modelVO->constructVODetailGeneral()["supportedServices"]));
    }


    public function testsetLastChangeDate() {


        $modelVO = new ModelVO($this->container, 304);

       $this->assertTrue($modelVO->setLastChangeDate());

    }
    /**
     * test on description part
     */
    public function testContructVODetailDescription() {

        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);

        //test description text
        $this->assertNotNull($modelVO->constructVODetailDescription(), "The VO must have a description");

    }

    /**
     * test on AUP
     */
    public function testgetAUP() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 1);

        //test file format
        $this->assertTrue($modelVO->getAUP()["type"] ==  "url" || "file"  ||"text", "Wrong file type for VO AUP");

        //test if file is present
        $this->assertNotNull($modelVO->getAUP()["val"], "no file for VO AUP");

    }


    /**
     * test on acknowledgment part
     */
    public function testConstructVODetailAcknowledgments() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);

        //test that if acknowledgment is set, this will return an array
        $this->assertTrue($modelVO->constructVODetailAcknowledgments() == "No Acknowledgment Statement"
            ||  ($modelVO->constructVODetailAcknowledgments() != null && is_array($modelVO->constructVODetailAcknowledgments()))
            , "If there is acknowlegement statement, it must return an array, else a sentence..."
        );


        $modelVO = new ModelVO($this->container, 305);

        //test that if acknowledgment is set, this will return an array
        $this->assertTrue($modelVO->constructVODetailAcknowledgments() == "No Acknowledgment Statement"
            ||  ($modelVO->constructVODetailAcknowledgments() != null && is_array($modelVO->constructVODetailAcknowledgments()))
            , "If there is acknowlegement statement, it must return an array, else a sentence..."
        );
    }

    /**
     * test on resources part
     */
    public function  testconstructVODetailResources() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 1);

        $this->assertTrue(is_array($modelVO->constructVODetailResources()), "the resources info must be in an array");

        //for aegis on dev, no cloud data
//        $this->assertTrue(count(array_unique($modelVO->constructVODetailResources())) === 1, "All resources data == 0 for aegis in dev mode");

    }

    /**
     * test on cloud resources part
     */
    public function  testconstructVODetailCloud() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 1);

        $this->assertTrue(is_array($modelVO->constructVODetailCloud()), "the cloud resources info must be in an array");

        //for aegis on dev, no cloud data
//        $this->assertEquals(implode($modelVO->constructVODetailCloud()),"000", "There must be no cloud data for aegis in dev mode");

    }

    /**
     * test on other requirements part
     */
    public function testConstructVODetailOtherReq() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);
        //for aegis on dev, no other requirements

        $this->assertTrue($modelVO->constructVODetailOtherReq() == "" || $modelVO->constructVODetailOtherReq() != "" , "VO has no other requirements in dev mode");

    }


    /**
     * test on generic contacts part
     */
    public function testgetVOContacts() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);

        //there must be at least 1 contact
        $this->assertGreaterThanOrEqual(1,count($modelVO->getVOContacts()), "Error on getVoContact doctrine call");

    }


    /**
     * test on mailing list part
     */
    public function testConstructVODetailsMailingList() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);

        //there must be at least 1 contact by category
        $this->assertNotNull($modelVO->constructVODetailsMailingList());

        $this->assertNotNull($modelVO->constructVODetailsMailingList()["admins"], "there must be an admin mail");
        $this->assertNotNull($modelVO->constructVODetailsMailingList()["operations"], "there must be an operation mail");
        $this->assertNotNull($modelVO->constructVODetailsMailingList()["usersupport"], "there must be a user support mail");
        $this->assertNotNull($modelVO->constructVODetailsMailingList()["users"], "there must be a user mail");
        $this->assertNotNull($modelVO->constructVODetailsMailingList()["security"], "there must be a security mail");

    }

    /**
     * test on VOMS list part
     */
    public function testgetVOMSList() {
        //create fake voms server
        $voVomsServer = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => 304));

        if ($voVomsServer == null) {
            $voVomsServer = new VoVomsServer();
            $voVomsServer->setSerial(304);
            $voVomsServer->setHostname("cclcgvomsli01.in2p3.fr");
            $voVomsServer->setHttpsPort(8443);
            $voVomsServer->setVomsesPort(15010);
            $voVomsServer->setIsVomsadminServer(0);
            $voVomsServer->setMembersListUrl("https://cclcgvomsli01.in2p3.fr:8443/voms/cic.test.fr/services/VOMSAdmin?method=listMembers");

            try {
                $em = $this->container->get("doctrine")->getManager();
                $em->persist($voVomsServer);
                $em->flush();
                $em->refresh($voVomsServer);

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);

        $this->assertGreaterThanOrEqual(1,count($modelVO->getVOMSList()), "Error on getVOMSList doctrine call");

        try {
            $voVomsServer = $this->em->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => 304, "is_vomsadmin_server" => 0));
            if ($voVomsServer != null) {
                $em = $this->container->get("doctrine")->getManager();
                $em->remove($voVomsServer);
                $em->flush();
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * test on getVOMSGroup
     */
    public function testgetVOMSGroup() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);

        $this->assertGreaterThanOrEqual(1,count($modelVO->getVOMSGroup()), "Error on getVOMSGroup doctrine call");
    }

    /**
     * test on changing vo date validation
     */
    public function testSetVOValidation() {
        //initialise modelVO
        $modelVO = new ModelVO($this->container, 304);

        $this->assertEquals(1, $modelVO->setVoValidation()["isSuccess"], "setVoValidation - error on lavoisier call");
    }

    /**
     * test on getVoNameBySerial
     */
    public function testgetVoNameBySerial() {
        $modelVO = new ModelVO($this->container, null);

        $this->assertNotNull($modelVO->getVONameBySerial(304));

    }

    public function testgetVoByName() {
        $modelVO = new ModelVO($this->container, null);

        $this->assertNotNull($modelVO->getVoByName("vo.cictest.fr"));

    }

    /**
     * test on setTypageMiddlewrare
     */
    public function testsetTypageMiddleWare() {
        $modelVO = new ModelVO($this->container, null);

        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $modelVO->getVoByName("vo.cictest.fr");

        $voHeader = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoHeader")->findOneBy(array("id" => $vo->getHeaderId()));

        $this->assertNotNull($modelVO->setTypageMiddleWare($voHeader, "bool"));

        $this->assertNotNull($modelVO->setTypageMiddleWare($voHeader, "integer"));


    }

    /**
     * test on setvoheader
     */
    public function testSetVoHeader() {
        $modelVO = new ModelVO($this->container, null);

        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $modelVO->getVoByName("vo.cictest.fr");

        $voHeader = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoHeader")->findOneBy(array("id" => $vo->getHeaderId()));

        $this->assertNotNull($modelVO->setVoHeader($voHeader));
    }


    /**
     * test on deleteDiscipline
     */
    public function testdeleteDiscipline()
    {
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertEquals("OK", $modelVO->deleteDiscipline());
    }

    /**
     * test on saveDiscipline
     */
    public function testsaveDiscipline() {

        $arrayDisciplines = array(0 => "Unknown", 4 => "Computational Chemistry", 10 => "Fusion");
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertEquals("OK", $modelVO->saveDiscipline($arrayDisciplines));
    }

    /**
     * test on updateScope
     */
    public function testupdateScope() {

        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertEquals("Scope updated", $modelVO->updateScope(220));
    }


    /**
     * test on updateStatus
     */
    public function testupdateStatus() {

        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertEquals("Status updated", $modelVO->updateStatus(1));
    }

    /**
     * test on updateVOToProd
     */
    public function testupdateVOToProd() {
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertEquals("Status updated", $modelVO->updateStatus(2));
    }

    /**
     * test on getVoManageInfo
     */
    public function testgetVoManageInfo() {
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertNotNull($modelVO->getVoManageInfo(2)["manager"]);

        $this->assertNotNull($modelVO->getVoManageInfo(2)["manager_email"]);
    }

    /**
     * test on getManagerBySerial
     */
    public function testgetManagerBySerial(){
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertNotNull($modelVO->getManagerBySerial());
    }

    /**
     * test on getNGIManagers
     */
    public function testgetNGIManagers() {
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertNotNull($modelVO->getNGIManagers());
    }

    /**
     * test on getVoSecurityMailingList
     */
    public function testgetVoSecurityMailingList() {
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertNotNull($modelVO->getVoSecurityMailingList());
    }

    /**
     * test on findSecurityMailingList
     */
    public function testfindSecurityMailingList() {
        $modelVO = new ModelVO($this->container, null);

        $this->assertNotNull($modelVO->getVoSecurityMailingList());
    }

    /**
     * test on findAllVONames
     */
    public function testfindAllVONames() {
        $modelVO = new ModelVO($this->container, null);

        $this->assertNotNull($modelVO->findAllVONames());
    }

    /**
     * test on getVoRobot
     */
    public function testgetVoRobot() {
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "biomed"));


        $modelVO = new ModelVO($this->container, null);

        $this->assertNotNull($modelVO->getVoRobot($vo->getName(), "/O=GRID-FR/C=FR/O=CNRS/OU=I3S/CN=Robot: VAPOR - Franck Michel"));
    }

    /**
     * test on getNbUsersbyDiscipline
     */
    public function testGetNbUsersbyDiscipline() {
        $modelVO = new ModelVO($this->container, null);
        $this->assertNotNull($modelVO->getNbUsersbyDiscipline());

    }

    /**
     * test on getVosByDiscipline
     */
    public function testGetVosByDiscipline() {
        $modelVO = new ModelVO($this->container, null);
        $this->assertNotNull($modelVO->getVosByDiscipline());

    }

    /**
     * test on getNbUsersMonth
     */
    public function testGetNbUsersMonth() {
        $modelVO = new ModelVO($this->container, null);
        $this->assertNotNull($modelVO->getNbUsersMonth());
    }


    /**
     * test on getCountVo
     */
    public function testGetCountVo() {
        $modelVO = new ModelVO($this->container, null);
        $this->assertNotNull($modelVO->getCountVo(date('Y-m-d')));
    }

    /**
     * test on getVoCreated
     */
    public function testGetVoCreated() {
        $date =  strtotime('today UTC');
        $dateBefore = date("Y-m-d", strtotime("+1 month", $date));
        $modelVO = new ModelVO($this->container, null);
        $this->assertNotNull($modelVO->getVoCreated($dateBefore, $date));
    }

    /**
     * test on getVoAdminsMailingList
     */
    public function testGetVoAdminsMailingList() {
        /** @var  $vo \AppBundle\Entity\VO\Vo */
        $vo = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $modelVO = new ModelVO($this->container, $vo->getSerial());

        $this->assertNotNull($modelVO->getVoAdminsMailingList());

    }
    /*
     * loop in an array an check for a value
     */
    private  function in_array_multi($array, $key, $val) {
        foreach ($array as $item)
        {
            if (is_array($item))
            {
                $this->in_array_multi($item, $key, $val);
            }

            if (isset($item[$key]) && $item[$key] == $val) return true;
        }

        return false;
    }
}