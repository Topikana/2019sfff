<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class  VoContactHasProfilesTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoContactHasProfile
     */
    private $contactHP;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->contactHP = Populate::createVoContactHasProfile();
    }

    public function testInstance(){
        $this->assertEquals($this->datasource['chp.serial'], $this->contactHP->getSerial());
        $this->assertEquals($this->datasource['chp.user_profile_id'], $this->contactHP->getUserProfileId());
        $this->assertEquals($this->datasource['chp.contact_id'], $this->contactHP->getContactId());
        $this->assertEquals($this->datasource['chp.comment'], $this->contactHP->getComment());

    }
}