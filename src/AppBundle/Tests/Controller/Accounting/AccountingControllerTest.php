<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 23/08/17
 * Time: 12:25
 */

namespace Tests\AppBundle\Controller\Accounting;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;


class AccountingControllerTest extends WebTestCase
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
        $user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(41);

        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $attributes = [];

        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $user);


        $session->set('_security_'.$firewall, serialize($token));
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }


    /**
     * test sending accounting mail for VO
     */
    public function testSendAccountingMailVOAction()
    {

        $this->logInSimpleUser();
        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->crawler = $this->client->request('GET', '/accounting/sendAccountingMail/vo');


        $this->assertEquals('AppBundle\Controller\Accounting\AccountingController::sendAccountingMailAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT sendAccountingMailAction !! ");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());


        $this->assertContains("accounting report email sent with success",$this->client->getResponse()->getContent());
    }


    /**
     * test sending accounting mail for sites
     */
    public function testSendAccountingMailSiteAction()
    {
        $this->logInSimpleUser();
        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->crawler = $this->client->request('GET', '/accounting/sendAccountingMail/site');


        $this->assertEquals('AppBundle\Controller\Accounting\AccountingController::sendAccountingMailAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT sendAccountingMailAction !! ");


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains("accounting report email sent with success",$this->client->getResponse()->getContent());
    }


    /**
     * test sending accounting mail for wrong type
     */
    public function testSendAccountingMailWrongAction()
    {
        $this->logInSimpleUser();
        //---------------------------------------------- TEST ON CONTROLLER CALL ------------------------------------------------------------------//

        $this->crawler = $this->client->request('GET', '/accounting/sendAccountingMail/toto');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

    }


}
