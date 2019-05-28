<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoUsersTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoUsers
     */
    private $user;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->user = Populate::createVoUsers();
    }

    public function testInstance(){

        $this->assertEquals($this->datasource['u.dn'], $this->user->getDn());
        $this->assertEquals($this->datasource['u.vo'], $this->user->getVo());
        $this->assertEquals($this->datasource['u.email'], $this->user->getEmail());
        $this->assertEquals($this->datasource['u.uservo'], $this->user->getUservo());
        $this->assertEquals($this->datasource['u.ca'], $this->user->getCa());
        $this->assertEquals($this->datasource['u.urlvo'], $this->user->getUrlvo());
        $this->assertEquals($this->datasource['u.last_update'], $this->user->getLastUpdate());
        $this->assertEquals($this->datasource['u.first_update'], $this->user->getFirstUpdate());

    }
}
