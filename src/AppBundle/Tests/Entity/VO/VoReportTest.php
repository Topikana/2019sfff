<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoReportTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoReport
     */
    private $report;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->report = Populate::createVoReport();
    }

    public function testInstance(){
        $this->assertEquals($this->datasource['r.report_body'], $this->report->getReportBody());
        $this->assertEquals($this->datasource['r.serial'], $this->report->getSerial());
        $this->assertEquals($this->datasource['r.report_body'], $this->report->getReportBody());
        $this->assertEquals($this->datasource['r.report_body'], $this->report->getReportBody());

    }
}