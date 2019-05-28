<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 15/01/16
 * Time: 09:52
 */

namespace Tests\AppBundle\Controller\VO;

use AppBundle\Entity\User;
use AppBundle\Entity\VO\Vo;

use AppBundle\Entity\VO\VoContactHasProfile;
use AppBundle\Entity\VO\VoContacts;

use AppBundle\Entity\VO\VoVomsGroup;
use AppBundle\Entity\VO\VoVomsServer;



use AppBundle\Form\VO\VoType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator;


class VOControllerTest extends WebTestCase
{


    /**
     * @var $container \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var $crawler \Symfony\Component\DomCrawler\Crawler
     */
    private $crawler;

    /**
     * @var $client \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @var $voTest \AppBundle\Entity\VO\Vo
     */
    private $voTest;


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \AppBundle\Entity\User
     */
    private $user;

    /**
     * @var $voRep
     */
    private $voRep;


    private $arrayHeaderMyVO;

    private $arrayHeaderOtherVO;

    private $arrayIncomingVO;

    private $arrayLeavingVO;

    private $arrayBadVO;

    private $arraySecurity;


    /**ok useable
     *
     */
    public function setUp()
    {
        self::bootKernel();
        $this->client = static::createClient(array(), array('HTTPS' => true));


        $kernel = new \AppKernel('dev', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->container = $kernel->getContainer();
        $this->container->get('security.token_storage')->setToken(null);


        $this->voTest = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

        $this->voRep = $this->em->getRepository("AppBundle:VO\Vo");


        // set up array
        $this->arrayHeaderMyVO = array(
            "name" => "VO",
            "last_change" => "Last update",
            "date_validation" => "Last validation date",
            "date_last_email_sending" => "Last email sending",
            "serial" => "Serial",
            "status" => "Status");


        $this->arrayHeaderOtherVO = array(
            "name" => "Name",
            "scope" => "Scope",
            "disciplines" => "Discipline",
            "members" => "Active Users",
            "membersTotal" => "Total Users",
            "middlewares" => "Middleware(s)",
            "serial" => "Serial",
            "status" => "Status");

        $this->arrayIncomingVO = array(
            "serial" => "Serial",
            "name" => "Name",
            "ggus_tickert_id" => "VO (2)",
            "need_ggus_support" => "VO SU (3)",
            "need_voms_help" => "Need VOMS Support (0)",
            "voms_ticket_id" => "VOMS (1)",
            "creation_date" => "Creation",
            "scope" => "Scope",
            "status" => "Status"
        );

        $this->arrayLeavingVO = array(
            "serial" => "Serial",
            "disciplines" => "Discipline",
            "scope" => "Scope",
            "middlewares" => "Middleware(s)",
            "members" => "Active Users",
            "membersTotal" => "Total Users",
            "status" => "Status"
        );

        $this->arrayBadVO = array(
            "Vo name",
            "Serial",
            "Admins",
            "AUP",
            "Description",
            "Homepage URL",
            "User Support",
            "Nb Voms Server",
            "Voms Users",
            "Score (%)",
            "Details",
            "Last report",
            "Submit report"
        );


        $this->arraySecurity = array(
            "Vo Name",
            "Security Contacts",
            "Contacts Managers",
            "Action"
        );

    }


    /**
     * ok useable
     * log in as simple fake user
     */
    private function logInSimpleUser()
    {
        $this->user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(41);

        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $attributes = [];

        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $this->user);


        $session->set('_security_'.$firewall, serialize($token));
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }


    private function logInIsSuUser()
    {
        $this->user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(325);

        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $attributes = [];

        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $this->user);


        $session->set('_security_'.$firewall, serialize($token));
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * ok useable
     * log in as simple fake su user
     */
    private function logInSUUser()
    {
        $this->user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(1258);

        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $attributes = [];

        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $this->user);


        $session->set('_security_'.$firewall, serialize($token));
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }


    /**
     * ok useable
     * log in as simple fake su user
     */
    private function logInSecurityUser()
    {
        $this->user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(1258); #943

        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $attributes = [];

        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $this->user);


        $session->set('_security_'.$firewall, serialize($token));
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }





//    /**
//     * log in as cyril
//     */
//    private function logInCyril()
//    {
//        $this->user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(16);
//
//        $session = $this->client->getContainer()->get('session');
//
//        $firewall = 'secured_area';
//        $attributes = [];
//
//        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $this->user);
//
//
//        $session->set('_security_'.$firewall, serialize($token));
//        $this->client->getContainer()->get('security.token_storage')->setToken($token);
//        $session->save();
//
//        $cookie = new Cookie($session->getName(), $session->getId());
//        $this->client->getCookieJar()->set($cookie);
//    }


//    /**
//     * test on registerUpdate
//     */
//    public function testRegisterUpdateAction()
//    {
//
//        $this->logInSUUser();
//
//        $this->crawler = $this->client->request('GET', '/vo/');
//
//
//        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//
//
//        //control that the controller method called is vaporHomeAction
//        $this->assertEquals('AppBundle\Controller\VO\VOController::registerUpdateAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is NOT registerUpdateAction !! ");
//
//
//        //test that there is no VO parameter in VO home
//        $this->assertEmpty($this->client->getRequest()->attributes->get('_route_params'), "there are unauthorized parameters in voList page !! ");
//
//
//        //----------------------------------------------------------//
//        // HTML COMPONENTS
//        //Table Incoming VO(s)
//        $this->assertLessThanOrEqual(1, $this->crawler->filter("#waitingVoListTable")->count(), "There must be 0 or 1 table 'Incoming VO(s)' in the view voList !!");
//
//        if ($this->crawler->filter("#waitingVoListTable")->count() == 1) {
//            foreach ($this->arrayIncomingVO as $key => $value) {
//                $this->assertTrue($this->crawler->filter("#waitingVoListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view VoWaitingList has been changed... ['" . $key . "'] is no more present...");
//            }
//        }
//        // Table Leaving VO(s)
//        $this->assertLessThanOrEqual(1, $this->crawler->filter("#leavingVoListTable")->count(), "There is no table 'MyVO' in the view voList !!");
//
//        if ($this->crawler->filter("#leavingVoListTable")->count() == 1) {
//            foreach ($this->arrayLeavingVO as $key => $value) {
//                $this->assertTrue($this->crawler->filter("#leavingVoListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view voRoles has been changed... ['" . $key . "'] is no more present...");
//            }
//        }
//
//        //Table MYVoListTable VO
//
////        $this->assertEquals(1, $this->crawler->filter("#myVoListTable")->count(), "There is no table 'MyVO' in the view voList !!");
////
////        foreach ($this->arrayHeaderMyVO as $key => $value) {
////            $this->assertTrue($this->crawler->filter("#myVoListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view voRoles has been changed... ['" . $key . "'] is no more present...");
////        }
//
//        $this->assertEquals(1, $this->crawler->filter("#headingMyVO")->count(), "There is no collapse 'MyVO' in the view voList !!");
//
//        //Table OTHER VO
//        $this->assertEquals(1, $this->crawler->filter("#collapseVoListOther")->count(), "There is no 'Other VO' part on voList page");
//
//
//        $this->assertEquals(1, $this->crawler->filter("#otherVoListTable")->count(), "There is no table 'MyVO' in the view voList !!");
//
//        foreach ($this->arrayHeaderOtherVO as $key => $value) {
//            $this->assertTrue($this->crawler->filter("#otherVoListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view voEntries has been changed... ['" . $key . "'] is no more present...");
//        }
//
//        //Table vo invalidity
//        $this->assertEquals(1, $this->crawler->filter("#headingVoValidity")->count(), "There is no 'Other VO' part on voList page");
//
//    }

    /**
     * add useable
     *
     * test on voDetailPermalinkAction
     */
    public function testVoDetailPermalinkAction() {


        //missing vo name parameter
        $this->crawler = $this->client->request('GET', '/vo/view/voname');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());


        //with wrong vo name parameter
        $this->crawler = $this->client->request('GET', '/vo/view/voname/toto');

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::voDetailPermalinkAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voDetailPermalinkAction !! ");

        $this->assertContains("Unknown vo name", $this->crawler->filter(".alert-danger")->text(), "this vo doesn't exist...");


        //with right vo name parameter
        $this->crawler = $this->client->request('GET', '/vo/view/voname/'. $this->voTest->getName());

        //control that the controller method called is voDetailPermalinkAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::voDetailPermalinkAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voDetailPermalinkAction !! ");

        $this->assertContains("VO Id Card", $this->crawler->filterXPath("//h1")->text(), "the vo detail permalink page hasn't been reached...");

        $this->assertGreaterThanOrEqual(10, $this->crawler->filter(".detailsVOTitle")->count(), "Missing part in vo detail permalink page");
    }


    /**
     * add useable
     */
    public function testVoSearchAction()
    {
        $this->crawler = $this->client->request('GET', '/vo/search');
        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::voSearchAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voSearchAction !! ");


        $this->assertEquals(1, $this->crawler->filter("#otherVoListTable")->count(), "There is no table 'MyVO' in the view voList !!");

        foreach ($this->arrayHeaderOtherVO as $key => $value) {
            $this->assertTrue($this->crawler->filter("#otherVoListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view voEntries has been changed... ['" . $key . "'] is no more present...");
        }


        $this->logInSimpleUser();
        $this->crawler = $this->client->request('GET', '/vo/search');
        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::voSearchAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voSearchAction !! ");


        $this->assertEquals(1, $this->crawler->filter("#otherVoListTable")->count(), "There is no table 'MyVO' in the view voList !!");

        foreach ($this->arrayHeaderOtherVO as $key => $value) {
            $this->assertTrue($this->crawler->filter("#otherVoListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view voEntries has been changed... ['" . $key . "'] is no more present...");
        }
    }

    /**
     * add useable
     *
     * test on update for a scope of a waiting vo
     */
    public function testUpdateScopeWaitingVOAction()
    {
        $this->logInSimpleUser();

        $this->crawler = $this->client->request('POST', '/vo/updateScope');

        $this->assertEquals(500, $this->client->getResponse()->getStatusCode(), "No parameter >>> request must fail");

        $this->crawler = $this->client->request('POST', '/vo/updateScope', array("serial" => $this->voTest->getSerial()));

        $this->assertEquals(500, $this->client->getResponse()->getStatusCode(), "No scope parameter >>> request must fail");


        $this->crawler = $this->client->request('POST', '/vo/updateScope', array("scopeId" => 15));

        $this->assertEquals(500, $this->client->getResponse()->getStatusCode(), "No serial parameter >>> request must fail");


        $this->crawler = $this->client->request('POST', '/vo/updateScope', array("serial" => $this->voTest->getSerial(), "scopeId" => 15));

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::updateScopeWaitingVOAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT updateScopeWaitingVOAction !! ");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "2nd scope update failed...");

        $this->assertNotNull(json_decode($this->client->getResponse()->getContent(), true)["status"], "response message must not been null...");


            // On double le test pour revenir sur le scope NGI_FRANCE
        $this->crawler = $this->client->request('POST', '/vo/updateScope', array("serial" => $this->voTest->getSerial(), "scopeId" => 220));

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::updateScopeWaitingVOAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT updateScopeWaitingVOAction !! ");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "scope update failed...");

        $this->assertNotNull(json_decode($this->client->getResponse()->getContent(), true)["status"], "response message must not been null...");


        //unexistant scope id
        $this->crawler = $this->client->request('POST', '/vo/updateScope', array("scopeId" => 99999));

        $this->assertEquals(500, $this->client->getResponse()->getStatusCode(), "No serial parameter >>> request must fail");


    }

    /**
     * add ok
     * test on rejection of vo
     */
    public function testRejectVoAction() {

        $this->logInSimpleUser();

        //test with vo null
        $this->crawler = $this->client->request('POST', '/vo/rejectVo',
            array("serial" => null,
                "cause" => "This is a unit test")
        );
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode(), "No vo parameter >>> request must fail");

        //test with cause null
        $this->crawler = $this->client->request('POST', '/vo/rejectVo',
            array("serial" => $this->voTest->getSerial(),
                "cause" => null)
        );
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode(), "No cause parameter >>> request must fail");

        //test with all parameters
        $this->crawler = $this->client->request('POST', '/vo/rejectVo',
            array("serial" => $this->voTest->getSerial(),
                "cause" => "This is a unit test")
        );

        $this->assertEquals("OK", $this->client->getResponse()->getContent());

        //test that flash message has been set and is OK
        $session = $this->client->getContainer()->get('session');
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Form submit has failed....");
        $this->assertContains('Registration of this VO has been rejected', $flash[0], "A flash message must have been set in session...");
    }

    /**
     * add useable
     *
     * test on status update for a vo
     */
    public function testUpdateStatusVoAction()
    {
        $this->logInIsSuUser();

        // no parameter
   //     $this->crawler = $this->client->request('POST', '/vo/updateStatusVo');
    //    $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), "No parameter >>> request must fail");

        // no scope parameter
        $this->crawler = $this->client->request('POST', '/vo/updateStatusVo/'.$this->voTest->getSerial());
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), "No scope parameter >>> request must fail");

        //no serial paramater
        $this->crawler = $this->client->request('POST', '/vo/updateStatusVo/2');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), "No serial parameter >>> request must fail");

        //all parameters
        $this->crawler = $this->client->request('POST', '/vo/updateStatusVo/'.$this->voTest->getSerial().'/2');

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::updateStatusVoAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT updateStatusVoAction !! ");

        $this->assertEquals(302,$this->client->getResponse()->getStatusCode(), "status update failed...");


        $this->crawler = $this->client->followRedirect();

        $this->assertEquals('AppBundle\Controller\VO\VOController::registerUpdateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT registerUpdateAction !! ");//

    }


    /**
     * test on set vo to prod
     * //!\\ GGUS TICKET PROCESS NOT TESTED
     */
    public function testSetVoToProductionAction()
    {

        $this->logInIsSuUser();
        $this->crawler = $this->client->request('POST', '/vo/setVoToProduction/'.$this->voTest->getSerial().'/2');
        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::setVoToProductionAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT setVoToProductionAction !! ");


        // Form redirectes
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Form submit has failed....");
        $this->assertContains('Status updated ', $flash[0], "A flash message must have been set in session...");

        $this->assertTrue($this->client->getResponse()->isRedirect(), "set vo to prod has failed ...");

        $this->client->followRedirect();

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::registerUpdateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT registerUpdateAction !! ");
    }

    /**ok  useable
     * Test new registration vo
     */
    public function testRegistrationAUPText()
    {
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/registration');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::registrationAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not registrationAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //test presence of vo form
        $this->assertEquals($this->crawler->filter("#registration")->count(), 1);

        $this->assertEquals($this->crawler->filter("#voForm")->count(), 1);

        $this->assertEquals(1,$this->crawler->filter("#VOH_section")->count());
        $this->assertEquals(1,$this->crawler->filter("#VOAS_section")->count());
        $this->assertEquals(1,$this->crawler->filter("#VOD_section")->count());
        $this->assertEquals(1,$this->crawler->filter("#VI_section")->count());
        $this->assertEquals(1,$this->crawler->filter("#VOR_section")->count());
        $this->assertEquals(1,$this->crawler->filter("#VM_section")->count());
        $this->assertEquals(1,$this->crawler->filter("#VOGC_section")->count());


        //complete vo form déjà grisé
        //AUP TYPE TEXT
//        $voForm = $this->crawler->filter("#voForm")->form();


//
//
//        $voForm["vo[VoHeader][name]"] = "laure3.vo.test.fr";
//        $voForm["vo[VoHeader][vo_scope]"] = "2";
//        $voForm["vo[VoHeader][scope_id]"] = "2";
//        $voForm["vo[VoHeader][homepage_url]"] = "http://vo.laure3.fr";
//        $voForm["vo[VoHeader][arc_supported]"] = "1";
//        $voForm["vo[VoHeader][description]"] = "this is a test vo made for test";
//
//        $voForm["vo[VoHeader][aup_type]"] = "Text";
//        $voForm["vo[VoHeader][aup]"] = "This Acceptable Use Policy applies to all members of [VoName] Virtual Organisation,  hereafter referred to as the VO, with reference to use of the EGI Infrastructure.  The [owner body] owns and gives authority to this policy.  Goal and description of the [VONAME] VO  ---------------------------------------------------------------------  [TO be completed]  Members and Managers of the VO agree to be bound by the Acceptable Usage Rules, VO Security Policy and other relevant EGI Policies, and to use the Infrastructure only in the furtherance of the stated goal of the VO.";
//
//        $voForm["vo[VoAcknowledgmentStatements][suggested]"] = "This work benefited from services provided by the laure3.vo.test.fr Virtual Organisation, supported by the national resource providers of the EGI Federation";
//
//        $voForm["voVomsRegistration[voms_need]"] = "1";

//        $voForm["vo[VoContacts][first_name]"] = "Laure";
//        $voForm["vo[VoContacts][last_name]"] = "Souai";
//        $voForm["vo[VoContacts][dn]"] = "/C=FR/O=CNRS/OU=USR6402/CN=Laure Souai/emailAddress=laure.souai@cc.in2p3.fr";
//        $voForm["vo[VoContacts][email]"] = "laure.souai@cc.in2p3.fr";
//
//        $voForm["vo[VoMailingList][admins_mailing_list]"] = "laure.souai@cc.in2p3.fr";
//        $voForm["vo[VoMailingList][users_mailing_list]"] = "laure.souai@cc.in2p3.fr";
//        $voForm["vo[VoMailingList][security_contact_mailing_list]"] = "laure.souai@cc.in2p3.fr";
//        $voForm["vo[VoMailingList][operations_mailing_list]"] = "laure.souai@cc.in2p3.fr";


//        $voForm["vo[_token]"] = $this->token;
//
//        $this->crawler = $this->client->submit($voForm);
//

//        $this->assertEquals(1, $this->crawler->filter("#registrationComplete")->count(), "Registration failed...");


        //delete after creation
        /** @var $voToDelete Vo */
//        $voToDelete = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "laure3.vo.test.fr"));
//        $voHeaderToDelete = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoHeader")->findOneBy(array("id" => $voToDelete->getHeaderId()));
//        $voVomsServerToDelete = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => $voToDelete->getSerial()));
//        $voMailListToDelete = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoMailingList")->findOneBy(array("serial" => $voToDelete->getMailingListId()));
//        $voResourcesToDelete = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoRessources")->findOneBy(array("id" => $voToDelete->getRessourcesId()));
//
//        try {
//            /** @var $em EntityManager */
//            $em = $this->container->get("doctrine")->getManager();
//            $em->remove($voMailListToDelete);
//            $em->remove($voVomsServerToDelete);
//            $em->remove($voResourcesToDelete);
//            $em->remove($voHeaderToDelete);
//            $em->remove($voToDelete);
//            $em->flush();
//
//        } catch (\Exception $e) {
//            echo $e->getMessage();
//        }
    }
//
//
    /**
    *ok useable
   * Test new registration vo
   */
    public function testRegistrationAUPFile()
    {
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/registration');


        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::registrationAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not registrationAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //test presence of vo form
        $this->assertEquals($this->crawler->filter("#registration")->count(), 1);

        $this->assertEquals($this->crawler->filter("#voForm")->count(), 1);

        $this->assertEquals($this->crawler->filter("#VOH_section")->count(), 1);
        $this->assertEquals($this->crawler->filter("#VOAS_section")->count(), 1);
        $this->assertEquals($this->crawler->filter("#VOD_section")->count(), 1);
        $this->assertEquals($this->crawler->filter("#VI_section")->count(), 1);
        $this->assertEquals($this->crawler->filter("#VOR_section")->count(), 1);
        $this->assertEquals($this->crawler->filter("#VM_section")->count(), 1);
        $this->assertEquals($this->crawler->filter("#VOGC_section")->count(), 1);


////        //complete vo form  déjà grisé
////        //AUP TYPE File
////        $voForm = $this->crawler->selectButton("Save")->form();
////
////        $voForm["vo[VoHeader][name]"] = "laure3.vo.test.fr";
////        $voForm["vo[VoHeader][vo_scope]"] = "2";
////        $voForm["vo[VoHeader][scope_id]"] = "2";
////        $voForm["vo[VoHeader][homepage_url]"] = "http://vo.laure3.fr";
////        $voForm["vo[VoHeader][arc_supported]"] = "1";
////        $voForm["vo[VoHeader][description]"] = "this is a test vo made for test";
////
////        $voForm["vo[VoHeader][aup_type]"] = "File";
////
////        $voForm["vo[VoHeader][aupFile][aupFile]"] =  new UploadedFile(
////            '../../Ressources/vo/AUP/aegis-AcceptableUsePolicy-20160129-1454076165609.xml',
////            'aegis-AcceptableUsePolicy-20160129-1454076165609.xml',
////            'text/xml',
////            3314,
////            0);
////        $voForm["vo[VoHeader][aupFile][name]"] = "laure3.vo.test.fr-AcceptableUsePolicy-20160425-1461596637233.xml";
////
////        $voForm["vo[VoAcknowledgmentStatements][suggested]"] = "This work benefited from services provided by the laure3.vo.test.fr Virtual Organisation, supported by the national resource providers of the EGI Federation";
////
////        $voForm["voVomsRegistration[voms_need]"] = "1";
////
////        $voForm["vo[VoContacts][first_name]"] = "Laure";
////        $voForm["vo[VoContacts][last_name]"] = "Souai";
////        $voForm["vo[VoContacts][dn]"] = "/C=FR/O=CNRS/OU=USR6402/CN=Laure Souai/emailAddress=laure.souai@cc.in2p3.fr";
////        $voForm["vo[VoContacts][email]"] = "laure.souai@cc.in2p3.fr";
////
////        $voForm["vo[VoMailingList][admins_mailing_list]"] = "laure.souai@cc.in2p3.fr";
////        $voForm["vo[VoMailingList][users_mailing_list]"] = "laure.souai@cc.in2p3.fr";
////        $voForm["vo[VoMailingList][security_contact_mailing_list]"] = "laure.souai@cc.in2p3.fr";
////        $voForm["vo[VoMailingList][operations_mailing_list]"] = "laure.souai@cc.in2p3.fr";
////
////        $this->crawler = $this->client->submit($voForm);
////
////        $this->assertEquals(1, $this->crawler->filter("#registrationComplete")->count(), "Registration failed...");
////
////
////        $this->assertEquals(1, $this->crawler->filter("#deleteMessage")->count(), "Registration failed...");
////
////
////        echo $this->crawler->filter("#deleteMessage")->text();
//
//
    }
//
    /**
     *
     * add ok
     * test on checking existing vo name
     */
    public function testCheckExistingVoName() {
        $this->logInSuUser();

        //Test with existing vo name
        $this->client->request('POST', '/vo/checkVoName', array("name" => $this->voTest->getName()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(true, json_decode($this->client->getResponse()->getContent(),true)["checkedName"]);

        //Test with unexisitng vo name
        $this->client->request('POST', '/vo/checkVoName', array("name" => "fakeName"));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(false, json_decode($this->client->getResponse()->getContent(),true)["checkedName"]);

        //Test with no vo name
        $this->client->request('POST', '/vo/checkVoName');
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }

    /*
    * Test update vo
     * add ok
    */
    public function testUpdateVo()
    {
        $this->logInSecurityUser();

        //test with no serial
        $this->crawler = $this->client->request('GET', '/vo/update/serial/toto');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        //test with correct parameters
        $this->crawler = $this->client->request('GET', '/vo/update/serial/'.$this->voTest->getSerial());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::voUpdateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not voUpdateAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Update VO vo.cictest.fr')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('General information')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('Acceptable use policy management')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('Acknowledgment Statement')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('Disciplines')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('Resources')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('Mailing List')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('Contact List')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('User Database')")->count(), 1);
        $this->assertEquals($this->crawler->filter("html:contains('VOMS Group and Role')")->count(), 1);


    }

    /*
     * add ok
   * Test update vo
   */
    public function testUpdateVoNoSu()
    {
        $this->logInSimpleUser();
        $this->crawler = $this->client->request('GET', '/vo/update/serial/' . $this->voTest->getSerial());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::voUpdateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not voUpdateAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Access Denied')")->count(), 1);

    }

    /*
     *
     * add ok
    * Test CONTACT LIST
    */
    public function testContactList()
    {
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/contactList', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::contactListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not contactListAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('VO MANAGER')")->count(), 1);

    }

    /**
     * ok useable
   * Test CONTACT Form
   */
    public function testContactForm()
    {
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/contactForm', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::contactFormAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not contactFormAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Add new Contact')")->count(), 1);

        $this->crawler = $this->client->request('GET', '/vo/contactForm', array("serial" => $this->voTest->getSerial(), "id" => "/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Cyril Lorphelin"));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is contactFormAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::contactFormAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not contactFormAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Edit /O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Cyril Lorphelin')")->count(), 1);

    }

    /**
     * ok useable
     * test on contact form submit
     */
    public function testContactFormSubmit() {

        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/contactForm', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

            //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::contactFormAction',
        $this->client->getRequest()->attributes->get('_controller'),
        "the controller method called is not contactFormAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Add new Contact')")->count(), 1);

        // submit the data to the form directly

        $contactForm = $this->crawler->filter("#contactForm")->form();


        $contactForm["vo_contact_has_profile[VoContacts][first_name]"] = "Vo";
        $contactForm["vo_contact_has_profile[VoContacts][last_name]"] = "User";
        $contactForm["vo_contact_has_profile[VoContacts][dn]"] = "/C=test/O=fake/CN=UserVo";
        $contactForm["vo_contact_has_profile[VoContacts][email]"] = "test.user@test.fr";

        $contactForm["vo_contact_has_profile[VoUserProfile]"] = "1";
        $contactForm["vo_contact_has_profile[comment]"] = "test";

        $contactForm["id"] = "/C=test/O=fake/CN=UserVo";
        $contactForm["serial"] =  $this->voTest->getSerial();

        $contactForm["vo_contact_has_profile[serial]"] =  $this->voTest->getSerial();
        $contactForm["vo_contact_has_profile[user_profile_id]"] =  "1";
        $contactForm["vo_contact_has_profile[VoContacts][grid_body]"] =  "0";

        $this->client->submit($contactForm);

        $this->assertTrue($this->client->getResponse()->isSuccessful());


        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Form submit has failed....");

        try {

            $voContact = $this->em->getRepository("AppBundle:VO\VoContacts")->findOneBy(array("dn" =>"/C=test/O=fake/CN=UserVo" ));

            $voContactHasProfile = $this->em->getRepository("AppBundle:VO\VoContactHasProfile")->findOneBy(array("serial" => $this->voTest->getSerial(), "contact_id" => $voContact->getId()));
            if ($voContactHasProfile != null) {
                /** @var $em EntityManager */
                $em = $this->container->get("doctrine")->getManager();
                $em->remove($voContactHasProfile);
                $em->flush();
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
//
//
    /**
     * ok useable
     * test on contact form submit without comment
     */
    public function testContactFormSubmitNoComment() {

        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/contactForm', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::contactFormAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not contactFormAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Add new Contact')")->count(), 1);

        // submit the data to the form directly

        $contactForm = $this->crawler->filter("#contactForm")->form();


        $contactForm["vo_contact_has_profile[VoContacts][first_name]"] = "VO";
        $contactForm["vo_contact_has_profile[VoContacts][last_name]"] = "User";
        $contactForm["vo_contact_has_profile[VoContacts][dn]"] = "/C=test/O=fake/CN=UserVo";
        $contactForm["vo_contact_has_profile[VoContacts][email]"] = "test.user@test.fr";

        $contactForm["vo_contact_has_profile[VoUserProfile]"] = "1";
        $contactForm["vo_contact_has_profile[comment]"] = null;

        $contactForm["id"] = "/C=test/O=fake/CN=UserVo";
        $contactForm["serial"] =  $this->voTest->getSerial();

        $contactForm["vo_contact_has_profile[serial]"] =  $this->voTest->getSerial();
        $contactForm["vo_contact_has_profile[user_profile_id]"] =  "1";
        $contactForm["vo_contact_has_profile[VoContacts][grid_body]"] =  "0";

        $this->client->submit($contactForm);

        $this->assertTrue($this->client->getResponse()->isSuccessful());


        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Form submit has failed....");

        try {

            $voContact = $this->em->getRepository("AppBundle:VO\VoContacts")->findOneBy(array("dn" =>"/C=test/O=fake/CN=UserVo" ));

            $voContactHasProfile = $this->em->getRepository("AppBundle:VO\VoContactHasProfile")->findOneBy(array("serial" => $this->voTest->getSerial(), "contact_id" => $voContact->getId()));
            if ($voContactHasProfile != null) {
                /** @var $em EntityManager */
                $em = $this->container->get("doctrine")->getManager();
                $em->remove($voContactHasProfile);
                $em->flush();
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }
//
//
//
    /**
     * ok useable
    * Test Voms Server LIST
    */
    public function testVomsServerList()
    {
        //create fake voms server
        $voVomsServer = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => $this->voTest->getSerial()));

        if ($voVomsServer == null) {
            $voVomsServer = new VoVomsServer();
            $voVomsServer->setSerial($this->voTest->getSerial());
            $voVomsServer->setHostname("cclcgvomsli01.in2p3.fr");
            $voVomsServer->setHttpsPort(8443);
            $voVomsServer->setVomsesPort(15010);
            $voVomsServer->setIsVomsadminServer(0);
            $voVomsServer->setMembersListUrl("https://cclcgvomsli01.in2p3.fr:8443/voms/cic.test.fr/services/VOMSAdmin?method=listMembers");

            try {
                /** @var $em EntityManager */
                $em = $this->container->get("doctrine")->getManager();
                $em->persist($voVomsServer);
                $em->flush();
                $em->refresh($voVomsServer);

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/vomsServerList', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::vomsServerListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not vomsServerListAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('cclcgvomsli01.in2p3.fr')")->count(), 1);

    }
//
//
    /**
     * ok useable
   * Test Voms server Form
   */
    public function testVomsServerForm()
    {

        //create fake voms server
        $voVomsServer = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => $this->voTest->getSerial()));

        if ($voVomsServer == null) {
            $voVomsServer = new VoVomsServer();
            $voVomsServer->setSerial($this->voTest->getSerial());
            $voVomsServer->setHostname("cclcgvomsli01.in2p3.fr");
            $voVomsServer->setHttpsPort(8443);
            $voVomsServer->setVomsesPort(15010);
            $voVomsServer->setIsVomsadminServer(0);
            $voVomsServer->setMembersListUrl("https://cclcgvomsli01.in2p3.fr:8443/voms/cic.test.fr/services/VOMSAdmin?method=listMembers");

            try {
                /** @var $em EntityManager */
                $em = $this->container->get("doctrine")->getManager();
                $em->persist($voVomsServer);
                $em->flush();
                $em->refresh($voVomsServer);

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/vomsServerForm', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vomsServerFormAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::vomsServerFormAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not vomsServerFormAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Add new Voms Server')")->count(), 1);

        $this->crawler = $this->client->request('GET', '/vo/vomsServerForm', array("serial" => $this->voTest->getSerial(), "id" => "cclcgvomsli01.in2p3.fr"));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::vomsServerFormAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not vomsServerFormAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Edit cclcgvomsli01.in2p3.fr')")->count(), 1);

        try {
            $voVomsServer = $this->em->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => $this->voTest->getSerial(), "is_vomsadmin_server" => 0));
            if ($voVomsServer != null) {
                $em = $this->container->get("doctrine")->getManager();
                $em->remove($voVomsServer);
                $em->flush();
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
//
    /**
     * ok useable
     *
     */
    public function testVomsServerFormSubmit() {

        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/vomsServerForm', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $vomsServerForm = $this->crawler->filter("#vomsServerForm")->form();

        $vomsServerForm["vo_voms_server[serial]"] = $this->voTest->getSerial();
        $vomsServerForm["vo_voms_server[https_port]"] = "8443";
        $vomsServerForm["vo_voms_server[vomses_port]"] = "15010";
        $vomsServerForm["vo_voms_server[hostname]"] = "cclcgvomsli01.in2p3.fr";
        $vomsServerForm["vo_voms_server[members_list_url]"] = "https://cclcgvomsli01.in2p3.fr:8443/voms/cic.test.fr/services/VOMSAdmin?method=listMembers";
        $vomsServerForm["vo_voms_server[is_vomsadmin_server]"] = 1;
        $vomsServerForm["mode"] = "create";

        $this->crawler = $this->client->submit($vomsServerForm);

        $this->assertTrue($this->client->getResponse()->isSuccessful(), "error on form submit");
        $this->assertContains("has been saved.",$this->crawler->filter("#submitMessage")->text(),"error on form submit" );

        try {
            $voVomsServer = $this->em->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => $this->voTest->getSerial(), "is_vomsadmin_server" => 1));
            if ($voVomsServer != null) {
                /** @var $em EntityManager */
                $em = $this->container->get("doctrine")->getManager();
                $em->remove($voVomsServer);
                $em->flush();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }
//
    /**
     * ok useable
     * test on VomsServerForm
     */
    public function testVomsServerFormUpdate() {

        //create fake voms server
        $voVomsServer = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => $this->voTest->getSerial()));

        if ($voVomsServer == null) {
            $voVomsServer = new VoVomsServer();
            $voVomsServer->setSerial($this->voTest->getSerial());
            $voVomsServer->setHostname("cclcgvomsli01.in2p3.fr");
            $voVomsServer->setHttpsPort(8443);
            $voVomsServer->setVomsesPort(15010);
            $voVomsServer->setIsVomsadminServer(0);
            $voVomsServer->setMembersListUrl("https://cclcgvomsli01.in2p3.fr:8443/voms/cic.test.fr/services/VOMSAdmin?method=listMembers");

            try {
                /** @var $em EntityManager */
                $em = $this->container->get("doctrine")->getManager();
                $em->persist($voVomsServer);
                $em->flush();
                $em->refresh($voVomsServer);

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }


        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/vomsServerForm', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $vomsServerForm = $this->crawler->filter("#vomsServerForm")->form();

        $vomsServerForm["vo_voms_server[serial]"] = $this->voTest->getSerial();
        $vomsServerForm["vo_voms_server[https_port]"] = "8443";
        $vomsServerForm["vo_voms_server[vomses_port]"] = "15010";
        $vomsServerForm["vo_voms_server[hostname]"] = "cclcgvomsli01.in2p3.fr";
        $vomsServerForm["vo_voms_server[members_list_url]"] = "https://cclcgvomsli01.in2p3.fr:8443/voms/cic.test.fr/services/VOMSAdmin?method=listMembers";
        $vomsServerForm["vo_voms_server[is_vomsadmin_server]"] = 1;
        $vomsServerForm["mode"] = "update";

        $this->crawler = $this->client->submit($vomsServerForm);

        $this->assertTrue($this->client->getResponse()->isSuccessful(), "error on form submit");
        $this->assertContains("has been saved.",$this->crawler->filter("#submitMessage")->text(),"error on form submit" );

        try {
            $voVomsServer = $this->em->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => $this->voTest->getSerial(), "is_vomsadmin_server" => 1));
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
     * ok useable
     * test on deleteVomsServerAction
     */
    public function testdeleteVomsServerAction() {

        //create fake voms server
        $voVomsServer = $this->container->get("doctrine")->getRepository("AppBundle:VO\VoVomsServer")->findOneBy(array("serial" => $this->voTest->getSerial()));

        if ($voVomsServer == null) {
            $voVomsServer = new VoVomsServer();
            $voVomsServer->setSerial($this->voTest->getSerial());
            $voVomsServer->setHostname("cclcgvomsli01.in2p3.fr");
            $voVomsServer->setHttpsPort(8443);
            $voVomsServer->setVomsesPort(15010);
            $voVomsServer->setIsVomsadminServer(0);
            $voVomsServer->setMembersListUrl("https://cclcgvomsli01.in2p3.fr:8443/voms/cic.test.fr/services/VOMSAdmin?method=listMembers");

            try {
                /** @var $em EntityManager */
                $em = $this->container->get("doctrine")->getManager();
                $em->persist($voVomsServer);
                $em->flush();
                $em->refresh($voVomsServer);

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        //delete this vomsserver
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/deleteVomsServer', array(
            "id" => $voVomsServer->getHostname(),
            "serial" => $this->voTest->getSerial()));

        $this->assertEquals('AppBundle\Controller\VO\VOController::deleteVomsServerAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteVomsServerAction");

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    /**
     * ok useable
    * Test Voms Group LIST
    */
    public function testVomsGroupList()
    {
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/vomsGroupList', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::vomsGroupListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not vomsGroupListAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('group/role')")->count(), 1);

    }

    /**
     * ok useable
   * Test Voms Group Form
   */
    public function testVomsGroupForm()
    {
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/vomsGroupForm', array("serial" => $this->voTest->getSerial()));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::vomsGroupFormAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not vomsGroupFormAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Add new Voms Group')")->count(), 1);

        $this->crawler = $this->client->request('GET', '/vo/vomsGroupForm', array("serial" => $this->voTest->getSerial(), "id" => 474));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::vomsGroupFormAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not vomsGroupFormAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($this->crawler->filter("html:contains('Edit group/role')")->count(), 1);
    }

    /**
     * ok useable
     * test on deleteVomsGroupAction
     */
    public function testDeleteVomsGroupAction() {

        //create fake vo voms group
        $voVomsGroup = new VoVomsGroup();
        $voVomsGroup->setGroupRole("/".$this->voTest->getName()."/Role=VO-Admin");
        $voVomsGroup->setDescription("Vo Administrators");
        $voVomsGroup->setIsGroupUsed(1);
        $voVomsGroup->setGroupType("");
        $voVomsGroup->setAllocatedRessources(0);
        $voVomsGroup->setSerial($this->voTest->getSerial());

        try {
            /** @var $em EntityManager */
            $em = $this->container->get("doctrine")->getManager();
            $em->persist($voVomsGroup);
            $em->flush();
            $em->refresh($voVomsGroup);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        //delete this vovomsgroup
        //delete this vomsserver
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/deleteVomsGroup', array(
            "id" => $voVomsGroup->getId()));

        $this->assertEquals('AppBundle\Controller\VO\VOController::deleteVomsGroupAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteVomsGroupAction");

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * ok useable
     * test on deleteContactHasProfileAction
     */
    public function testDeleteContactHasProfileAction() {

        //create fake contact
        $contact = new VoContacts();
        $contact->setFirstName("False");
        $contact->setLastName("Contact");
        $contact->setDn("/C=test/O=fake/CN=UserVo");
        $contact->setEmail("fakeUser@test.com");
        $contact->setGridBody(0);

        try {
            /** @var $em EntityManager */
            $em = $this->container->get("doctrine")->getManager();
            $em->persist($contact);
            $em->flush();
            $em->refresh($contact);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        //create fake relation
        $contactHasProfile = new VoContactHasProfile();
        $contactHasProfile->setSerial($this->voTest->getSerial());
        $contactHasProfile->setContactId($contact->getId());
        $contactHasProfile->setUserProfileId(1);
        $contactHasProfile->setComment("");

        try {
            /** @var $em EntityManager */
            $em = $this->container->get("doctrine")->getManager();
            $em->persist($contactHasProfile);
            $em->flush();
            $em->refresh($contactHasProfile);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }


        //delete this relation
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/vo/deleteContactHasProfil', array(
            "id" => $contactHasProfile->getContactId(),
            "serial" => $this->voTest->getSerial()));

        $this->assertEquals('AppBundle\Controller\VO\VOController::deleteContactHasProfilAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteContactHasProfilAction");

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //delete fake user
        try {
            $em = $this->container->get("doctrine")->getManager();
            $em->remove($contact);
            $em->flush();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * ok useable
     * test on manage aup file action
     */
    public function testManageAupFileAction() {

        $this->logInSuUser();

        $this->crawler = $this->client->request('GET', '/vo/manageAupFile/serial/'.$this->voTest->getSerial());

        $this->assertEquals('AppBundle\Controller\VO\VOController::manageAupFileAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not manageAupFileAction");


        $this->assertLessThanOrEqual(1, $this->crawler->filter("#tableAupManage")->count(), "table of manage Aup File view not present");

        $this->assertEquals(1, $this->crawler->filter("#voName")->count(), "no input voname in Aup File view");

        $this->assertEquals(1, $this->crawler->filter("#aupManageForm")->count(), "no form in Aup File view");

        //test submitting a file
        $form = $this->crawler->selectButton("Upload this file")->form();

        $form["manage_aup_file[aupFile]"] = new UploadedFile(
            __DIR__.'/../../Ressources/vo/AUP/aegis-AcceptableUsePolicy-20160129-1454076165609.xml',
            'aegis-AcceptableUsePolicy-20160129-1454076165609.xml',
            'text/xml',
            3314,
            0);
        $form["manage_aup_file[name]"] = "vo.cictest.fr-AcceptableUsePolicy-20160129-1454076165609.xml";


        $this->client->submit($form);

       $this->assertEquals(1, $this->crawler->filter(".alert-success")->count(), "The file sould have been successfully added");
    }

    /**
     * ok useable
     * TEST VO DETAIL MODAL PRESENTATION
     */
    public function testDetailVo()
    {
        $this->logInSimpleUser();
        $this->crawler = $this->client->request('POST', '/vo/voDetailAjax', array('serial' => $this->voTest->getSerial()));


        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::voDetailAjaxAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voDetailAjaxAction !! ");

        //test that a modal is displayed
        $this->assertEquals($this->crawler->filter(".modal")->count(), 1, "The modal didn't appear after call to detailAjaxAction method ...");

        //test on modal titles
        $this->assertEquals($this->crawler->filter("html:contains('General information')")->count(), 1, "The modal doesn't contain 'General Information' part ...");
        $this->assertEquals($this->crawler->filter("html:contains('Description')")->count(), 1, "The modal doesn't contain 'Description' part ...");
        $this->assertEquals($this->crawler->filter("html:contains('Acceptable Use Policy')")->count(), 1, "The modal doesn't contain 'Acceptable Use Policy' part ...");
        $this->assertEquals($this->crawler->filter("html:contains('Acknowledgments Statement')")->count(), 1, "The modal doesn't contain 'Acknowledgments Statement' part ...");
        $this->assertEquals($this->crawler->filter("html:contains('Resources')")->count(), 1, "The modal doesn't contain 'Resources' part ...");
        $this->assertEquals($this->crawler->filter("html:contains('Generic contacts')")->count(), 1, "The modal doesn't contain 'Generic contacts' part ...");
        $this->assertEquals($this->crawler->filter("html:contains('Mailing List')")->count(), 1, "The modal doesn't contain 'Mailing List' part ...");
        $this->assertEquals($this->crawler->filter("html:contains('VOMS information')")->count(), 1, "The modal doesn't contain 'VOMS information' part ...");
        $this->assertEquals($this->crawler->filter("html:contains('Groups and Roles')")->count(), 1, "The modal doesn't contain 'Groups and Roles' part ...");


        $this->assertTrue($this->crawler->filter("html:contains('Other requirements')")->count() <= 1, "The modal doesn't contain 'Other requirements' part ...");
    }

    /**
     * ok useable
     * test on downloadAUP
     */
    public function testdownloadAUPAction() {
        $this->logInSuUser();

        $this->crawler = $this->client->request('GET', '/vo/downloadAUP');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        $this->crawler = $this->client->request('GET', '/vo/downloadAUP/vo.cictest.fr-AcceptableUsePolicy-20160129-1454076165609.xml');


        $this->assertContains("text/force-download", $this->client->getResponse()->headers->get("Content-Type"));
        $this->assertContains("vo.cictest.fr-AcceptableUsePolicy-20160129-1454076165609.xml", $this->client->getResponse()->headers->get("content-disposition"));

    }

    /**
     * ok useable
     * test on vomsdetailsAjax
     */
    public function testVOMSDetailAjaxAction() {
        $this->logInSuUser();

        $this->crawler = $this->client->request('GET', '/vo/vomsDetailAjax');

        $this->assertEquals(1, $this->crawler->filter("#vomsDetailError")->count(), "No parameter, voms detail method must fail...");

        $this->crawler = $this->client->request('GET', '/vo/vomsDetailAjax',
            array("serial" => $this->voTest->getSerial(), "host" => "voms2.cern.ch"));

        $this->assertEquals(1, $this->crawler->filter("#vomsDetail".$this->voTest->getSerial()."voms2cernch")->count(), "Error on method voms detail");



    }

    /**
     * ok useable
     * test on validateVoAjax
     */
    public function testValidateVOAjaxAction() {
        $this->logInSuUser();

        $this->crawler = $this->client->request('GET', '/vo/validateVOAjax');

        $this->assertEquals(0, json_decode($this->client->getResponse()->getContent(),true)["isSuccess"], "No parameter, validate vo ajax method must fail...");


        $this->crawler = $this->client->request('GET', '/vo/validateVOAjax', array("serial" => $this->voTest->getSerial()));


        $this->assertEquals(1, json_decode($this->client->getResponse()->getContent(),true)["isSuccess"], "validate vo ajax method failed...");

    }

    /**
     * ok
     * test on synopticAction
     */
    public function testSynopticSuAction() {
        $this->logInSUUser();

        $this->crawler = $this->client->request('GET', '/vo/synoptic');

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::synopticAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT synopticAction !! ");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());


        $this->assertEquals(1, $this->crawler->filter("#badVoListTable")->count(), "no synoptic table...");

    }


    /**
     * ok useable
     * test on securityAction
     */
    public function testSynopticAction() {
        $this->logInSimpleUser();

        $this->crawler = $this->client->request('GET', '/vo/synoptic');


        $this->assertContains("Access Denied", $this->crawler->filterXPath("//h1")->text(), "user not security officer can not access this page...");


    }

    /**
     * ok
     * test on sendReportAction
     */
    public function testSendReportAction()
    {
        $this->logInSecurityUser();

        $this->crawler = $this->client->request('GET', '/vo/synoptic');

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::synopticAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT synopticAction !! ");

        $msg = '== Test contact form ==';


        $formReport = $this->crawler->filterXPath("//*[@id='sendReportForm_364']")->form(
            array(
                "voserial" => $this->voTest->getSerial(),
                "body" => "score : 71",
                "mail_subject" => "VO ID card Incorrect"
            )
        );

        $this->client->submit($formReport);


        // Form redirectes
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Form submit has failed....");
        $this->assertContains('Email has been sent to VO managers', $flash[0], "A flash message must have been set in session...");

        $this->assertTrue($this->client->getResponse()->isRedirect(), "report submit failed...");

        $this->client->followRedirect();

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::registerUpdateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the redirection is not the correct one !! ");

    }

    /**
     * ok useable
     * test on UserTrackingAction
     */

    public function testUserTrackingAction()
    {
        $this->logInSuUser();

        $this->client->enableProfiler();

        $this->crawler = $this->client->request('GET', '/vo/userTracking');

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::userTrackingAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT userTrackingAction !! ");

        $msg = '== Test contact form ==';

        $this->assertEquals(1, $this->crawler->filter("#userTrackingForm")->count());

        $form = $this->crawler->selectButton("Send Email")->form(array(
            'user_tracking[DN]' => '/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Cyril Lorphelin',
            'user_tracking[vo]' => 'biomed',
            'user_tracking[name]' => 'test',
            'user_tracking[email]' => 'test@test.com',
            'user_tracking[subject]' => 'test on user tracking',
            'user_tracking[body]' => 'test on user tracking test on user tracking test on user tracking test on user tracking'

        ));

        $this->client->submit($form);


        $this->assertTrue($this->client->getResponse()->isRedirect("/vo/userTracking"));


        $this->client->followRedirect();


        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::userTrackingAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT userTrackingAction !! ");


    }

    /**
     * ok useable
     * test on EmailTrackingAjaxAction
     */
    public function testEmailTrackingAjax()
    {
        $this->logInSuUser();

        $this->client->enableProfiler();

        $this->crawler = $this->client->request('GET', '/vo/userTracking');

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::userTrackingAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT userTrackingAction !! ");

        $msg = '== Test contact form ==';

        $this->assertEquals(1, $this->crawler->filter("#userTrackingForm")->count());

        $form = $this->crawler->selectButton("Send Email")->form(array(
            'user_tracking[DN]' => '/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Cyril Lorphelin',
            'user_tracking[vo]' => 'biomed',
            'user_tracking[name]' => 'test',
            'user_tracking[email]' => 'test@test.com',
            'user_tracking[subject]' => 'test on user tracking',
            'user_tracking[body]' => 'test on user tracking test on user tracking test on user tracking test on user tracking'

        ));

        $this->client->submit($form);


        $this->assertTrue($this->client->getResponse()->isRedirect("/vo/userTracking"));


        $this->client->followRedirect();


        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\VO\VOController::userTrackingAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT userTrackingAction !! ");


    }

    /**
     * ok useable
     * test on DNTrackingAjaxAction
     */
    public function testDNTrackingAjaxAction()
    {

        $this->logInSuUser();

        $this->client->enableProfiler();

        $this->crawler = $this->client->request('GET', '/vo/DNTrackingAjax', array('q' => 'cyril lorphelin'));


        if ($this->client->getResponse()->getStatusCode() == 500) {
            $this->assertContains("No results....", $this->client->getResponse()->getContent());
        } else {
            $this->assertNotEmpty($this->client->getResponse()->getContent(), "There must be at least one result...");
        }


        $this->crawler = $this->client->request('GET', '/vo/DNTrackingAjax', array('q' => 'toto'));


        if ($this->client->getResponse()->getStatusCode() == 500) {
            $this->assertContains("No results....", $this->client->getResponse()->getContent());
        } else {
            $this->assertNotEmpty($this->client->getResponse()->getContent(), "There must be at least one result...");
        }
    }

    /**
     * ok useable
     * test on securityAction
     */
    public function testSecurityAction()
    {
        $this->logInSimpleUser();

        $this->crawler = $this->client->request('GET', '/vo/security');


      $this->assertContains("Access Denied", $this->crawler->filterXPath("//h1")->text(), "user not security officer can not access this page...");
     //   $this->assertContains("You have not been recognized as an EGI Security Officer. Consequently you are not authorized to access to this URL.", $this->crawler->filter(".lead")->text(), "wrong page for access error");

    }

    /**
     * ok useable
     * test on securitySuAction
     */
    public function testSecuritySuAction()
    {
        $this->logInSecurityUser();

        $this->crawler = $this->client->request('GET', '/vo/security');


        $this->assertEquals(1, $this->crawler->filter("#securityListTable")->count(), "no security tab");


        foreach ($this->arraySecurity as $key => $value) {
            $this->assertTrue($this->crawler->filter("#securityListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view voEntries has been changed... ['" . $key . "'] is no more present...");
        }

    }


    /**
     * ok useable
     * test on voUrlCheckReport
     */
    public function testVoUrlCheckReport() {

        $this->logInSimpleUser();

        $this->crawler = $this->client->request('GET', '/vo/voUrlCheckReport');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), "VoUrlCheckReport doesn't work without vo name...");

        $this->crawler = $this->client->request('GET', '/vo/voUrlCheckReport/voname/alice');

        $this->assertTrue($this->client->getResponse()->isSuccessful(), "VoUrlCheckReport should work with a set vo name...");

        $this->assertGreaterThanOrEqual(4, $this->crawler->filter("h2")->count(), "There should be at least 3 parts");

        $this->assertEquals(1, $this->crawler->filterXPath("//h2[text()[contains(.,'Enrollment url')]]")->count(), "The should be a Enrollment url part");

        $this->assertEquals(1, $this->crawler->filterXPath("//h2[text()[contains(.,'Homepage url')]]")->count(), "The should be a Homepage url part");

        $this->assertGreaterThanOrEqual(1, $this->crawler->filterXPath("//h2[text()[contains(.,'List member url')]]")->count(), "The should be a Voms part");

    }


}