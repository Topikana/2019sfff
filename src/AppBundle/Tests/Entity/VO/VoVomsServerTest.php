<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoVomsServerTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoVomsServer
     */
    private $voVomsServer;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->voVomsServer = Populate::createVoVomsServer();
    }

    public function testInstance(){

        $this->assertEquals($this->datasource['vs.serial'], $this->voVomsServer->getSerial());
        $this->assertEquals($this->datasource['vs.hostname'], $this->voVomsServer->getHostname());
        $this->assertEquals($this->datasource['vs.https_port'], $this->voVomsServer->getHttpsPort());
        $this->assertEquals($this->datasource['vs.vomses_port'], $this->voVomsServer->getVomsesPort());
        $this->assertEquals($this->datasource['vs.is_vomsadmin_server'], $this->voVomsServer->getIsVomsadminServer());
        $this->assertEquals($this->datasource['vs.is_vomsadmin_server'], $this->voVomsServer->getIsVomsadminServer());
        $this->assertEquals($this->datasource['vs.members_list_url'], $this->voVomsServer->getMembersListUrl());

    }
}