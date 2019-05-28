<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoUserProfileTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoUserProfile
     */
    private $userProfile;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->userProfile = Populate::createVoUserProfile();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->userProfile->getId());
        $this->assertEquals($this->datasource['up.profile'], $this->userProfile->getProfile());
        $this->assertEquals($this->datasource['up.description'], $this->userProfile->getDescription());
        $this->assertEquals($this->datasource['up.help_msg'], $this->userProfile->getHelpMsg());

    }
}
