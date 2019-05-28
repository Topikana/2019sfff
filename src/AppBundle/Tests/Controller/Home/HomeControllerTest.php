<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 15/01/16
 * Time: 09:52
 */

namespace Tests\AppBundle\Controller\Home;

use AppBundle\Services\Mailer;
use AppBundle\Services\op\Message;
use Symfony\Component\BrowserKit\Cookie;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;


class HomeControllerTest extends WebTestCase
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



    
    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
        $this->container->get('security.token_storage')->setToken(null);
        $this->client = static::createClient(array(), array('HTTPS' => true));

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
     * test on home page
     */
    public function testHomeAction()
    {

        $this->logInSimpleUser();
        $this->crawler = $this->client->request('GET', '/');



        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\Home\HomeController::homeAction', $this->client->getRequest()->attributes->get('_controller'), "the controller method called is NOT homeAction !! ");

        //---------------------------------------------- TEST ON HTML ------------------------------------------------------------------//
        $this->assertEquals(1, $this->crawler->filter("#homePageMap")->count(), "Missing home page map");
        $this->assertEquals(2, $this->crawler->filter("#accordion_news")->count(), "Missing latest news part of home page");

    }

    /**
     * test on credit view
     */
    public function testCreditAction() {

        $this->crawler = $this->client->request('GET', '/home/a/credits');


        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\Home\HomeController::creditsAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT creditsAction !! ");


        //---------------------------------------------- TEST ON HTML ELEMENTS ------------------------------------------------------------------//
        $this->assertContains("Credits", $this->crawler->filter(".navbar-text")->text(), "This is not the right page title !");


        //test that headers of each part is in the "credits.yml" file
        foreach ($this->container->getParameter('credits') as $key => $value) {

            $this->assertEquals(1,$this->crawler->filter(".card-title:contains('".strip_tags($value['title'])."')")->count(), "Credits parts for ".$value['title']." are missing...");

        }
    }

    /**
     * test on site map view
     */
    public function testSiteMapAction() {

        $this->crawler = $this->client->request('GET', '/home/a/siteMap');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\Home\HomeController::siteMapAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT siteMapAction !! ");


        //---------------------------------------------- TEST ON SITE MAP ELEMENTS ------------------------------------------------------------------//

        foreach($this->container->getParameter("menu") as $key => $module) {

            $this->assertTrue($this->crawler->filter("#sitemap > ul > li > a:contains('".$module["title"]."')")->count() == 1, "The first lvl of site map doesn't correspond to menu.yml items !!" );
            foreach ($module['items'] as $key2 => $item) {
                $this->assertTrue($this->crawler->filter("#sitemap > ul > li > ul > li > a:contains('".$item["title"]."')")->count() >= 1, "The 2nd lvl of site map doesn't correspond to menu.yml items !!" );


                $link = $this->crawler->selectLink($item["title"])->link();


                $this->client->click($link);

                //test links on 2nd lvl links
                $this->assertEquals(200 || 302, $this->client->getResponse()->getStatusCode(), "redirecting to ".$item['title']." doesn't work as expected...");

            }
        }

    }


    /**
     * test on task list view
     */
    public function testTaskListAction() {
        $this->logInSimpleUser();

        $this->crawler = $this->client->request('GET', '/home/tasksList');
        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\Home\HomeController::tasksListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT tasksListAction !! ");


        $this->assertEquals(1, $this->crawler->filter("h2")->count(), "1 h2 title expected : ['OPERATIONS PORTAL']");

        $this->assertEquals(1, $this->crawler->filter("#opsportal_listReleases")->count(), "list of ops portal releases is missing");

        $this->assertEquals(1, $this->crawler->filter("#opsportal_listFeatures")->count(), "list of ops portal features for a release is missing");

    }


    /**
     * test on contact page
     */
    public function testContactAction() {
        $this->logInSimpleUser();

        // Enable the profiler for the next request (it does nothing if the profiler is not available)
        $this->client->enableProfiler();

        $this->crawler = $this->client->request('GET', 'home/contact');

        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

      
        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\Home\HomeController::contactAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT contactAction !! ");

        $msg = '== Test contact form ==';

        $this->assertEquals(1, $this->crawler->filter("#feedBackForm")->count());
        //---------------------------------------------- TEST ON MAIL SENDING ------------------------------------------------------------------//

        ///////////////////////////////////////////////////////
        //TEST OK MAIL
        ///////////////////////////////////////////////////////

        $form = $this->crawler->filter('form')->form(array(
            'mail[name]' => 'Laure Souai',
            'mail[email]' => 'laure.souai@cc.in2p3.fr',
            'mail[subject]' => "test email",
            'mail[cc]' => 'pierre.frebault@cc.in2p3.fr',
            'mail[body]' => $msg
        ));


        $this->client->submit($form);

        // Form redirectes
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());


        // Check that an email was sent
        $email = $this->container->getParameter('webMasterMail');
        $profile = $this->client->getProfile();

        /** @var $collector \Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface */
        $collector = $profile->getCollector('swiftmailer');

        $found = false;
        foreach ($collector->getMessages() as $message) {
            // Checking the recipient email and the X-Swift-To
            // header to handle the RedirectingPlugin.
            // If the recipient is not the expected one, check
            // the next mail.
            $correctRecipient = array_key_exists(
                $email, $message->getTo()
            );
            $headers = $message->getHeaders();
            $correctXToHeader = false;
            if ($headers->has('X-Swift-To')) {
                $correctXToHeader = array_key_exists($email,
                    $headers->get('X-Swift-To')->getFieldBodyModel()
                );
            }
            if (!$correctRecipient && !$correctXToHeader) {
                continue;
            }
            if (strpos($message->getBody(), $msg) !== false) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Email was not sent to ' . $email);


        ///////////////////////////////////////////////////////
        //TEST NOK MAILS : EMPTY MAIL
        ///////////////////////////////////////////////////////
//        $form = $this->crawler->filter('form')->form(array(
//            'mail[name]' => '',
//            'mail[email]' => '',
//            'mail[subject]' => "",
//            'mail[body]' => ""
//        ));
//
//        $this->client->submit($form);
//
//        // Form redirectes
//        $this->assertNotEquals(302, $this->client->getResponse()->getStatusCode());


        // Check that an email was sent
//        $email = $this->container->getParameter('webMasterMail');
//        $profile = $this->client->getProfile();
//        $collector = $profile->getCollector('swiftmailer');
//
//        $found = false;
//        foreach ($collector->getMessages() as $message) {
//            // Checking the recipient email and the X-Swift-To
//            // header to handle the RedirectingPlugin.
//            // If the recipient is not the expected one, check
//            // the next mail.
//            $correctRecipient = array_key_exists(
//                $email, $message->getTo()
//            );
//            $headers = $message->getHeaders();
//            $correctXToHeader = false;
//            if ($headers->has('X-Swift-To')) {
//                $correctXToHeader = array_key_exists($email,
//                    $headers->get('X-Swift-To')->getFieldBodyModel()
//                );
//            }
//            if (!$correctRecipient && !$correctXToHeader) {
//                continue;
//            }
//            if (strpos($message->getBody(), $msg) !== false) {
//                $found = true;
//                break;
//            }
//        }
//        $this->assertFalse($found, 'Email was  sent to ' . $email. " WITH EMPTY FIELDS !");


        ///////////////////////////////////////////////////////
        //TEST NOK MAILS : WRONG CHARACTERS IN EMAIL NAME
        ///////////////////////////////////////////////////////
//        $form = $this->crawler->filter('form')->form(array(
//            'mail[name]' => '@@@@@',
//            'mail[email]' => 'laure.souai@cc.in2p3.fr',
//            'mail[subject]' => "test",
//            'mail[body]' => $msg
//        ));
//
//
//        $this->client->submit($form);
//
//        // Form redirectes
//        $this->assertNotEquals(302, $this->client->getResponse()->getStatusCode());
//
//
//        // Check that an email was sent
//        $email = $this->container->getParameter('webMasterMail');
//        $profile = $this->client->getProfile();
//
//        $collector = $profile->getCollector('swiftmailer');
//
//        $found = false;
//        foreach ($collector->getMessages() as $message) {
//            // Checking the recipient email and the X-Swift-To
//            // header to handle the RedirectingPlugin.
//            // If the recipient is not the expected one, check
//            // the next mail.
//            $correctRecipient = array_key_exists(
//                $email, $message->getTo()
//            );
//            $headers = $message->getHeaders();
//            $correctXToHeader = false;
//            if ($headers->has('X-Swift-To')) {
//                $correctXToHeader = array_key_exists($email,
//                    $headers->get('X-Swift-To')->getFieldBodyModel()
//                );
//            }
//            if (!$correctRecipient && !$correctXToHeader) {
//                continue;
//            }
//            if (strpos($message->getBody(), $msg) !== false) {
//                $found = true;
//                break;
//            }
//        }
//        $this->assertFalse($found, 'Email was  sent to ' . $email. " WITH WRONG ELEMENT IN EMAIL NAME");
//
//
//        ///////////////////////////////////////////////////////
//        //TEST NOK MAILS : WRONG EMAIL SENDER
//        ///////////////////////////////////////////////////////
//        $form = $this->crawler->filter('form')->form(array(
//            'mail[name]' => 'Laure Souai',
//            'mail[email]' => 'laure.souai@ccopkj',
//            'mail[subject]' => "test",
//            'mail[body]' => $msg
//        ));
//
//
//        $this->client->submit($form);
//
//        // Form redirectes
//        $this->assertNotEquals(302, $this->client->getResponse()->getStatusCode());
//
//
//        // Check that an email was sent
//        $email = $this->container->getParameter('webMasterMail');
//        $profile = $this->client->getProfile();
//        $collector = $profile->getCollector('swiftmailer');
//
//        $found = false;
//        foreach ($collector->getMessages() as $message) {
//            // Checking the recipient email and the X-Swift-To
//            // header to handle the RedirectingPlugin.
//            // If the recipient is not the expected one, check
//            // the next mail.
//            $correctRecipient = array_key_exists(
//                $email, $message->getTo()
//            );
//            $headers = $message->getHeaders();
//            $correctXToHeader = false;
//            if ($headers->has('X-Swift-To')) {
//                $correctXToHeader = array_key_exists($email,
//                    $headers->get('X-Swift-To')->getFieldBodyModel()
//                );
//            }
//            if (!$correctRecipient && !$correctXToHeader) {
//                continue;
//            }
//            if (strpos($message->getBody(), $msg) !== false) {
//                $found = true;
//                break;
//            }
//        }
//        $this->assertFalse($found, 'Email was  sent to ' . $email. " WITH WRONG EMAIL SENDER");



        ///////////////////////////////////////////////////////
        //TEST NOK MAILS : WRONG SUBJECT
        ///////////////////////////////////////////////////////
//        $form = $this->crawler->filter('form')->form(array(
//            'mail[name]' => 'Laure Souai',
//            'mail[email]' => 'laure.souai@cc.in2p3.fr',
//            'mail[subject]' => "test",
//            'mail[body]' => ""
//        ));
//
//
//        $this->client->submit($form);
//
//        // Form redirectes
//        $this->assertNotEquals(302, $this->client->getResponse()->getStatusCode());
//
//
//        // Check that an email was sent
//        $email = $this->container->getParameter('webMasterMail');
//        $profile = $this->client->getProfile();
//        $collector = $profile->getCollector('swiftmailer');
//
//        $found = false;
//        foreach ($collector->getMessages() as $message) {
//            // Checking the recipient email and the X-Swift-To
//            // header to handle the RedirectingPlugin.
//            // If the recipient is not the expected one, check
//            // the next mail.
//            $correctRecipient = array_key_exists(
//                $email, $message->getTo()
//            );
//            $headers = $message->getHeaders();
//            $correctXToHeader = false;
//            if ($headers->has('X-Swift-To')) {
//                $correctXToHeader = array_key_exists($email,
//                    $headers->get('X-Swift-To')->getFieldBodyModel()
//                );
//            }
//            if (!$correctRecipient && !$correctXToHeader) {
//                continue;
//            }
//            if (strpos($message->getBody(), $msg) !== false) {
//                $found = true;
//                break;
//            }
//        }
//        $this->assertFalse($found, 'Email was  sent to ' . $email. " WITH EMPTY BODY");
    }

    /**
     * test terms of use page
     */
    public function testTermsOfUseAction() {

        $this->crawler = $this->client->request('GET', '/home/a/termsofuse');
        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\Home\HomeController::TermsUseAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT TermsUseAction !! ");


    }
}