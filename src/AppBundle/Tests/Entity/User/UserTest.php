<?php

namespace AppBundle\Tests\Entity\User;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;



class UserTest extends WebTestCase
{

    private $datasource;
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

    private $lavoisierUrl;

    /**
     * Init
     *
     * datasource = references data
     *
     * set Users for test :
     * user = user not Grid Body
     * userGB = user Grid Body
     *
     */
    public function setUp(){

        $client = static::createClient(array(),array('HTTPS' => true));

        $container = $client->getContainer();
        $this->lavoisierUrl = $container->getParameter('lavoisierTestUrl');

        $this->datasource = Populate::datasource();
        $users = Populate::createUser();
        $this->user  = $users["user"];
        $this->userGB  = $users["userGB"];
        $this->userVo  = $users["userVo"];
        $this->userTest = new User($this->datasource['user.dn'], null,null,array("ROLE_USER"),array());
    }

    public function testInstance(){
        $this->assertEquals($this->datasource['user.name'], $this->userGB->getUsername());
        $this->assertEquals($this->datasource['user.dn'], $this->userGB->getDn());
        $this->assertEquals($this->datasource['user.opRoles'], $this->userGB->getOpRoles());

        $this->assertEquals("Marc Hufschmitt", $this->user->getUsername());
        $this->assertEquals("/O=GRID-FR/C=FR/O=CNRS/OU=IPGP/CN=Marc Hufschmitt", $this->user->getDn());
        $this->assertEquals(array(), $this->user->getOpRoles()); // roles not set


    }

    /*
     * @attributes: lavoisier => get roles in lavoisier (lavoisier test)
     * if error, check roles for "/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Cyril Lorphelin" in Populate and "OPSCORE_users_agg" view
     */
    public function testSetRoles(){
        $this->userGB->setOpRoles($this->lavoisierUrl);
        $this->assertEquals(sort($this->datasource['user.opRoles']["ngi"]), sort($this->userGB->getOpRoles()["ngi"]));
        $this->assertEquals(sort($this->datasource['user.opRoles']["site"]), sort($this->userGB->getOpRoles()["site"]));
        $this->assertEquals(sort($this->datasource['user.opRoles']["project"]), sort($this->userGB->getOpRoles()["project"]));

    }

    public function testIsSuUser(){
        $this->assertEquals(true, $this->userGB->isSuUser());
        $this->assertEquals(false, $this->user->isSuUser());
    }

    public function testCanModifyVO(){

        $this->assertEquals(true, $this->userGB->canModifyVO("voNameManager")); // true => GRID BODY
        $this->assertEquals(false, $this->user->canModifyVO("voNameManager")); // false => not Vo Manager
        $this->assertEquals(false, $this->user->canModifyVO("voNameDeputy")); // false => not Vo Deputy

        $this->assertEquals(true, $this->userVo->canModifyVO("atlas")); // true => user is Vo Manager
        $this->assertEquals(true, $this->userVo->canModifyVO("aegis")); // true => user is Vo Deputy

    }


}
