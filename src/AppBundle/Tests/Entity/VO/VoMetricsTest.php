<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoMetricsTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoMetrics
     */
    private $metrics;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->metrics = Populate::createVoMetrics();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->metrics->getId());
        $this->assertEquals($this->datasource['m.nb_vo'], $this->metrics->getNbVo());
        $this->assertEquals($this->datasource['m.nb_added'], $this->metrics->getNbAdded());
        $this->assertEquals($this->datasource['m.nb_removed'], $this->metrics->getNbRemoved());
        $this->assertEquals($this->datasource['m.nb_inter_vo'], $this->metrics->getNbInterVo());
        $this->assertEquals($this->datasource['m.nb_inter_added'], $this->metrics->getNbInterAdded());
        $this->assertEquals($this->datasource['m.nb__inter_removed'], $this->metrics->getNbInterRemoved());
        $this->assertEquals($this->datasource['m.day_date'], $this->metrics->getDayDate());

    }
}
