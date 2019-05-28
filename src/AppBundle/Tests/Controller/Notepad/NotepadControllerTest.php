<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 08/10/18
 * Time: 13:27
 */

namespace AppBundle\Tests\Controller\Notepad;


use AppBundle\Entity\Comment;
use AppBundle\Entity\Notepad;
use AppBundle\Entity\NotepadsAlarms;
use AppBundle\Entity\RodNagiosProblem;
use Doctrine\ORM\EntityManager;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Cookie;

class NotepadControllerTest extends WebTestCase
{
    /**
     * @var $crawler Crawler
     */
    private $crawler;

    /**
     * @var $client Client
     */
    private $client;

    /**
     * @var $container Container
     */
    private $container;

    /**
     * @var Notepad
     */
    private $notepad;

    /**
     * @var NotepadsAlarms
     */
    private $notepadAlarms;

    public function setUp(){
        self::bootKernel();
        $this->client = static::createClient(array(), array('HTTPS' => true));


        $kernel = new \AppKernel('dev', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->container = $kernel->getContainer();


    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'ROD');
        $this->assertTrue(true);
    }

    public function testSetting()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'ROD/notepad');
        $this->assertTrue(true);
    }

    /**
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

    public function testNewNotepadAction()
    {
        $this->logInSUUser();

        // create fake alarm
        $alarmFake1 = new RodNagiosProblem();
        $alarmFake1->setId(time() . 'test1-alarms-id');
        $alarmFake1->setVo('ops');
        $alarmFake1->setLastHistoryId(12);
        $alarmFake1->setCreatedAt(new \DateTime('now'));
        $alarmFake1->setUpdatedAt(new \DateTime('now'));
        $alarmFake1->setTestName('eu.egi.sec.WMS');
        $alarmFake1->setHostName('wms2.edgi-grid.eu');
        $alarmFake1->setService('WMS');
        $alarmFake1->setStartDate(new \DateTime('now'));
        $alarmFake1->setEndDate(new \DateTime('now + 1 day'));
        $alarmFake1->setStatus(2);
        $alarmFake1->setFlags(0);
        $alarmFake1->setDetails('details');
        $alarmFake1->setSummary('summary');
        $alarmFake1->setSite('OBSPM');
        $alarmFake1->setNgi('NGI_FRANCE');
        $alarmFake1->setUrlToHistory('http://argo.egi.eu/lavoisier/site_ar?granularity=daily&report=Critical&site=OBSPM');
        $alarmFake1->setOpsFlags(2);

        $em = $this->container->get("doctrine")->getManager();

        $this->em->persist($alarmFake1);
        try {
            $this->em->flush();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        $id1 = $alarmFake1->getId();

        // create fake alarm
        $alarmFake2 = new RodNagiosProblem();
        $alarmFake2->setId(time() . 'test2-alarms-id');
        $alarmFake2->setVo('ops');
        $alarmFake2->setLastHistoryId(12);
        $alarmFake2->setCreatedAt(new \DateTime('now'));
        $alarmFake2->setUpdatedAt(new \DateTime('now'));
        $alarmFake2->setTestName('eu.egi.sec.WMS');
        $alarmFake2->setHostName('wms2.edgi-grid.eu');
        $alarmFake2->setService('WMS');
        $alarmFake2->setStartDate(new \DateTime('now'));
        $alarmFake2->setEndDate(new \DateTime('now + 1 day'));
        $alarmFake2->setStatus(2);
        $alarmFake2->setFlags(0);
        $alarmFake2->setDetails('details');
        $alarmFake2->setSummary('summary');
        $alarmFake2->setSite('OBSPM');
        $alarmFake2->setNgi('NGI_FRANCE');
        $alarmFake2->setUrlToHistory('http://argo.egi.eu/lavoisier/site_ar?granularity=daily&report=Critical&site=OBSPM');
        $alarmFake2->setOpsFlags(2);

        $em = $this->container->get("doctrine")->getManager();

        $this->em->persist($alarmFake2);
        try {
            $this->em->flush();
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        $id2 = $alarmFake2->getId();

        //test url and action controller
        $this->crawler = $this->client->request('GET', '/ROD/notepad/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //        control that the controller method called is indexAction
        $this->assertEquals('AppBundle\Controller\NotepadController::newNotepadAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteAction");

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());


        // submit the data to the form directly
        $form = $this->crawler->filter("#formNewNotepad")->form();

        $form['appbundle_notepad[subject]'] = "[Rod Dashboard] Issue detected : IN2P3-IRES";
        $form['appbundle_notepad[comment]'] = "Dear all, Issues have been detected at IN2P3-IRES. ---";
//        $form['appbundle_notepad[carbon_copy]'] = [0 => 1];
        $form['appbundle_notepad[site]'] = "IN2P3-IRES";
        $form['appbundle_notepad[group_alarms]'] = $id1 . ',' . $id2;

        $this->client->submit($form);

        $notepad = $this->container->get("doctrine")->getRepository(Notepad::class)->findOneBy(['group_alarms' => $id1.','.$id2]);

//        $this->testGetDetailNotepadAction($notepad);
//        $this->testGetShowHistoryAction($notepad);

        $this->crawler = $this->client->request('GET', '/ROD/notepad/remove_notepad/'.$notepad->getId() );
        $this->assertEquals('AppBundle\Controller\NotepadController::deleteNotepadAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteAction");

        $this->client->followRedirects(true);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        //test url and action controller to delete alarmTest
        $this->crawler = $this->client->request('GET', '/ROD/notepad/delete_alarm_test/'.$alarmFake1->getId() );
        $this->assertEquals('AppBundle\Controller\NotepadController::deleteAlarmTestAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteAlarmTest1");

        $this->client->followRedirects(true);
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());

        //test url and action controller to delete alarmTest
        $this->crawler = $this->client->request('GET', '/ROD/notepad/delete_alarm_test/'.$alarmFake2->getId() );
        $this->assertEquals('AppBundle\Controller\NotepadController::deleteAlarmTestAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteAlarmTest2");

        $this->client->followRedirects(true);
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        //end test
    }

    public function testGetDetailNotepadAction(){

        $this->logInSUUser();

        $em = $this->container->get("doctrine")->getManager();
        //create fake notepad
        $notepad = new Notepad();
        $notepad->setSubject('[Rod Dashboard] Issue detected : IN2P3-IRES');
        $notepad->setComment('Dear all, Issues have been detected at IN2P3-IRES. ---');
        $notepad->setCarbonCopy([1]);
        $notepad->setSite('IN2P3-IRES');
        $notepad->setCreationDate( new \DateTime('now'));
        $notepad->setLastUpdate( new \DateTime('now'));
        $notepad->setStatus(1);
        $notepad->setGroupAlarms('1552922040test1-alarms-id,1552922040test2-alarms-id');

        $notepad->setCurrentPlace("");
        $notepad->setLinkToAlarm("");
        $notepad->setLastModifer("Ops Portal");


        try {
            $em->persist($notepad);
            $em->flush();

            $em->refresh($notepad);

        }

        catch (\Exception $e) {
            echo $e->getMessage();
        }


//        dump($notepad->getId());
//        $notepad = $this->container->get("doctrine")->getRepository(Notepad::class)->findOneBy(['id' => 2949]);

        //test url and action controller get Detail Notepad


        //test url and action controller add comment
        $this->crawler = $this->client->request('GET',  '/ROD/notepad/detail/'.$notepad->getId() );
        $this->assertEquals('AppBundle\Controller\NotepadController::getDetailNotepadAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not getDetailNotepadAction");
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $this->crawler->filter("#formNewComment")->form();
        $form['appbundle_notepad[commentary]'] = "Le test des comment very";

        $this->client->submit($form);
//        end test

        $this->crawler = $this->client->request('GET', '/ROD/notepad/remove_notepad/'.$notepad->getId() );
        $this->assertEquals('AppBundle\Controller\NotepadController::deleteNotepadAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteAction");
    }

//    /**
//     * @throws \Exception
//     */
//    public function testGetShowHistoryAction(){
//        $this->logInSUUser();
//
//        $em = $this->container->get("doctrine")->getManager();
//        $notepadBdd = $this->container->get("doctrine")->getRepository(Notepad::class)->findAll();
//        $notepad = $notepadBdd[0];
//
//        //test url and action controller history notepad
//        $this->crawler =  $this->client->request('GET', 'ROD/notepad/history/'. $notepad->getId());
//        $this->assertEquals('AppBundle\Controller\NotepadController::getShowHistoryAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method is not getShowHistoryAction"
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//        //end test
//    }

    /**
     * @throws \Exception
     */
    public function testDeleteNotepadAction(){

        /** @var $em EntityManager */
        $em = $this->container->get("doctrine")->getManager();

        $this->logInSuUser();

        // create fake alarm
        $alarm = new RodNagiosProblem();
        $id=time().'id-for-test';
        $alarm->setId($id);
        $alarm->setVo('ops');
        $alarm->setLastHistoryId(12);
        $alarm->setCreatedAt( new \DateTime('now'));
        $alarm->setUpdatedAt( new \DateTime('now'));
        $alarm->setTestName('eu.egi.sec.WMS');
        $alarm->setHostName('wms2.edgi-grid.eu');
        $alarm->setService('WMS');
        $alarm->setStartDate(new \DateTime('now'));
        $alarm->setEndDate(new \DateTime('now + 1 day'));
        $alarm->setStatus(2);
        $alarm->setFlags(0);
        $alarm->setDetails('details');
        $alarm->setSummary('summary');
        $alarm->setSite('IN2P3-IRES');
        $alarm->setNgi('NGI_FRANCE');
        $alarm->setUrlToHistory('http://argo.egi.eu/lavoisier/site_ar?granularity=daily&report=Critical&site=UNIBE-LHEP');
        $alarm->setOpsFlags(2);

        //create fake notepad
        $notepad = new Notepad();
        $notepad->setSubject('[Rod Dashboard] Issue detected : IN2P3-IRES');
        $notepad->setComment('Dear all, Issues have been detected at IN2P3-IRES. ---');
        $notepad->setCarbonCopy([1]);
        $notepad->setSite('IN2P3-IRES');
        $notepad->setCreationDate( new \DateTime('now'));
        $notepad->setLastUpdate( new \DateTime('now'));
        $notepad->setStatus(1);
        $notepad->setGroupAlarms($id);

        $notepad->setCurrentPlace("");
        $notepad->setLinkToAlarm("");
        $notepad->setLastModifer("Ops Portal");

        try {
            $em->persist($notepad);
            $em->persist($alarm);
            $em->flush();

            $em->refresh($notepad);
            $em->refresh($alarm);
        }

        catch (\Exception $e) {
            echo $e->getMessage();
        }

        $notepadId = $notepad->getId();

        $notepadAlarms = new NotepadsAlarms();
        $notepadAlarms->setIdNotepad($notepadId);
        $notepadAlarms->setIdAlarm($alarm->getId());

        try {
            $em->persist($notepadAlarms);
            $em->flush();
            $em->refresh($notepadAlarms);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        //test url and action controller
        $this->crawler = $this->client->request('GET', '/ROD/notepad/remove_notepad/'.$notepad->getId() );
        $this->assertEquals('AppBundle\Controller\NotepadController::deleteNotepadAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteAction");

        $this->client->followRedirects(true);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        //test url and action controller to delete alarmTest
        $this->crawler = $this->client->request('GET', '/ROD/notepad/delete_alarm_test/'.$alarm->getId() );
        $this->assertEquals('AppBundle\Controller\NotepadController::deleteAlarmTestAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not deleteAlarmTest");

        $this->client->followRedirects(true);
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        //end test
    }

}