<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 29/02/16
 * Time: 09:40
 */

namespace Tests\AppBundle\Controller\Backend;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class BackendControllerTest extends WebTestCase
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
     * test on backend Home for user not allowed
     */
    public function testbackendHomeNotAllowedAction()
    {

        $this->crawler = $this->clientFalse->request('GET', '/a/backend/');


        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\BackendController::backendHomeAction',
            $this->clientFalse->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT backendHomeAction !! ");

        $this->assertEquals(401,$this->clientFalse->getResponse()->getStatusCode());


    }

    /**
     * test on backend Home for user allowed
     */
    public function testbackendHomeAllowedAction()
    {

        //----------------------------------------------------//

        $this->crawler = $this->clientTrue->request('GET', '/a/backend/');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->assertEquals('AppBundle\Controller\Backend\BackendController::backendHomeAction',
            $this->clientTrue->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT backendHomeAction !! ");

        $this->assertEquals(200,$this->clientTrue->getResponse()->getStatusCode());

        //---------------------------------------------- TEST ON SIDEBAR MENU ------------------------------------------------------------------//
        $this->assertEquals(1, $this->crawler->filter("#sidebar-menu")->count(), "missing side bar menu");
        $this->assertEquals(1, $this->crawler->filter(".tile_count")->count(), "missing tile_count tab");
        $this->assertEquals(1, $this->crawler->filter("#VoUsersTab")->count(), "missing VoUsersTab tab");
        $this->assertEquals(1, $this->crawler->filter("#lastUpdateTable")->count(), "missing VoUsersTab tab");
        $this->assertEquals(1, $this->crawler->filter("#spoolTable")->count(), "missing spool tab");
        $this->assertEquals(1, $this->crawler->filter("#downtimeTable")->count(), "missing VoUsersTab tab");

    }

}