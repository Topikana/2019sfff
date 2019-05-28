<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\User;
use AppBundle\Tests\Entity\User\Populate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;

class DefaultControllerTest extends WebTestCase
{

    /**
     * @var $client \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client = null;

    private $datasource = array();


    public function setUp()
    {
        $this->client = static::createClient(array(),array('HTTPS' => true));

    }

    /**
     * log in as simple fake user
     */
    private function logIn(User $user)
    {

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

        $this->user->setOpRoles('cclavoisier01.in2p3.fr');


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
        $this->user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(1646);

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

    public function testFakeDn()
    {

        $user = new User("/dn",'testUser',null, array('ROLE_USER'));
        $user
            ->SetId("fakedIc")
            ->setUsername("fakeduser")
            ->setEmail("anonymous@cc.in2p3.fr");

        $user->setOpRoles('cclavoisier01');

        $this->logIn($user);

        $this->user=$user;


        $crawler = $this->client->request('GET', "/user/userInfo");


        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $attributes = [];

        $token = new SamlSpToken(array('ROLE_USER'),$firewall, $attributes, $this->user);


        $session->set('_security_'.$firewall, serialize($token));
        $this->client->getContainer()->get('security.token_storage')->setToken($token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);


        $auth_admin =  $this->client->getContainer()->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $auth_user =  $this->client->getContainer()->get('security.authorization_checker')->isGranted('ROLE_USER');



        $this->assertEquals(false, $auth_admin, "authentication admin failed");
        $this->assertEquals(true, $auth_user, "authentication user failed");
        $this->assertEquals(0, $crawler->filter('html:contains("Cyril Lorphelin")')->count(),"ne contient pas le bon dn");
    }

    public function testUserInfoSuperUser()
    {

      $this->logInSUUser();

        $crawler = $this->client->request('GET', "/user/userInfo");

        $this->assertTrue($this->client->getResponse()->isSuccessful());



        $this->assertEquals(1, $crawler->filter('html:contains("EGI")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Super User")')->count());

    }

    public function testUserInfoVoUser()
    {
       $this->logInSUUser();


        $crawler = $this->client->request('GET', "/user/userInfo");
        $this->assertTrue($this->client->getResponse()->isSuccessful());

      //  $this->assertEquals(1, $crawler->filter('h2:contains("VO")')->count());
  //      $this->assertEquals(1, $crawler->filter('html:contains("VO MANAGER")')->count());
//        $this->assertEquals(1, $crawler->filter('html:contains("Vo Deputy")')->count());
//        $this->assertEquals(1, $crawler->filter('html:contains("Vo Expert")')->count());
    }

    public function testUserInfoUser()
    {
        $this->logInSimpleUser();

        $crawler = $this->client->request('GET', "/user/userInfo");
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertEquals(0, $crawler->filter('html:contains("EGI")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Super User")')->count());
        $this->assertEquals(0, $crawler->filter('h1:contains("NGI")')->count());
        $this->assertEquals(0, $crawler->filter('h1:contains("VO")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Vo Manager")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Vo Deputy")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Vo Expert")')->count());
    }

    public function testVoAccessSU(){
        // Check if super user can access to vo aegis => *** SUPER USER ***
       $this->logInSUUser();
        $crawler = $this->client->request('GET', "/vo/update/serial/1");
        $this->assertEquals(1, $crawler->filter('h1:contains("Update VO aegis")')->count());


    }

    public function testVoAccessDeniedUser(){

      $this->logInSimpleUser();

        $crawler = $this->client->request('GET', "/vo/update/serial/13");

        $this->assertEquals(1, $crawler->filter('h1:contains("Access Denied")')->count());
    }


    //TODO A VOIR

//    public function testVoAccessUserVo(){
//        // Check if userVo can access to vo dteam =>    *** VO MANAGER ***
//        $userVO= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(44);
//
//        $this->logIn($userVO);

//        $crawler = $this->client->request('GET', "/vo/update/serial/50");
//        $this->assertEquals(1, $crawler->filter('h1:contains("Update VO dteam")')->count());
//
//        // Check if can access to vo see =>           *** VO DEPUTY ***
//        $crawler = $this->client->request('GET', "/vo/update/serial/1");
//        $this->assertEquals(1, $crawler->filter('h1:contains("Update VO aegis")')->count());
//
//        // Check if can't access to vo apesci =>        *** Without role ***
//        $crawler = $this->client->request('GET', "/vo/update/serial/5");
//        $this->assertEquals(1, $crawler->filter('h1:contains("Access Denied")')->count());
//
//        // Check if can't access to vo vo.grid.auth.gr =>         *** VO EXPERT ***
//        $crawler = $this->client->request('GET', "/vo/update/serial/50");
//        $this->assertEquals(1, $crawler->filter('h1:contains("Update VO dteam")')->count());
//    }

    public function testRefreshSessionAction() {
       $this->logInSUUser();


        $this->client->request('GET', "/user/refreshSession");
        $this->assertTrue($this->client->getResponse()->isRedirect("userInfo"));
    }
}
