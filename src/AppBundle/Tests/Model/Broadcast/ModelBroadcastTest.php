<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 29/02/16
 * Time: 15:31
 */

namespace Tests\Model\Broadcast;

use AppBundle\Entity\VO\VoHeader;

use AppBundle\Model\Broadcast\ModelBroadcast;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModelBroadcastTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;


    private $container;


    /**
     * @var $client \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;



    public function __construct()
    {
        parent::__construct();

        self::bootKernel();


        $this->container = self::$kernel->getContainer();

        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->client = static::createClient(array(), array('HTTPS' => true));


    }

    private function logIn()
    {
        $session = $this->container->get('session');

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken(
            new User('/C=test/O=fake/CN=UserVo', null,null,array("ROLE_USER"),array()),
            null, $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        //IMPORTANT for get my broadcast method
        $this->container->get('security.token_storage')->setToken($token);


        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }


    /**
     * test on getLastBroadcasts
     */
    public function testgetLastBroadcasts() {

        $modelBd = new ModelBroadcast($this->container);

        //test that there is a result
        $this->assertNotNull($modelBd->getLastBroadcasts(3), "No result for last broadcasts...");

        //test max size of result
        $this->assertEquals(3, count($modelBd->getLastBroadcasts(3)), "Max size of result is determined by parameter");

    }


    public function testgetMyBroadcast() {

        $this->logIn();

        $modelBd = new ModelBroadcast($this->container);

        $this->assertTrue(count($modelBd->getMyBroadcast()) >= 0, "No result or at least 1 result for getMyBroadcast");


    }


    /**
     * test on getModels
     */
    public function testgetModels() {
        $modelBd = new ModelBroadcast($this->container);

        $this->assertTrue(count($modelBd->getModels()) >= 0, "No result or at least 1 result for getMyBroadcast");
    }


    /**
     * test on getJSONCheckBoxes
     */
    public function testgetJSONCheckBoxes() {

        $broadcastMessage = $this->container->get("doctrine")->getRepository("AppBundle:Broadcast\BroadcastMessage")->findOneById(1030);


        $modelBd = new ModelBroadcast($this->container);

        $res=json_decode($modelBd->getJSONCheckBoxes($broadcastMessage));


       if ($broadcastMessage != null) {
            $this->assertTrue( (int) $res[0]->{'values'}[0] > 0, "broadcast messages must have targets ! ");
        }

    }

    public function testSearch() {

        //test with only author
        $searchCriteria = array(
            "begin_date" => new \DateTime("2013-01-01 00:00:00"),
            "end_date" =>  new \DateTime("2016-02-29 00:00:00"),
            "author" => "Cyril L'orphelin");

        $modelBd = new ModelBroadcast($this->container);

        $this->assertTrue(count($modelBd->search($searchCriteria)) >= 0, "there must be none or at least one result");

        //test with author and subject
        $searchCriteria = array(
            "begin_date" => new \DateTime("2013-01-01 00:00:00"),
            "end_date" =>  new \DateTime("2016-02-29 00:00:00"),
            "author" => "Cyril L'orphelin",
            "subject" => "test confirmation");

        $modelBd = new ModelBroadcast($this->container);

        $this->assertTrue(count($modelBd->search($searchCriteria)) >= 0, "there must be none or at least one result");


        //test with author, subject and email
        $searchCriteria = array(
            "begin_date" => new \DateTime("2013-01-01 00:00:00"),
            "end_date" =>  new \DateTime("2016-02-29 00:00:00"),
            "author" => "Cyril L'orphelin",
            "subject" => "test confirmation",
            "email" => "testestest");

        $modelBd = new ModelBroadcast($this->container);

        $this->assertTrue(count($modelBd->search($searchCriteria)) >= 0, "there must be none or at least one result");


        //test with author, subject, email, body
        $searchCriteria = array(
            "begin_date" => new \DateTime("2013-01-01 00:00:00"),
            "end_date" =>  new \DateTime("2016-02-29 00:00:00"),
            "author" => "Cyril L'orphelin",
            "subject" => "test confirmation",
            "email" => "testestest",
            "body" => "testestestestestestestestestestestestestestestestestestestestestestest");

        $modelBd = new ModelBroadcast($this->container);

        $this->assertTrue(count($modelBd->search($searchCriteria)) >= 0, "there must be none or at least one result");
    }

}
