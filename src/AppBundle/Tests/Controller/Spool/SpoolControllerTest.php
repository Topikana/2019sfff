<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 23/08/17
 * Time: 12:25
 */

namespace Tests\AppBundle\Controller\Spool;

use Symfony\Component\BrowserKit\Cookie;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;

class SpoolControllerTest extends WebTestCase
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
     * @var \AppBundle\Entity\User
     */
    private $user;


    public function setUp()
    {
        self::bootKernel();

        $this->client = static::createClient(array(), array('HTTPS' => true));

        $this->container = self::$kernel->getContainer();


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
     * @Route("/sendSpool", name="sendSpool")
     */
    public function testSendSpoolAction()
    {
        $this->logInSimpleUser();
        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->crawler = $this->client->request('GET', '/spool/sendSpool');


        $this->assertEquals('AppBundle\Controller\Spool\SpoolController::sendSpoolAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT sendSpoolAction !! ");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

}
