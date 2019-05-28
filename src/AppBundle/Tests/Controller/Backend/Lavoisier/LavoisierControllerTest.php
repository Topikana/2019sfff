<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 29/02/16
 * Time: 09:40
 */

namespace Tests\AppBundle\Controller\Backend\Lavoisier;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class LavoisierControllerTest extends WebTestCase
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
     * test on global status for user not allowed
     */
    public function testglobalStatusUserNotAllowedAction()
    {


        $this->crawler = $this->clientFalse->request('GET', '/a/backend/lavoisier/globalStatus');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\Lavoisier\LavoisierController::globalStatusAction',
            $this->clientFalse->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT globalStatusAction !! ");

        $this->assertEquals(401,$this->clientFalse->getResponse()->getStatusCode());


    }

    /**
     * test on global status for user allowed
     */
    public function testglobalStatusUserAllowedAction()
    {

        $this->crawler = $this->clientTrue->request('GET', '/a/backend/lavoisier/globalStatus');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\Lavoisier\LavoisierController::globalStatusAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT globalStatusAction !! ");

        $this->assertEquals(200,$this->clientTrue->getResponse()->getStatusCode());

        //---------------------------------------------- TEST ON SIDEBAR MENU ------------------------------------------------------------------//
        $this->assertEquals(1, $this->crawler->filter("#sidebar-menu")->count(), "missing side bar menu");
        $this->assertEquals(1, $this->crawler->filter("#lavoisierUrlTab")->count(), "missing lavoisierUrlTab");

    }

    /**
     * test on ok nok views for user not allowed
     */
    public function testOkNokViewUserNotAllowedAction()
    {

        $this->crawler = $this->clientFalse->request('GET', '/a/backend/lavoisier/OkNokViews');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\Lavoisier\LavoisierController::OkNokViewsAction',
            $this->clientFalse->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT OkNokViewsAction !! ");

        $this->assertEquals(401,$this->clientFalse->getResponse()->getStatusCode());


    }

    /**
     * test on global status for user allowed
     */
    public function testOkNokViewUserAllowedAction()
    {

        $this->crawler = $this->clientTrue->request('GET', '/a/backend/lavoisier/OkNokViews');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\Lavoisier\LavoisierController::OkNokViewsAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT OkNokViewsAction !! ");

        $this->assertEquals(200,$this->clientTrue->getResponse()->getStatusCode());

        //---------------------------------------------- TEST ON SIDEBAR MENU ------------------------------------------------------------------//
        $this->assertEquals(1, $this->crawler->filter("#sidebar-menu")->count(), "missing side bar menu");



        $this->assertEquals(1, $this->crawler->filterXPath("//h2[text()[contains(.,'ccosvms0086')]]")->count(), "missing ccosvms0086 tab");
        $this->assertEquals(1, $this->crawler->filter("#ccosvms0086OK")->count(), "missing ccovms0086 OK tab");
        $this->assertEquals(1, $this->crawler->filter("#ccosvms0086NOK")->count(), "missing ccovms0086 NOK tab");

        $this->assertEquals(1, $this->crawler->filterXPath("//h2[text()[contains(.,'cclavoisier01')]]")->count(), "missing cclavoisier01 tab");
        $this->assertEquals(1, $this->crawler->filter("#cclavoisier01OK")->count(), "missing cclavoisier01 OK tab");
        $this->assertEquals(1, $this->crawler->filter("#cclavoisier01NOK")->count(), "missing cclavoisier01 NOK tab");
    }


    /**
     * test notification on lavoisier for user allowed
     */
    public function testnotifyLavoisierAjaxAction() {

        // no arguement
        $this->crawler = $this->clientTrue->request('POST', '/a/backend/lavoisier/notifyLavoisierAjax');
        $this->assertEquals(500, $this->clientTrue->getResponse()->getStatusCode(), "missing arguments lavoisier and view");

        //missing lavoisier argument
        $this->crawler = $this->clientTrue->request('POST', 'notifyLavoisierAjax', array("view" => "OPSCORE_users_agg"));

        $this->assertEquals(500, $this->clientTrue->getResponse()->getStatusCode(), "missing arguments lavoisier and view");


        //missing view argument
        $this->crawler = $this->clientTrue->request('POST', 'notifyLavoisierAjax', array("lavoisier" => "cclavoisier01"));
        $this->assertEquals(500, $this->clientTrue->getResponse()->getStatusCode(), "missing arguments lavoisier and view");


        //arguments OK
        $this->crawler = $this->clientTrue->request('POST', 'notifyLavoisierAjax', array("lavoisier" => "cclavoisier01", "view" => "OPSCORE_users_agg"));
        $this->assertEquals(200, $this->clientTrue->getResponse()->getStatusCode());
    }
}