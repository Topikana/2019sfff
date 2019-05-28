<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 29/02/16
 * Time: 09:40
 */

namespace Tests\AppBundle\Controller\Broadcast;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;


class BroadcastControllerTest extends WebTestCase
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
     * @var $user \AppBundle\Entity\User
     */
    private $user;

    /**
     * @var $voTest \AppBundle\Entity\VO\Vo
     */
    private $voTest;


    private $tabBdSent = array("Details", "Author", "Subject", "Emails", "Action");


    public function setUp()
    {
        self::bootKernel();

        $this->client = static::createClient(array(), array('HTTPS' => true));

        $this->container = self::$kernel->getContainer();

        $this->voTest = $this->container->get("doctrine")->getRepository("AppBundle:VO\Vo")->findOneBy(array("name" => "vo.cictest.fr"));

    }


    /**
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


    /**
     * test on broadcast home page with wrong form
     */
    public function testWrongBroadcastHomeAction()
    {
        $this->logInSimpleUser();

        $this->crawler = $this->client->request('GET', '/broadcast/');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        //control that the controller method called is broadcastHomeAction
        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::broadcastHomeAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT broadcastHomeAction !! ");

        $form = $this->crawler->selectButton('Validate Broadcast')->form();

        //submit empty form
        $this->crawler = $this->client->submit($form);

        //control that the controller method called is broadcastHomeAction
        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::broadcastHomeAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT broadcastHomeAction with POST parameters !! ");


        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());

    }

    /**
     * test on broadcast home page
     */
    public function testbroadcastHomeAction()
    {

        $this->logInSimpleUser();

        $this->crawler = $this->client->request('GET', 'broadcast/send');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        //control that the controller method called is broadcastHomeAction
        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::broadcastHomeAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT broadcastHomeAction !! ");


        //---------------------------------------------- SERVICES INITIALISATION ------------------------------------------------------------------//

        //load targets from jstree services to display in jstree view
        $jSTreeService = $this->container->get('broadcastjstree');

        $this->assertNotNull($jSTreeService, "no service broadcastjstree found");

        $targets = $jSTreeService->getBranchesHTML($this->user->getDN());
        $this->assertNotNull($targets, "no mail targets found with service broadcastjstree");


        //----------------------------------------------------------//
        // HTML COMPONENTS

        //header
        $this->menuHeaderTest();


        //targets jstree
        $this->assertTrue($this->crawler->filter("#jsTreeTargets")->count() == 1, "no jstree in broadcast home page");


        //predefined broadcast link
        $this->assertTrue($this->crawler->filter("#predefinedTitle")->count() == 1, "no predefined broadcast link in broadcast home page");


        //---------------------------------------------- FORM INITIALISATION ------------------------------------------------------------------//
        $this->assertTrue($this->crawler->filter("#formContactEGI")->count() == 1, "the broadcast form is not present in page...");

        //"Your name"
        $this->assertTrue($this->crawler->filterXPath("//*[@id='broadcast_message_author_cn']/@value")->text() == $this->user->getUsername(), "the author cn field is not correctly set...");

        $this->assertTrue($this->crawler->filter("#broadcast_message_author_cn")->count() == 1, "the broadcast form has no author cn field...");

        //"Your email"
        $this->assertTrue($this->crawler->filter("#broadcast_message_author_email")->count() == 1, "the broadcast form has no author email field...");

        //"cc"
        $this->assertTrue($this->crawler->filter("#broadcast_message_cc")->count() == 1, "the broadcast form has no cc field...");

        //"publication type"
        $this->assertTrue($this->crawler->filter("#broadcast_message_publication_type")->count() == 1, "the broadcast form has no publication type field...");

        //"news title/ mail subject"
        $this->assertTrue($this->crawler->filter("#broadcast_message_subject")->count() == 1, "the broadcast form has no subject field...");

        //"news content / mail body"
        $this->assertTrue($this->crawler->filter("#broadcast_message_body")->count() == 1, "the broadcast form has no publication type field...");

        //---------------------------------------------- TEST ON CONTROLLER CALL WITH PARAMETER ------------------------------------------------------------------//

        $this->crawler = $this->client->request('GET', 'broadcast/send/2182');

        //control that the controller method called is broadcastHomeAction
        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::broadcastHomeAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT broadcastHomeAction !! ");

        $form = $this->crawler->selectButton('Validate Broadcast')->form();

        /**
         * @var $broadcast \AppBundle\Entity\Broadcast\BroadcastMessage
         */
        $broadcast = $this->container->get("doctrine")->getRepository("AppBundle:Broadcast\BroadcastMessage")->findOneBy(array("id" => 2182));

        //test that the form has already been set
        $this->assertEquals($form["broadcast_message[author_email]"]->getValue(), $broadcast->getAuthorEmail(), "author email field has not been preset by model");
        $this->assertEquals($form["broadcast_message[publication_type]"]->getValue(), $broadcast->getPublicationType(), "publication type field has not been preset by model");
        $this->assertEquals($form["broadcast_message[subject]"]->getValue(), $broadcast->getSubject(), "subject field has not been preset by model");
        $this->assertEquals($form["broadcast_message[body]"]->getValue(), $broadcast->getBody(), "body field has not been preset by model");

        //---------------------------------------------- SET FORM AND SUBMIT ------------------------------------------------------------------//
        $form["broadcast_message[author_cn]"] = "Laure Souai";
        $form["broadcast_message[author_email]"] = "test@test.com";
        $form["broadcast_message[cc]"] = "test@test.com,test@test.com,test@test.com";
        $form["broadcast_message[confirmation]"] = "1";
        $form["broadcast_message[publication_type]"] = "0";
        $form["broadcast_message[subject]"] = 'test test test test test test';
        $form["broadcast_message[body]"] = 'test test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test test testtest test test test';
        $form["broadcast_message[targets]"] = "gm_1,gm_2,gm_3,gm_5,gm_4,gm_13,gm_7";


        $this->crawler = $this->client->submit($form);

        //control that the controller method called is broadcastHomeAction
        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::broadcastHomeAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT broadcastHomeAction with POST parameters !! ");

        $this->assertTrue($this->client->getResponse()->isRedirect("/broadcast/send"));

        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Form submit has failed....");
        $this->assertContains('Your broadcast has been sent! Thanks!', $flash[0], "A flash message must have been set in session...");

        $this->client->followRedirect();

        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::broadcastHomeAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT broadcastHomeAction with POST parameters !! ");


    }

    public function testBroadcastHomeFrom()
    {
        $this->logInSimpleUser();

        //---------------------------------------------- TEST ON CONTROLLER CALL WITH WRONG PARAMETER ------------------------------------------------------------------//
        $this->crawler = $this->client->request('GET', 'broadcast/send/xwxwxqs');

        //control that the controller method called is broadcastHomeAction
        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::broadcastHomeAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT broadcastHomeAction !! ");

        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("danger");
        $this->assertNotNull($flash, "There are no registered broadcast...");

    }

    /**
     * test on predefined broadcast
     */
    public function testshowMyBroadcastAjaxAction()
    {

        $this->logInSimpleUser();

        $this->crawler = $this->client->request('GET', 'broadcast/showMyBroadcastAjax');

        $this->assertTrue(($this->crawler->filter(".table")->count() <= 2), "max table == 2 in predefined broadcast modal");

    }


    /**
     * test on getSelectedTargets
     */
    public function testgetSelectedTargetsAjaxAction()
    {
        $this->logInSimpleUser();

        $this->crawler = $this->client->request('POST', 'broadcast/selectedTargetsAjax', array("targets" => 'gm_1,gm_2,gm_3,gm_5,gm_4,gm_13,gm_7'));

        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::getSelectedTargetsAjaxAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT getSelectedTargetsAjaxAction !! ");


        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotNull($response["targets"], "list of targets must not be null");

    }

    /**
     * test archive broadcast page
     */
    public function testArchiveAction()
    {
        $this->logInSimpleUser();

        //---------------------------------------------------------------------------------------------------------------------------------------------------------------//
        //test with id parameter
        $this->crawler = $this->client->request('GET', 'broadcast/archive');

        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::archiveAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT archiveAction !! ");

        $this->assertEquals(1, $this->crawler->filter("#broadcastSearchForm")->count(), "There must be a form to search specific EGI broadcast");

        $this->assertEquals(1, $this->crawler->filter("#accordionBd")->count(), "There must be an accordion part showing 'broadcast sent'");

        $this->assertEquals(1, $this->crawler->filter("#headingYrBdSent")->count(), "There must be the 'broadcast sent' part header");

        $this->assertEquals(1, $this->crawler->filter("#collapseYrBdSent")->count(), "There must be the 'broadcast sent' part WHEN ID IS SET '");

        $this->assertEquals(1, $this->crawler->filter("#headingLastBdSent")->count(), "There must be the 'last EGI broadcst sent' part header");

        $this->assertEquals(1, $this->crawler->filter("#collapseLastBdSent")->count(), "There must be the 'last EGI broadcst sent' part");

        if ($this->crawler->filter("#collapseResults .panel-body .table")->count() != 0) {

            foreach ($this->tabBdSent as $header) {
                $this->assertEquals(1, $this->crawler->filterXPath("//*[@id='collapseResults']/div[@class='panel-body']/table/thead/tr/th/div[contains(text(),'" . $header . "')]")->count(), $header . " Not present in table 'Your boradcast sent'");

            }
        }
        //---------------------------------------------------------------------------------------------------------------------------------------------------------------//

        $form = $this->crawler->selectButton('Search')->form();


        $form["broadcast_search[begin_date][month]"] = "1";
        $form["broadcast_search[begin_date][day]"] = "1";
        $form["broadcast_search[begin_date][year]"] = "2013";
        $form["broadcast_search[end_date][month]"] = "8";
        $form["broadcast_search[end_date][day]"] = "23";
        $form["broadcast_search[end_date][year]"] = "2017";
        $form["broadcast_search[author]"] = "cyril";

        $this->crawler = $this->client->submit($form);

        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::archiveAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT archiveAction !! ");

        $this->assertEquals(1, $this->crawler->filter("#headingResults")->count(), "There must be the 'result' part header");

        $this->assertEquals(1, $this->crawler->filter("#collapseResults")->count(), "There must be the 'result' part");

    }

    /**
     * test redirection to archive page from old url
     */
    public function testOldArchiveAction()
    {
        $this->logInSimpleUser();

        //---------------------------------------------------------------------------------------------------------------------------------------------------------------//
        //test with id parameter
        $this->crawler = $this->client->request('GET', 'broadcast/archive/id/1');

        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::oldArchiveAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT oldArchiveAction !! ");

        $this->assertTrue($this->client->getResponse()->isRedirect("/broadcast/archive/1"));
    }

    public function testArchiveWithIdAction()
    {
        $this->logInSimpleUser();

        //---------------------------------------------------------------------------------------------------------------------------------------------------------------//
        //test with id parameter
        $this->crawler = $this->client->request('GET', 'broadcast/archive/1');

        $this->assertEquals('AppBundle\Controller\Broadcast\BroadcastController::archiveAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT archiveAction !! ");


        $this->assertEquals(0, $this->crawler->filter("#broadcastSearchForm")->count(), "There must NOT be a form to search specific EGI broadcast WHEN ID IS SET");

        $this->assertEquals(1, $this->crawler->filter("#accordionBd")->count(), "There must be an accordion part showing 'broadcast sent'");

        $this->assertEquals(1, $this->crawler->filter("#headingResults")->count(), "There must be the 'result' part header");

        $this->assertEquals(1, $this->crawler->filter("#collapseResults")->count(), "There must be the 'result' part");

        $this->assertEquals(0, $this->crawler->filter("#headingYrBdSent")->count(), "There must NOT be the 'broadcast sent' part header WHEN ID IS SET'");

        $this->assertEquals(0, $this->crawler->filter("#collapseYrBdSent")->count(), "There must NOT be the 'broadcast sent' part WHEN ID IS SET '");

        $this->assertEquals(0, $this->crawler->filter("#headingLastBdSent")->count(), "There must NOT be the 'last EGI broadcst sent' part header WHEN ID IS SET");

        $this->assertEquals(0, $this->crawler->filter("#collapseLastBdSent")->count(), "There must NOT be the 'last EGI broadcst sent' part WHEN ID IS SET");

        if ($this->crawler->filter("#collapseResults .panel-body .table")->count() != 0) {

            foreach ($this->tabBdSent as $header) {
                $this->assertEquals(1, $this->crawler->filterXPath("//*[@id='collapseResults']/div[@class='panel-body']/table/thead/tr/th/div[contains(text(),'" . $header . "')]")->count(), $header . " Not present in table 'Your boradcast sent'");

            }
        }
        //---------------------------------------------------------------------------------------------------------------------------------------------------------------//
    }

    /**
     * test elements of header bar
     */
    private function menuHeaderTest()
    {

        //---------------------------------------------- TEST ON HEADER COMPONENTS ------------------------------------------------------------------//


        //control on html components in header
        //control that there is the header black bar
        $this->assertGreaterThanOrEqual(1, $this->crawler->filter(".navbar-inverse.mb-5")->count(), "There is no header navigation bar in the page !!");

        //control that there is the operations portal logo link and vapor logo link

        $this->assertGreaterThanOrEqual(1, $this->crawler->filterXPath("//*[@class='navbar-brand']")->count(), "There are no operations portal logo in the header !!");

        //control operations portal link
        $linkToOpsPortal = $this->crawler->selectLink("logo operations portal")->link();
        $this->client->click($linkToOpsPortal);

        //  $this->assertTrue($this->client->getResponse()->isSuccessful(), "redirection to operations portal failed !!");

        //control page title in header
        $this->assertEquals("Send", trim($this->crawler->filter(".navbar-text")->text()), "Wrong header title");

        $this->assertTrue($this->crawler->filter("#OpsPortalGlobalMenuBig")->count() == 1, "No Global ops portal menu for big device in header !!");

//        $this->assertTrue($this->crawler->filter("#OpsPortalGlobalMenuSmall")->count() == 1, "No Global ops portal menu for small device in header !!");

        $this->assertTrue($this->crawler->filter("#OpsPortalGlobalMenuBig .dropdown #dropdownMenu1")->count() == 1, "No module dropdown menu in header !!");

        $this->assertTrue($this->crawler->filter("#OpsPortalGlobalMenuBig .dropdown #dropdownMenu2")->count() == 1, "No user dropdown menu in header !!");

        //control that there is the navigation menu
        $this->assertTrue($this->crawler->filter("#navbarsf3")->count() == 1, "There is no navbar sf3 menu for big device in header !!");

//        $this->assertTrue($this->crawler->filter("#navbarSF3Small")->count() == 1, "There is no navbar sf3 menu for small device in header !!");

        //---------------------------------------------- TEST ON HEADER SF3 MENU LINKS ------------------------------------------------------------------//

        //first lvl of titles
        foreach ($this->container->getParameter('menu')["broadcast"]["items"] as $key => $module) {

            //test that each first lvl of titles correspond to the first lvl of titles in the "menu.yml" file
            $this->assertTrue($this->crawler->filter(".navbar-nav > li > a:contains('" . $module["title"] . "')")->count() > 0, "First level of links don't correspond to menu.yml file !!");

        }


    }


}