<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Notepad;
use AppBundle\Entity\NotepadsAlarms;
use AppBundle\Entity\ROD\TicketAlarm;
use AppBundle\Entity\RodNagiosProblem;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use AppBundle\Services\TicketingSystem\Workflow\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;




class RODControllerTest extends WebTestCase
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
     * @var $rodTest \AppBundle\Entity\ROD\Ticket
     */
    private $ticketTest;


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \AppBundle\Entity\User
     */
    private $user;


    public function setUp(){
        self::bootKernel();
        $this->client = static::createClient(array(), array('HTTPS' => true));

        $kernel = new \AppKernel('dev', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->container = $kernel->getContainer();


        $this->container->get('security.token_storage')->setToken(null);
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

    public function testIndex()
    {

        $this->crawler = $this->client->request('GET', '/ROD');
        $this->assertTrue(true);
    }

    public function testSettings()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ROD/settings');
        $this->assertTrue(true);
    }

    public function testNewTicket(){

        $this->logInSUUser();

        $this->crawler = $this->client->request('GET', '/ROD');

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $_SERVER['HTTP_HOST'] = $this->client->getRequest()->getHost();

//        control that the controller method called is indexAction
        $this->assertEquals('AppBundle\Controller\RODController::indexAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not indexAction");


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(1, $this->crawler->filter('#formNewTicket')->count());

        // create fake alarm
        $alarmFake = new RodNagiosProblem();
        $alarmFake->setId(time().'test-alarms-id');
        $alarmFake->setVo('ops');
        $alarmFake->setLastHistoryId(12);
        $alarmFake->setCreatedAt( new \DateTime('now'));
        $alarmFake->setUpdatedAt( new \DateTime('now'));
        $alarmFake->setTestName('eu.egi.sec.WMS');
        $alarmFake->setHostName('wms2.edgi-grid.eu');
        $alarmFake->setService('WMS');
        $alarmFake->setStartDate(new \DateTime('now'));
        $alarmFake->setEndDate(new \DateTime('now + 1 day'));
        $alarmFake->setStatus(2);
        $alarmFake->setFlags(0);
        $alarmFake->setDetails('details');
        $alarmFake->setSummary('summary');
        $alarmFake->setSite('OBSPM');
        $alarmFake->setNgi('NGI_FRANCE');
        $alarmFake->setUrlToHistory('http://argo.egi.eu/lavoisier/site_ar?granularity=daily&report=Critical&site=UNIBE-LHEP');
        $alarmFake->setOpsFlags(2);


        $this->em->persist($alarmFake);
        try{
            $this->em->flush();
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }

        // submit the data to the form directly
        $form = $this->crawler->filter("#formNewTicket")->form();

        $form["appbundle_rod_ticket[site]"] = "OBSPM";
        $form["appbundle_rod_ticket[ngi]"] = "NGI_FRANCE";
        $form["appbundle_rod_ticket[description]"] = "--------------- TEST TICKET --------------\n


        TestName: eu.egi.sec.WMS
        HostName : wms2.edgi-grid.eu

        ----------------- GENERATED AUTOMATICALLY-----------------------------";
        $form["appbundle_rod_ticket[pbNumber]"] = 1;
        $form["appbundle_rod_ticket[alarms]"] = $alarmFake->getId();

        $this->client->submit($form);

        $this->client->followRedirect();

       $ggus_id=substr(strstr($this->client->getRequest()->getRequestUri(),'ggus_id='),8);

        $this->assertEquals(200,$this->client->getResponse()->getStatusCode(),'redirection ratée après soumission ticket');

        $this->crawler = $this->client->request('GET', '/ROD');
        $ticketAlarms= $this->container->get("doctrine")->getRepository(TicketAlarm::class)->findBy(['idTicket' => $ggus_id ]);

        $ticketId = $ticketAlarms[0]->getIdTicket();
        $alarmId = $ticketAlarms[0]->getIdAlarm();
        $site = "OBSPM";

        //        control that the controller method called is removeAlarmsToTicketAction
//        $this->crawler = $this->client->request('POST', '/ROD/removeTicketAlarm/'.$site.'/'.$ticketId.'/'.$alarmId);
        $this->crawler = $this->client->request('POST', '/ROD/removeTicketAlarm/', [
            'site' => $site,
            'alarms' => $alarmId,
            'ticket' => $ticketId
        ]);


        $this->assertEquals('AppBundle\Controller\RODController::removeAlarmsToTicketAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not removeAlarmsToTicketAction");

//        $this->crawler = $this->client->request('GET', '/ROD/addTicketAlarm/'.$site.'/'.$ticketId.'/'.$alarmId);
        $this->crawler = $this->client->request('POST', '/ROD/addTicketAlarm/',[
            'site' => $site,
            'alarms' => $alarmId,
            'ticket' => $ticketId
        ]);

        $this->assertEquals('AppBundle\Controller\RODController::addAlarmsToTicketAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not addAlarmsToTicketAction");

        $this->assertNotEmpty($ticketAlarms);

        $this->crawler = $this->client->request('GET', '/ROD');

        //control that the controller method called is vaporHomeAction
        $this->assertEquals('AppBundle\Controller\RODController::indexAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not indexAction");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $formDelete = $this->crawler->filter("#form-delete-ticket")->form();
        $formDelete["appbundle_rod_close_ticket[ticketId]"] = $ggus_id;

        $this->client->submit($formDelete);

        $ticketAlarms= $this->container->get("doctrine")->getRepository(TicketAlarm::class)->findBy(['idTicket' => $ggus_id ]);

        $this->assertEmpty($ticketAlarms);
        }

        public function testTicketHistoryAction(){
        $this->logInSUUser();

        $em = $this->container->get('doctrine')->getManager();
        $ticketAlarm = $this->container->get('doctrine')->getRepository(TicketAlarm::class)->findAll();

        foreach ($ticketAlarm as $ticketId){
            $ticketId->getIdTicket();
            if($ticketId != "0"){
                break;
            }
        }

        //        control that the controller method called is getTicketHistoryAction
        $this->crawler = $this->client->request('GET', '/ROD/ticket/'.$ticketId->getIdTicket());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //        control that the controller method called is indexAction
        $this->assertEquals('AppBundle\Controller\RODController::getTicketHistoryAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not ticketHistoryAction");

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());


    }

    public function testIndexAction(){
        $this->logInSUUser();

        //        control that the controller method called is indexAction
        $this->crawler = $this->client->request('GET', '/ROD');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        //        control that the controller method called is indexAction
        $this->assertEquals('AppBundle\Controller\RODController::indexAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is not indexAction");

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws \Exception
     * test Actions (notepadAlarmsGroupeAction, removeAlarmsToNotepadAction, addAlarmsToNotepadAction, getHistoryAlarms)
     */
//    public function testNotepadAlarmsaction(){
//
//        $this->logInSUUser();
//
//        $em = $this->container->get('doctrine')->getManager();
//
//        // create fake alarm
//        $alarm = new RodNagiosProblem();
//        $id=time().'id-for-test';
//        $alarm->setId($id);
//        $alarm->setVo('ops');
//        $alarm->setLastHistoryId(12);
//        $alarm->setCreatedAt( new \DateTime('now'));
//        $alarm->setUpdatedAt( new \DateTime('now'));
//        $alarm->setTestName('eu.egi.sec.WMS');
//        $alarm->setHostName('wms2.edgi-grid.eu');
//        $alarm->setService('WMS');
//        $alarm->setStartDate(new \DateTime('now'));
//        $alarm->setEndDate(new \DateTime('now + 1 day'));
//        $alarm->setStatus(2);
//        $alarm->setFlags(0);
//        $alarm->setDetails('details');
//        $alarm->setSummary('summary');
//        $alarm->setSite('IN2P3-IRES');
//        $alarm->setNgi('NGI_FRANCE');
//        $alarm->setUrlToHistory('http://argo.egi.eu/lavoisier/site_ar?granularity=daily&report=Critical&site=UNIBE-LHEP');
//        $alarm->setOpsFlags(2);
//
//
//        //create fake notepad
//        $notepad = new Notepad();
//        $notepad->setSubject('[Rod Dashboard] Issue detected : IN2P3-IRES');
//        $notepad->setComment('Dear all, Issues have been detected at IN2P3-IRES. ---');
//        $notepad->setCarbonCopy([1]);
//        $notepad->setSite('IN2P3-IRES');
//        $notepad->setCreationDate( new \DateTime('now'));
//        $notepad->setLastUpdate( new \DateTime('now'));
//        $notepad->setStatus(1);
//        $notepad->setGroupAlarms($id);
//
//        $notepad->setCurrentPlace($id);
//        $notepad->setLinkToAlarm("");
//        $notepad->setLastModifer("Ops Portal");
//
//        try {
//            $em->persist($notepad);
//            $em->persist($alarm);
//            $em->flush();
//
//            $em->refresh($notepad);
//            $em->refresh($alarm);
//        }
//
//        catch (\Exception $e) {
//            echo $e->getMessage();
//        }
//
//        $Notepad = $this->container->get('doctrine')->getRepository(Notepad::class)->findOneBy(['currentPlace' => $id]);
//        $idNotepad = $notepad->getId();
////        var_dump($idNotepad);die;
//
//        $groupAlarm = new NotepadsAlarms();
//        $groupAlarm->setIdAlarm($id);
//        $groupAlarm->setIdNotepad($idNotepad);
//        $em->persist($groupAlarm);
//        $em->flush();
//
//        $site  = 'IN2P3-IRES';
//        dump('/ROD/notepadAlarm/'.$site.'/'.$notepad->getId());
//
//        //        control that the controller method called is notepadAlarmsGroupAction
//        $this->crawler = $this->client->request('GET', '/ROD/notepadAlarm/'.$site.'/'.$notepad->getId());
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals('AppBundle\Controller\RODController::notepadAlarmsGroupeAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not notepadAlarmsGroupeAction");
//
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//
//        //      control that the controller method called is removeNotepadAlarm
//        $this->crawler = $this->client->request('POST', '/ROD/removeNotepadAlarm/',[
//            'site' => $site,
//            'alarms' => $alarm->getId(),
//            'notepad' => $notepad->getId()
//        ]);
//
//        $this->assertEquals('AppBundle\Controller\RODController::removeAlarmsToNotepadAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not removeAlarmsToNotepadAction");
//
//        //        control that the controller method called is addAlarmsToNotepadAlarm
//        $this->crawler = $this->client->request('POST', '/ROD/addNotepadAlarm/',[
//            'site' => $site,
//            'alarms' => $alarm->getId(),
//            'notepad' => $notepad->getId()
//        ]);
//        $this->assertEquals('AppBundle\Controller\RODController::addAlarmsToNotepadAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not addAlarmsToNotepadAction");
//
//        //        control that the controller method called is getHistoryAlarms
//        $this->crawler = $this->client->request('GET', '/ROD/history/alarms/'.$alarm->getId());
//        $this->assertEquals('AppBundle\Controller\RODController::getHistoryAlarms',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not getHistoryAlarms");
//
//        //        delete notepad, group alarm, alarm
//        $this->crawler = $this->client->request('GET', '/ROD/notepad/remove_notepad/'.$notepad->getId() );
//        $this->assertEquals('AppBundle\Controller\NotepadController::deleteNotepadAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not remove Notepad");
//
//
//
//        $this->crawler = $this->client->request('GET', '/ROD/notepad/delete_alarm_test/'.$alarm->getId() );
//        $this->assertEquals('AppBundle\Controller\NotepadController::deleteAlarmTestAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not deleteAlarm");
//
//        }

//    public function testAllDetailsSiteAction(){
//        $this->logInSUUser();
//
//        $site  = 'CYFRONET-LCG2';
//
//
//        $this->crawler = $this->client->request('GET', '/ROD/details/'.$site);
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals('AppBundle\Controller\RODController::getAllDetailsSiteAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not getAllDetailsSiteAction");
//
//
//    }

//    public function testDetailsSiteAction(){
//        $this->logInSUUser();
//        $site = "IN2P3-IRES";
//
//        //       control that the url called is DetailsSiteAction avre
//        $detailsType = 'avre';
//        $this->crawler = $this->client->request('POST', '/ROD/details', [
//                'site' => $site,
//                'detailsType' => $detailsType,
//            ]
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//
//        //       control that the controller method called is DetailsSiteAction avre
//        $this->assertEquals('AppBundle\Controller\RODController::detailsSiteAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not detailsSiteAction");
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//
//
//        //       control that the url method called is DetailsSiteAction alarms
//        $detailsType = 'alarms';
//        $this->crawler = $this->client->request('POST', '/ROD/details',
//            [
//                'site' => $site,
//                'detailsType' => $detailsType,
//            ]
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//
//        //       control that the controller method called is DetailsSiteAction alarms
//        $this->assertEquals('AppBundle\Controller\RODController::detailsSiteAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not detailsSiteAction");
//
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//
//
//        //       control that the url method called is DetailsSiteAction downtimes
//        $detailsType = 'downtimes';
//        $this->crawler = $this->client->request('POST', '/ROD/details',
//            ['site' => $site,
//                'detailsType' => $detailsType,
//            ]
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//
//        //       control that the controller method called is DetailsSiteAction downtimes
//        $this->assertEquals('AppBundle\Controller\RODController::detailsSiteAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not detailsSiteAction");
//
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//
//
//        //       control that the url method called is DetailsSiteAction tickets
//        $detailsType = 'tickets';
//        $this->crawler = $this->client->request('POST', '/ROD/details',
//            ['site' => $site,
//                'detailsType' => $detailsType,
//            ]
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//
//        //       control that the controller method called is DetailsSiteAction tickets
//        $this->assertEquals('AppBundle\Controller\RODController::detailsSiteAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not detailsSiteAction");
//
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//
//
//        //       control that the url method called is DetailsSiteAction notepads
//        $detailsType = 'notepads';
//        $this->crawler = $this->client->request('POST', '/ROD/details',
//            ['site' => $site,
//                'detailsType' => $detailsType,
//            ]
//        );
//
//
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//
//        //       control that the controller method called is DetailsSiteAction notepads
//        $this->assertEquals('AppBundle\Controller\RODController::detailsSiteAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not detailsSiteAction");
//
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//
//
//        //       control that the url method called is DetailsSiteAction alarmsSecurity
//        $detailsType = 'alarmsSecurity';
//        $this->crawler = $this->client->request('POST', '/ROD/details',
//            ['site' => $site,
//                'detailsType' => $detailsType,
//            ]
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//
//        //       control that the controller method called is DetailsSiteAction alarmsSecurity
//        $this->assertEquals('AppBundle\Controller\RODController::detailsSiteAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is not detailsSiteAction");
//
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//
//        }
}
