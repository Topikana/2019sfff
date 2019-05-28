<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoVomsGroupTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoVomsGroup
     */
    private $voVomsGroup;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->voVomsGroup = Populate::createVoVomsGroup();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->voVomsGroup->getId());
        $this->assertEquals($this->datasource['vg.group_role'], $this->voVomsGroup->getGroupRole());
        $this->assertEquals($this->datasource['vg.description'], $this->voVomsGroup->getDescription());
        $this->assertEquals($this->datasource['vg.is_group_used'], $this->voVomsGroup->getIsGroupUsed());
        $this->assertEquals($this->datasource['vg.group_type'], $this->voVomsGroup->getGroupType());
        $this->assertEquals($this->datasource['vg.allocated_ressources'], $this->voVomsGroup->getAllocatedRessources());
        $this->assertEquals($this->datasource['vg.serial'], $this->voVomsGroup->getSerial());

    }
}