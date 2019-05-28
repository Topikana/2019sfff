<?php

namespace AppBundle\Tests\Entity\User;

use AppBundle\Entity\User;
use AppBundle\Entity\User\UserProvider;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;



class UserProviderTest extends WebTestCase
{
    /**
     * @var \AppBundle\Entity\User\User
     */
    private $user;

    /**
     * @var \AppBundle\Entity\User\User
     */
    private $userGB;

    /**
     * @var \AppBundle\Entity\User\User
     */
    private $userVo;

    /**
     * @var \AppBundle\Entity\User\User
     */
    private $userTest;

    /**
     * @var \AppBundle\Entity\User\UserProvider
     */
    private $userProvider;

    /**
     * @var Client
     */
    private $client;
    private $lavoisierUrl;

    public function setUp(){
        static::bootKernel();
        $this->lavoisierUrl = static::$kernel->getContainer()->getParameter('lavoisierTestUrl');

        $users = Populate::createUser();
        $this->user  = $users["user"];
        $this->userGB  = $users["userGB"];
        $this->userVo  = $users["userVo"];
        $this->userTest = new User("CN=Cyril Lorphelin,OU=CC-IN2P3,O=CNRS,C=FR,O=GRID-FR", null,null,array("ROLE_USER") );
        $this->client = static::createClient(array(),array('HTTPS' => true));

        $this->userProvider = new UserProvider($this->client->getContainer()->get('session'), $this->lavoisierUrl,$this->client->getContainer());
    }

    private function logIn(User $user)
    {
        /**
         * @var $session Session
         */
        $session = $this->client->getContainer()->get('session');

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

//
//    public function testLoadUserByUsername(){
//        $this->assertEquals($this->userGB->isSuUser(), $this->userProvider->loadUserByUsername($this->userGB->getDn())->isSuUser());
//    }

//    public function testRefreshUser(){
//        $user= self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneById(52);
//        $user->setEmail($user->getDn());
//        $this->assertEquals($this->userGB->isSuUser(), $this->userProvider->refreshUser($user)->isSuUser());
//    }

    public function testSupportsClass(){
        $this->assertEquals(true, $this->userProvider->supportsClass('AppBundle\Entity\User'));
    }


}
