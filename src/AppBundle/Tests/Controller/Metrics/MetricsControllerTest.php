<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 15/01/16
 * Time: 09:52
 */

namespace Tests\AppBundle\Controller\Metrics;

use AppBundle\Entity\VO\VoRobotCertificate;
use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Cookie;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetricsControllerTest extends WebTestCase
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


    private $rbCertArray = array("Vo", "Robot DN", "Contact Email", "use per-user sub-proxies", "Service Name", "Service Url", "Validation date", "Action");


    /**
     * @var $voTest \AppBundle\Entity\VO\Vo
     */
    private $voTest;


    /**
     * @var $user \AppBundle\Entity\User
     */
    private $user;



    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
        $this->client = static::createClient(array(),array('HTTPS' => true));

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
     * log in as simple fake su user
     */
    private function logInVOUser()
    {
        $this->user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(44);

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
     * log in as simple fake su user
     */
    private function logInSUUser()
    {
        $this->user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(1258);

        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $attributes = [];

        $this->user->setOpRoles('cclavoisier01.in2p3.fr');

        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $this->user);


        $session->set('_security_'.$firewall, serialize($token));
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }


    /**
     * test on MetricsReports view
     */
    public function testMetricsReportAction() {

        //access without registration CA
        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        //log in classic user
        $this->logInSimpleUser();

        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "The metrics reports view doesn't work !");

        $this->assertEquals(1, $this->crawler->filter("#metricsReportsForm")->count(), "The EGI reports form is not present in view");

        $this->assertEquals(0, $this->crawler->filter("#metricsDump")->count(), "Only SU User can access vo metrics dump");

    }

    /**
     *
     */
    public function testMetricsReportSU(){
        //log in su user
        $this->logInSuUser();

        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "The metrics reports view doesn't work !");

        $this->assertEquals(1, $this->crawler->filter("#metricsReportsForm")->count(), "The EGI reports form is not present in view");

        $this->assertEquals(1, $this->crawler->filter("#metricsDump")->count(), "Only SU User can access vo metrics dump");

        $form = $this->crawler->selectButton("Submit")->form();

        $form["metrics_report[entity]"] = "vo";
        $form["metrics_report[begin_date]"] = "04/2017";

        $this->crawler = $this->client->submit($form);

        //control that the controller method called is broadcastHomeAction
        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::redirectToReportsListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT redirectToReportsListAction with POST parameters !! ");


    }

    /**
     * test on voMetricsDump on page metricsReport
     */
    public function testVoMetricsDump() {
        //log in su user
        $this->logInSuUser();

        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "The metrics reports view doesn't work with https access !");

        // test on set of users dump
        $dumpUsersBtn =$this->crawler->filterXPath("//a[@title='download dump of the whole set of users in a gz file']")->link();

        $this->client->click($dumpUsersBtn);

        $this->assertContains("vo_users_raw.csv", $this->client->getResponse()->headers->get("content-disposition"));

        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        // test on users accounting dump
        $dumpUsersAccoutingBtn =$this->crawler->filterXPath("//a[@title='download the history of the users accounting']")->link();

        $this->client->click($dumpUsersAccoutingBtn);

        $this->assertContains("vo_users_history.csv", $this->client->getResponse()->headers->get("content-disposition"));
    }

    /**
     * test on redirectToReportsListAction
     */
    public function testredirectToReportsListAction() {
        //log in classic user
        $this->logInSimpleUser();

        //no parameter
        $this->crawler = $this->client->request('GET', '/metrics/redirectToReportsList');

        $this->assertTrue($this->client->getResponse()->isRedirect("/metrics/metricsReports"), "Missing form parameters");

        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //wrong parameter
//        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');
//
//        $form = $this->crawler->selectButton("Submit")->form();
//
//        $form["metrics_report[entity]"] = "vo";
//        $form["metrics_report[begin_date]"]= "dsdsqdsqdsq";
//
//        $this->crawler = $this->client->submit($form);
//
//        $this->client->followRedirect();
//
//        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::metricsReportsAction',
//            $this->client->getRequest()->attributes->get('_controller'),
//            "the controller method called is NOT metricsReportsAction with POST parameters !! ");
        ///////////////////////////////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //VO entity parameter
        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        $form = $this->crawler->selectButton("Submit")->form();

        $form["metrics_report[entity]"] = "vo";
        $form["metrics_report[begin_date]"]= "04/2016";

        $this->crawler = $this->client->submit($form);

        $this->client->followRedirect();

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::metricsReportsListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT metricsReportsListAction with POST parameters !! ");
        ///////////////////////////////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //CA entity parameter
        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        $form = $this->crawler->selectButton("Submit")->form();

        $form["metrics_report[entity]"] = "ca";
        $form["metrics_report[begin_date]"]= "04/2016";

        $this->crawler = $this->client->submit($form);

        $this->client->followRedirect();

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::metricsReportsListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT metricsReportsListAction with POST parameters !! ");
        ///////////////////////////////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //discipline entity parameter
        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        $form = $this->crawler->selectButton("Submit")->form();

        $form["metrics_report[entity]"] = "discipline";
        $form["metrics_report[begin_date]"]= "04/2016";

        $this->crawler = $this->client->submit($form);

        $this->client->followRedirect();

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::disciplineMetricsReportsAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT disciplineMetricsReportsAction with POST parameters !! ");
        ///////////////////////////////////////////////////////////////////////////////////////////////////////


        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //national entity parameter
        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        $form = $this->crawler->selectButton("Submit")->form();

        $form["metrics_report[entity]"] = "national";
        $form["metrics_report[begin_date]"]= "04/2016";

        $this->crawler = $this->client->submit($form);

        $this->client->followRedirect();

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::internationalMetricsReportsTableAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT internationalMetricsReportsTableAction with POST parameters !! ");
        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //vo activities entity parameter
        $this->crawler = $this->client->request('GET', '/metrics/metricsReports');

        $form = $this->crawler->selectButton("Submit")->form();

        $form["metrics_report[entity]"] = "voActivities";
        $form["metrics_report[start_date]"] = "03/2017";
        $form["metrics_report[end_date]"]= "08/2017";

        $this->crawler = $this->client->submit($form);

        $this->client->followRedirect();

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::voActivitiesReportsTableAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT internationalMetricsReportsTableAction with POST parameters !! ");
        ///////////////////////////////////////////////////////////////////////////////////////////////////////
    }


    /**
     * test on metricsReportsListAction
     */
    public function testMetricsReportsListAction() {

        //log in classic user
        $this->logInSimpleUser();

        //no parameter
        $this->crawler = $this->client->request('POST', '/metrics/metricsReportsList');

        $this->assertEquals(404,$this->client->getResponse()->getStatusCode(), "Missing form parameters");

        //entity : vo
        $this->crawler = $this->client->request('POST', '/metrics/metricsReportsList/vo/2016-04-01 00:00:00');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::metricsReportsListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT metricsReportsListAction with POST parameters !! ");

        $this->assertEquals("User metrics per VO",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#usersMetricsTable")->count(), "missing user metrics per vo table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "missing user metrics per vo breadcrumb");


        //entity : ca
        $this->crawler = $this->client->request('POST', '/metrics/metricsReportsList/ca/2016-04-01 00:00:00');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::metricsReportsListAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT metricsReportsListAction with POST parameters !! ");

        $this->assertEquals("User metrics per CA",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#usersMetricsTable")->count(), "missing user metrics per ca table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "missing user metrics per ca breadcrumb");

    }


    /**
     * test on disciplineMetricsReportsAction
     */
    public function testdisciplineMetricsReportsAction(){

        //log in classic user
        $this->logInSimpleUser();

        //no parameter
        $this->crawler = $this->client->request('POST', '/metrics/disciplineMetricsReports');

        $this->assertEquals(404,$this->client->getResponse()->getStatusCode(), "Missing form parameters");

        //no discipline id
        $this->crawler = $this->client->request('POST', '/metrics/disciplineMetricsReports/discipline/2016-04-01 00:00:00');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::disciplineMetricsReportsAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT disciplineMetricsReportsAction with POST parameters !! ");

        $this->assertEquals("User metrics per DISCIPLINE",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#usersMetricsTable")->count(), "missing user metrics per discipline table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "missing user metrics per discipline breadcrumb");


        //with discipline id
        $this->crawler = $this->client->request('POST', '/metrics/disciplineMetricsReports/discipline/2016-04-01 00:00:00/110');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::disciplineMetricsReportsAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT disciplineMetricsReportsAction with POST parameters !! ");

        $this->assertEquals("User metrics per DISCIPLINE",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#usersMetricsTable")->count(), "missing user metrics per discipline table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "missing user metrics per discipline breadcrumb");
    }



    /**
     * test on internationalMetricsReportsTableAction
     */
    public function testInternationalMetricsReportsTableAction(){

        //log in classic user
        $this->logInSimpleUser();

        //no parameter
        $this->crawler = $this->client->request('POST', '/metrics/internationalMetricsReportsTable');

        $this->assertEquals(404,$this->client->getResponse()->getStatusCode(), "Missing form parameters");

        //rights parameters
        $this->crawler = $this->client->request('POST', '/metrics/internationalMetricsReportsTable/international/2016-04-01 00:00:00');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::internationalMetricsReportsTableAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT internationalMetricsReportsTableAction with POST parameters !! ");

        $this->assertEquals("Vo creations",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#internationalUsersMetricsTable")->count(), "missing vo creations table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "missing vo creations breadcrumb");

    }

    /**
     * test on voActivitiesReportsTableAction
     */
    public function testVoActivitiesReportsTableAction() {
        //log in classic user
        $this->logInSimpleUser();

        //no parameter
        $this->crawler = $this->client->request('POST', '/metrics/voActivitiesReportsTable');

        $this->assertEquals(404,$this->client->getResponse()->getStatusCode(), "Missing form parameters");

        //rights parameters
        $this->crawler = $this->client->request('POST', '/metrics/voActivitiesReportsTable/voActivities/2017-03/2017-08');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::voActivitiesReportsTableAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT internationalMetricsReportsTableAction with POST parameters !! ");

        $this->assertEquals("Vo activities",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#voActivitiesMetricsTable")->count(), "missing voActivities Metrics Table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "missing vo creations breadcrumb");

    }



    /**
     * test on getVoCreationDetailsAjaxAction
     */
    public function testGetVoCreationDetailsAjaxAction(){
        //log in classic user
        $this->logInSimpleUser();


        $dateStart = '2014-10-01';
        $dateEnd = '2015-04-01';

        //no parameter
        $this->crawler = $this->client->request('POST', '/metrics/voCreationDetailsAjax',
            array("date_start" => $dateStart, "date_end" => $dateEnd));

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::voCreationDetailsAjaxAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT voCreationDetailsAjaxAction with POST parameters !! ");


        $this->assertEquals(1, $this->crawler->filter("#voCreatedDetails")->count(), "missing created vo details collapse");

        $this->assertEquals(1, $this->crawler->filter("#createdVoDetailsTable".$dateStart.$dateEnd)->count(), "missing created vo details table");

    }


    /**
     * test on oneYearVoCaMetricsReportsAction
     */
    public function testOneYearVoCaMetricsReportsAction() {
        //log in classic user
        $this->logInSimpleUser();

        //no parameter
        $this->crawler = $this->client->request('GET', '/metrics/oneYearVoCaMetricsReports');

        $this->assertEquals(404,$this->client->getResponse()->getStatusCode(), "Missing form parameters");

        //vo entity parameter
        $this->crawler = $this->client->request('GET', '/metrics/oneYearVoCaMetricsReports/vo/vo.cictest.fr/2016-04');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::oneYearVoCaMetricsReportsAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT oneYearVoCaMetricsReportsAction with POST parameters !! ");

        $this->assertEquals("User Number per vo : History",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#usersMetricsPerVoCaHistTable")->count(), "missing User Number per vo : History table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "missing User Number per vo : History breadcrumb");


        //ca entity parameter
        $this->crawler = $this->client->request('GET', '/metrics/oneYearVoCaMetricsReports/ca/%252FC%253DAM%252FO%253DArmeSFo%252FCN%253DArmeSFo%2520CA/2016-04');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::oneYearVoCaMetricsReportsAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT oneYearVoCaMetricsReportsAction with POST parameters !! ");

        $this->assertEquals("User Number per ca : History",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#usersMetricsPerVoCaHistTable")->count(), "User Number per ca : History table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "User Number per ca : History breadcrumb");

    }



    /**
     * test on oneYearDisciplineMetricsReportsAction
     */
    public function testOneYearDisciplineMetricsReportsAction() {
        //log in classic user
        $this->logInSuUser();

        //no parameter
        $this->crawler = $this->client->request('GET', '/metrics/oneYearDisciplinesMetricsReports');

        $this->assertEquals(404,$this->client->getResponse()->getStatusCode(), "Missing form parameters");

        //vo entity parameter
        $this->crawler = $this->client->request('GET', '/metrics/oneYearDisciplinesMetricsReports/discipline/Engineering and Technology/2016-04');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::oneYearDisciplinesMetricsReportsAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT oneYearDisciplinesMetricsReportsAction with POST parameters !! ");


     //   $this->assertEquals("User Number per DISCIPLINE : History",$this->crawler->filterXPath("//h2/text()")->text(),"Missing page title");

        $this->assertEquals(1, $this->crawler->filter("#users_metrics_table")->count(), "missing User Number per DISCIPLINE : History table");

        $this->assertEquals(1, $this->crawler->filter(".breadcrumb")->count(), "missing User Number per DISCIPLINE : History breadcrumb");

    }


    /**
     * test on usersSummaryAction
     */
    public function testUsersSummaryAction() {


        //no parameter
        $this->crawler = $this->client->request('GET', '/metrics/a/usersSummary');


        $this->assertEquals(200,$this->client->getResponse()->getStatusCode(), "Users summary must be accessible without discipline ID");

        //discipline id parameter
        $this->crawler = $this->client->request('GET', '/metrics/a/usersSummary/110');

        $this->assertEquals(200,$this->client->getResponse()->getStatusCode(), "Users summary muste be accessible with a discipline ID");

        $this->assertEquals(1, $this->crawler->filterXPath("//*[@id='tableUsersSummary']")->count(), "missing Users summary table");

        $this->assertEquals(1, $this->crawler->filter("#pieChartNbVo")->count(), "missing Users summary div for pie chart nb vo");

        $this->assertEquals(1, $this->crawler->filter("#pieChartNbUsers")->count(), "missing Users summary div for pie chart nb users");

    }

    /**
     * test on downloadVoUsersAction
     */
    public function testDownloadVoUsersAction() {

        $tabFilename = array("vo_users_raw.csv", "vo_users_robot.csv", "vo_users_history.csv");
        //log in classic user
        $this->logInSuUser();


        //no parameter
        $this->crawler = $this->client->request('GET', '/metrics/downloadVoUsers');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Wrong status code');
        $this->assertEquals('application/octet-stream', $this->client->getResponse()->headers->get('Content-Type'), 'Wrong content type');
     //   $this->assertContains($tabFilename[0],$this->client->getResponse()->headers->get("Content-Disposition"), "dump metrics filename expected : ".$tabFilename[0] );

        //history type parameter
        $this->crawler = $this->client->request('GET', '/metrics/downloadVoUsers/history');
        $this->assertContains($tabFilename[2],$this->client->getResponse()->headers->get("Content-Disposition"), "dump metrics filename expected : ".$tabFilename[2] );

    }

    /**
     * test on toCsvAction
     */
    public function testToCsvAction() {

        $csv = "2014-10,atlas,2746,cms,2348,vo.plgrid.pl,954,hgdemo,928,see,742,enmr.eu,649,alice,647,lhcb,573,dteam,561,cdf,464,total,18903 2015-04,atlas,2708,cms,2278,vo.plgrid.pl,1060,hgdemo,928,see,772,enmr.eu,669,alice,658,lhcb,570,dteam,584,cdf,596,total,23580 2015-10,atlas,2948,cms,2540,vo.plgrid.pl,1115,hgdemo,928,see,791,enmr.eu,705,alice,753,lhcb,598,dteam,610,cdf,605,total,24870 2016-04,atlas,000000,cms,000000,vo.plgrid.pl,000000,hgdemo,000000,see,000000,enmr.eu,000000,alice,000000,lhcb,000000,dteam,000000,cdf,000000,total,0";

        //log in classic user
        $this->logInSuUser();

        //no parameter
        $this->crawler = $this->client->request('GET', '/metrics/toCsv',
            array("csv" => $csv));

        //$this->assertContains("UsersMetrics.csv",$this->client->getResponse()->headers->get("content-disposition"), "csv metrics filename expected : UsersMetrics.csv" );
      //  $this->assertEquals('text/csv', $this->client->getResponse()->headers->get('Content-Type'), 'Wrong content type');
        $this->assertEquals($csv, $this->client->getResponse()->getContent(), "csv content must correspond to csv sent in request");


        //NO CSV CONTENT
        $csv = "";

        //log in classic user
        $this->logInSuUser();

        //no parameter
        $this->crawler = $this->client->request('GET', '/metrics/toCsv',
            array("csv" => $csv));

        $this->assertContains("Error",$this->client->getResponse()->getContent(), "no csv metrics file expected" );

    }

    /**
     * test on robotCertificate
     */
    public function testrobotCertificateSuAction(){
        $this->logInSuUser();
        $this->crawler = $this->client->request('GET', '/metrics/rbCert');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::robotCertificateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT robotCertificateAction !! ");


        $this->assertEquals(1, $this->crawler->filter("#rbCertListTable")->count(), "pb with babVoListTable");

        foreach ($this->rbCertArray as $key => $value) {
            $this->assertTrue($this->crawler->filter("#rbCertListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view voEntries has been changed... ['" . $key . "'] is no more present...");
        }


    }


    public function testrobotCertificateAction(){
        $this->logInSUUser();
        $this->crawler = $this->client->request('GET', '/metrics/rbCert');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::robotCertificateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT robotCertificateAction !! ");


        $this->assertEquals(1, $this->crawler->filter("#rbCertListTable")->count(), "pb with babVoListTable");

        foreach ($this->rbCertArray as $key => $value) {
            $this->assertTrue($this->crawler->filter("#rbCertListTable > .floating-header > tr > th:contains('" . $value . "')")->count() == 1, "The Lavoisier view voEntries has been changed... ['" . $key . "'] is no more present...");
        }


    }

    public function testrobotCertificateUserNoVOAction(){
        $this->logInSimpleUser();
        $this->crawler = $this->client->request('GET', '/metrics/rbCert');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::robotCertificateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT robotCertificateAction !! ");

        $this->assertContains("Access Denied", $this->crawler->filterXPath("//h1")->text(), "user not related to a vo can not access this page...");

    }


    public function testremoveRbCertificateNoParamAction() {
        $this->logInSuUser();

        //test with no parameter
        $this->crawler = $this->client->request('POST', '/metrics/removeRbCert');

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::removeRbCertificateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT removeRbCertificateAction !! ");

        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());

        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is NOK
        $flash = $session->getBag('flashes')->get("danger");
        $this->assertNotNull($flash, "Form submit has failed....");

    }




    public function testUpdateAndRemoveRbCertAction() {
        $this->logInSuUser();


        //create a fake robot certificate
        $rbCertificate = new VoRobotCertificate();

        $rbCertificate->setVoName($this->voTest->getName());
        $rbCertificate->setEmail("test@test.fr");
        $rbCertificate->setServiceName("test");
        $rbCertificate->setServiceUrl("http://test.fr");
        $rbCertificate->setRobotDn("/O=GRID-FR/C=FR/O=CNRS/OU=I3S/CN=Robot: VAPOR - Franck Michel");
        $rbCertificate->setUseSubProxies(0);


        try {
            /** @var $em EntityManager */
            $em = $this->container->get("doctrine")->getManager();
            $em->persist($rbCertificate);
            $em->flush();
            $em->refresh($rbCertificate);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }


        $id = $rbCertificate->getId();

        //modify the created robot certificate
        $this->crawler = $this->client->request('GET', '/metrics/rbCert');

        $form = $this->crawler->selectButton("Save Robot Certificate")->form();


        $form["vo_robot_certificate[vo_name]"] = $this->voTest->getName();
        $form["vo_robot_certificate[email]"] = "fmichel@i3s.unice.fr";
        $form["vo_robot_certificate[service_name]"] = "Vapor" ;
        $form["vo_robot_certificate[service_url]"] = "http://operations-portal.fr/vapor";
        $form["vo_robot_certificate[robot_dn]"] = "/O=GRID-FR/C=FR/O=CNRS/OU=I3S/CN=Robot: VAPOR - Franck Michel";
        $form["vo_robot_certificate[use_sub_proxies]"] = 1;
        $form["vo_robot_certificate[id]"] = $id;

        $this->crawler = $this->client->submit($form);


        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Form submit has failed....");
        $this->assertContains('The new robot certificate had been saved successfully !', $flash[0], "A flash message must have been set in session...");

        $this->client->followRedirect();

        //control that the controller method called is broadcastHomeAction
        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::robotCertificateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT robotCertificateAction with POST parameters !! ");


        //test delete the created certificate
        $this->crawler = $this->client->request('POST', '/metrics/removeRbCert', array("rbCertId" => $id));

        $this->assertEquals('AppBundle\Controller\Metrics\MetricsController::removeRbCertificateAction',
            $this->client->getRequest()->attributes->get('_controller'),
            "the controller method called is NOT removeRbCertificateAction !! ");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains("OK", $this->client->getResponse()->getContent());


        $session = $this->client->getContainer()->get('session');

        //test that flash message has been set and is OK
        $flash = $session->getBag('flashes')->get("success");
        $this->assertNotNull($flash, "Delete submit has failed....");
        $this->assertContains('Robot Certificate has been removed successfully !', $flash[0], "A flash message must have been set in session...");

    }
}