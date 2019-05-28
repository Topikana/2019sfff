<?php
/**
 * Created by PhpStorm.
 * User: tsalanon
 * Date: 25/01/2016
 * Time: 09:55
 */

namespace AppBundle\Tests\Controller\Downtime;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;


class DowntimeControllerTest extends WebTestCase
{

    /**
     * log in as simple fake user
     */
    private function logInSimpleUser()
    {
        $client= static::createClient(array(),array('HTTPS' => true));

        $user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(41);

        $session = $client->getContainer()->get('session');

        $firewall = 'secured_area';
        $attributes = [];

        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $user);


        $session->set('_security_'.$firewall, serialize($token));
        $client->getContainer()->get('security.token_storage')->setToken($token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;

    }



    public function testIndex(){

        $client = $this->logInSimpleUser();

        $crawler = $client->request('POST', '/downtimes/subscription');
        $this->assertEquals('AppBundle\Controller\Downtime\DowntimeController::indexAction', $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals($crawler->filter("#table_subscription")->count(), 1,"No table subscription");
        $this->assertEquals($crawler->filter("#user_form_name")->count(), 1);
        $this->assertEquals($crawler->filter("#user_form_email")->count(), 1);
        $this->assertEquals($crawler->filter("#user_form_dn")->count(), 1);
        $this->assertEquals($crawler->filter("#add-another-subscription")->count(), 1);
        $this->assertEquals($crawler->filter("#user_form_save")->count(), 1);

        $form = $crawler->selectButton('Save rules specifications')->form();

        $form['user_form[name]'] = 'User User';
        $form['user_form[email]'] = 'user@user.com';
        $form['user_form[dn]'] = '/C=test/O=fake/CN=User';

//        $form['user_form[subscriptions][0][rule]'] = 1;
//        $form['user_form[subscriptions][0][region]'] = 'ALL';
//        $form['user_form[subscriptions][0][site]'] = 'ALL';
//        $form['user_form[subscriptions][0][node]'] = 'ALL';
//        $form['user_form[subscriptions][0][vo]'] = 'ALL';
//
//        $form['user_form[subscriptions][0][adding]'] = 1;
//        $form['user_form[subscriptions][0][beginning]'] = 1;
//        $form['user_form[subscriptions][0][ending]'] = 1;
//        $form['user_form[subscriptions][0][isActive]'] = 1;

//        $form['user_form[subscriptions][0][communications][0][type]'] = 0;
//        $form['user_form[subscriptions][0][communications][0][value]'] = 'user@user.com';
//        $form['user_form[subscriptions][0][communications][1][type]'] = 2;
//        $form['user_form[subscriptions][0][communications][1][value]'] = 'feedURL';
//        $form['user_form[subscriptions][0][communications][2][type]'] = 3;
//        $form['user_form[subscriptions][0][communications][2][value]'] = 'feedURL';

        $client->submit($form);
        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testTimeline(){

        $client = self::createClient(array(), array('HTTPS' => true));

        $crawler = $client->request('GET', '/downtimes/a/timeline');
        $this->assertEquals('AppBundle\Controller\Downtime\DowntimeController::timelineAction', $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter("#table-timeline")->count(), 1, "No timeline table");

        $client->request('GET', '/downtimes/a/timeline?field=ngi&query=test');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/downtimes/a/timeline?field=site&query=test');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/downtimes/a/timeline?field=tier&query=test');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/downtimes/a/timeline?field=vo&query=test');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testListSites(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/sites');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $entries = json_decode($client->getResponse()->getContent(), true);

        foreach($entries as $entry){
            $this->assertArrayHasKey('PRIMARY_KEY', $entry, "Key PRIMARY_KEY does not exist");
            $this->assertArrayHasKey('NAME', $entry, "Key NAME does not exist");
            $this->assertArrayHasKey('NGI', $entry, "Key NGI does not exist");
            $this->assertArrayHasKey('COUNTRY', $entry, "Key COUNTRY does not exist");
            $this->assertArrayHasKey('PRODUCTION_INFRASTRUCTURE', $entry, "Key PRODUCTION_INFRASTRUCTURE does not exist");
            $this->assertArrayHasKey('CERTIFICATION_STATUS', $entry, "Key CERTIFICATION_STATUS does not exist");
            break;
        }

        $client->request('GET', '/downtimes/a/sites/site_name');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testListNs(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/nodesservices');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $entries = json_decode($client->getResponse()->getContent(), true);

        foreach($entries as $entry1){
            foreach ($entry1 as $entry2) {
                foreach($entry2 as $entry){
                    $this->assertArrayHasKey('HOSTNAME', $entry, "Key HOSTNAME does not exist");
                    $this->assertArrayHasKey('GOCDB_PORTAL_URL', $entry, "Key GOCDB_PORTAL_URL does not exist");
                    $this->assertArrayHasKey('SERVICE_TYPE', $entry, "Key SERVICE_TYPE does not exist");
                    $this->assertArrayHasKey('IN_PRODUCTION', $entry, "Key IN_PRODUCTION does not exist");
                    $this->assertArrayHasKey('NODE_MONITORED', $entry, "Key NODE_MONITORED does not exist");
                    $this->assertArrayHasKey('SITENAME', $entry, "Key SITENAME does not exist");
                    $this->assertArrayHasKey('ROC_NAME', $entry, "Key ROC_NAME does not exist");
                    break;
                }
            }
            break;
        }

        $client->request('GET', '/downtimes/a/nodesservices/site_name');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testListNgi(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/ngi');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testListVo(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/vo');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testTimelineDataJSON(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/timelinedata/json');
        $entries = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($client->getResponse()->isSuccessful());

        foreach($entries as $entry){
            $this->assertArrayHasKey('GOCDB_PORTAL_URL', $entry, "Key GOCDB_PORTAL_URL does not exist");
            $this->assertArrayHasKey('SEVERITY', $entry, "Key SEVERITY does not exist");
            $this->assertArrayHasKey('DESCRIPTION', $entry, "Key DESCRIPTION does not exist");
            $this->assertArrayHasKey('FORMATED_START_DATE', $entry, "Key FORMATED_START_DATE does not exist");
            $this->assertArrayHasKey('FORMATED_END_DATE', $entry, "Key FORMATED_END_DATE does not exist");
            $this->assertArrayHasKey('START_DATE', $entry, "Key START_DATE does not exist");
            $this->assertArrayHasKey('END_DATE', $entry, "Key END_DATE does not exist");
            $this->assertArrayHasKey('NGI', $entry, "Key NGI does not exist");
            $this->assertArrayHasKey('SITE', $entry, "Key SITE does not exist");
            $this->assertArrayHasKey('TIER', $entry, "Key TIER does not exist");
            $this->assertArrayHasKey('CLASSIFICATION', $entry, "Key CLASSIFICATION does not exist");
            $this->assertArrayHasKey('INSERT_DATE', $entry, "Key INSERT_DATE does not exist");
            $this->assertArrayHasKey('entities', $entry, "Key entities does not exist");
            $this->assertArrayHasKey('Endpoints', $entry, "Key Endpoints does not exist");
            break;
        }

    }

    public function testTimelineDataCSV(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/timelinedata/csv');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testFeedInfos(){

        $client = $this->logInSimpleUser();
        $client->request('GET', '/downtimes/feedinfos');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testSubscriptionsJson(){

        $client = $this->logInSimpleUser();
        $client->request('GET', '/downtimes/subscriptionsjson');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $datas = json_decode($client->getResponse()->getContent(), true);

        if(!empty($datas)){
            foreach($datas as $data){
                $this->assertArrayHasKey('region', $data, "Key region does not exist");
                $this->assertArrayHasKey('site', $data, "Key region does not exist");
                $this->assertArrayHasKey('node', $data, "Key region does not exist");
            }
        }

    }

    public function testFeedRSS(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/feed/32');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('GET', '/downtimes/a/feed/0');
        $this->assertTrue($client->getResponse()->isClientError());

    }

    public function testFeedICAL(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/feed/ical/32');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('GET', '/downtimes/a/feed/ical/0');
        $this->assertTrue($client->getResponse()->isClientError());

    }

    public function testEmail(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/email');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testTier(){

        $client = self::createClient(array(), array('HTTPS' => true));
        $client->request('GET', '/downtimes/a/tier');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }
}
