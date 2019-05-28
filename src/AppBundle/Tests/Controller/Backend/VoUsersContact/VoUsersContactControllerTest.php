<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 29/02/16
 * Time: 09:40
 */

namespace Tests\AppBundle\Controller\Backend\VoUsersContact;

use AppBundle\Entity\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class VoUsersContactControllerTest extends WebTestCase
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
    private $clientTrue;

    /**
     * @var $client \Symfony\Bundle\FrameworkBundle\Client
     */
    private $clientFalse;


    public function setUp()
    {
        self::bootKernel();

        $this->clientTrue = static::createClient([], ['REMOTE_ADDR' => '134.158.231.106']);

        $this->clientFalse = static::createClient([], ['REMOTE_ADDR' => '134.158.231.256']);

        $this->container = self::$kernel->getContainer();

    }

    /**
     * test on VoUsersContactListAction for user not allowed
     */
    public function testVoUsersContactListNotAllowedAction()
    {

        $this->crawler = $this->clientFalse->request('GET', '/a/backend/voUsersContact/list/C');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::voUsersContactListAction',
            $this->clientFalse->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT VoUsersContactListAction !! ");

        $this->assertEquals(401, $this->clientFalse->getResponse()->getStatusCode());

    }

    /**
     * test on VoUsersContactListAction for user allowed
     */
    public function testVoUsersContactListAllowedClassicAction()
    {


        $this->crawler = $this->clientTrue->request('GET', '/a/backend/voUsersContact/list/C');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::voUsersContactListAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT VoUsersContactListAction !! ");

        $this->assertEquals(200,$this->clientTrue->getResponse()->getStatusCode());

        //---------------------------------------------- TEST ON SIDEBAR MENU ------------------------------------------------------------------//
        $this->assertEquals(1, $this->crawler->filter("#sidebar-menu")->count(), "missing side bar menu");
        $this->assertEquals(1, $this->crawler->filter("#VoUsersTab")->count(), "missing VoUsersTab tab");

    }

    /**
     * test on VoUsersContactListAction for user allowed
     */
    public function testVoUsersContactListAllowedNoneAction()
    {

        $this->crawler = $this->clientTrue->request('GET', '/a/backend/voUsersContact/list/none');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::voUsersContactListAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voUsersContactListAction !! ");


        $this->assertEquals(200,$this->clientTrue->getResponse()->getStatusCode());

    }

    /**
     * test on VoUsersContactListAction for user allowed
     */
    public function testVoUsersContactListAllowedSpecialAction()
    {

        $this->crawler = $this->clientTrue->request('GET', '/a/backend/voUsersContact/list/special');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::voUsersContactListAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voUsersContactListAction !! ");


        $this->assertEquals(200,$this->clientTrue->getResponse()->getStatusCode());

    }


    /**
     * test on VoUsersContactListAction for super user
     */
    public function testVoUsersContactListAllowedNumericAction()
    {

        $this->crawler = $this->clientTrue->request('GET', '/a/backend/voUsersContact/list/numeric');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::voUsersContactListAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voUsersContactListAction !! ");


        $this->assertEquals(200,$this->clientTrue->getResponse()->getStatusCode());

    }

    /**
     * test on modifyVoUserContactAction for user not allowed
     */
    public function testmodifyVoUserContactNotAllowedAction()
    {

        $this->crawler = $this->clientFalse->request('GET', '/a/backend/voUsersContact/modify/firstName/Sandor/lastName/Acs/dn/%252FC%253DHU%252FO%253DNIIF+CA%252FOU%253DGRID%252FOU%253DSZTAKI%252FCN%253DSandor+Acs/email/acs@sztaki.hu');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::modifyVoContactAction',
            $this->clientFalse->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT modifyVoContactAction !! ");


        $this->assertEquals(401,$this->clientFalse->getResponse()->getStatusCode());

    }
        /**
         * test on modifyVoUserContactAction for user allowed
         */
        public function testmodifyVoUserContactAllowedAction() {

            $this->crawler = $this->clientTrue->request('GET', '/a/backend/voUsersContact/modify/firstName/Sandor/lastName/Acs/dn/%252FC%253DHU%252FO%253DNIIF+CA%252FOU%253DGRID%252FOU%253DSZTAKI%252FCN%253DSandor+Acs/email/acs@sztaki.hu');

            //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

            //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

            $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::modifyVoContactAction',
                $this->clientTrue->getRequest()->attributes->get('_controller'),
                "the controller method called is NOT modifyVoContactAction !! ");

            //---------------------------------------------- TEST ON SIDEBAR MENU ------------------------------------------------------------------//
            $this->assertEquals(1, $this->crawler->filter("#sidebar-menu")->count(), "missing side bar menu");
            $this->assertEquals(1, $this->crawler->filter("#voContactForm")->count(), "missing voContactForm form");

    }


    /**
     * test on modifyVoUserContactAction for user allowed
     */
    public function testmodifyVoUserContactPostAllowedAction() {

        $this->crawler = $this->clientTrue->request('GET', '/a/backend/voUsersContact/modify/firstName/Sandor/lastName/Acs/dn/%252FC%253DHU%252FO%253DNIIF%2520CA%252FOU%253DGRID%252FOU%253DSZTAKI%252FCN%253DSandor%2520Acs/email/acs@sztaki.hu');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::modifyVoContactAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT modifyVoContactAction !! ");

        //---------------------------------------------- TEST ON SIDEBAR MENU ------------------------------------------------------------------//
        $form = $this->crawler->selectButton("Modify User")->form();

        $form["vo_contacts_with_grid[first_name]"] = "Sandor";
        $form["vo_contacts_with_grid[last_name]"] = "Acs New";
        $form["vo_contacts_with_grid[dn]"] = "/C=HU/O=NIIF CA/OU=GRID/OU=SZTAKI/CN=Sandor Acs";
        $form["vo_contacts_with_grid[email]"] = "acs@sztaki.hu";

        $this->crawler = $this->clientTrue->submit($form);


        $this->assertTrue($this->clientTrue->getResponse()->isRedirect("/a/backend/voUsersContact/modify/firstName/Sandor/lastName/Acs%20New/dn/%252FC%253DHU%252FO%253DNIIF+CA%252FOU%253DGRID%252FOU%253DSZTAKI%252FCN%253DSandor+Acs/email/acs@sztaki.hu"));

        $session = $this->clientTrue->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Form submit has failed....");
        $this->assertContains('Modification was made successfully', $flash[0], "A flash message must have been set in session...");

        $this->crawler = $this->clientTrue->followRedirect();

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::modifyVoContactAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT modifyVoContactAction !! ");

        $form = $this->crawler->selectButton("Modify User")->form();

        $form["vo_contacts_with_grid[first_name]"] = "Sandor";
        $form["vo_contacts_with_grid[last_name]"] = "Acs";
        $form["vo_contacts_with_grid[dn]"] = "/C=HU/O=NIIF CA/OU=GRID/OU=SZTAKI/CN=Sandor Acs";
        $form["vo_contacts_with_grid[email]"] = "acs@sztaki.hu";
        $this->crawler = $this->clientTrue->submit($form);

    }


    /**
     * test on modifyVoUserContactAction with wrong form for user allowed
     */
    public function testmodifyVoUserContactPostErrorAction() {

        $this->crawler = $this->clientTrue->request('GET', '/a/backend/voUsersContact/modify/firstName/Sandor/lastName/Acs/dn/%252FC%253DHU%252FO%253DNIIF+CA%252FOU%253DGRID%252FOU%253DSZTAKI%252FCN%253DSandor+Acs/email/acs@sztaki.hu');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\VoUsersContact\VoUsersContactController::modifyVoContactAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT modifyVoContactAction !! ");

        //---------------------------------------------- TEST ON SIDEBAR MENU ------------------------------------------------------------------//
        $form = $this->crawler->selectButton("Modify User")->form();

        $form["vo_contacts_with_grid[first_name]"] = "Sandor";
        $form["vo_contacts_with_grid[dn]"] = "/C=HU/O=NIIF CA/OU=GRID/OU=SZTAKI/CN=Sandor Acs";
        $form["vo_contacts_with_grid[email]"] = "acs@sztaki.hu";

        $this->crawler = $this->clientTrue->submit($form);

        $this->assertFalse($this->clientTrue->getResponse()->isRedirect("/a/backend/voUsersContact/modify/firstName/Sandor/lastName/Acs%20New/dn/%252FC%253DHU%252FO%253DNIIF+CA%252FOU%253DGRID%252FOU%253DSZTAKI%252FCN%253DSandor+Acs/email/acs@sztaki.hu"));

    }
}