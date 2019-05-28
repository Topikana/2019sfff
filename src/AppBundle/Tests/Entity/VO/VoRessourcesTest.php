<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoRessourcesTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoRessources
     */
    private $ressources;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->ressources = Populate::createVoRessources();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->ressources->getId());
        $this->assertEquals($this->datasource['r.serial'], $this->ressources->getSerial());
        $this->assertEquals($this->datasource['r.insert_date'], $this->ressources->getInsertDate());
        $this->assertEquals($this->datasource['r.ram386'], $this->ressources->getRam386());
        $this->assertEquals($this->datasource['r.ram64'], $this->ressources->getRam64());
        $this->assertEquals($this->datasource['r.job_scratch_space'], $this->ressources->getJobScratchSpace());
        $this->assertEquals($this->datasource['r.job_max_cpu'], $this->ressources->getJobMaxCpu());
        $this->assertEquals($this->datasource['r.job_max_wall'], $this->ressources->getJobMaxWall());
        $this->assertEquals($this->datasource['r.other_requirements'], $this->ressources->getOtherRequirements());
        $this->assertEquals($this->datasource['r.cpu_core'], $this->ressources->getCpuCore());
        $this->assertEquals($this->datasource['r.vm_ram'], $this->ressources->getVmRam());
        $this->assertEquals($this->datasource['r.storage_size'], $this->ressources->getStorageSize());
        $this->assertEquals($this->datasource['r.public_ip'], $this->ressources->getPublicIp());
        $this->assertEquals($this->datasource['r.user'], $this->ressources->getUser());
        $this->assertEquals($this->datasource['r.validated'], $this->ressources->getValidated());
        $this->assertEquals($this->datasource['r.reject_reason'], $this->ressources->getRejectReason());
        $this->assertEquals($this->datasource['r.notify_sites'], $this->ressources->getNotifySites());
        $this->assertEquals($this->datasource['r.cvmfs'], $this->ressources->getCvmfs());

    }
}