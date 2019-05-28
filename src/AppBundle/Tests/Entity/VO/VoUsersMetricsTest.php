<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoUsersMetricsTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoUsersMetrics
     */
    private $userMetrics;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->userMetrics = Populate::createVoUsersmetrics();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->userMetrics->getId());
        $this->assertEquals($this->datasource['um.vo'], $this->userMetrics->getVo());
        $this->assertEquals($this->datasource['um.discipline'], $this->userMetrics->getDiscipline());
        $this->assertEquals($this->datasource['um.day_date'], $this->userMetrics->getDayDate());
        $this->assertEquals($this->datasource['um.nbtotal'], $this->userMetrics->getNbtotal());

    }
}
