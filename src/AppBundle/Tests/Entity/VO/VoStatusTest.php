<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoStatusTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoStatus
     */
    private $status;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->status = Populate::createVoStatus();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->status->getId());
        $this->assertEquals($this->datasource['status.status'], $this->status->getStatus());
        $this->assertEquals($this->datasource['status.description'], $this->status->getDescription());
    }
}
