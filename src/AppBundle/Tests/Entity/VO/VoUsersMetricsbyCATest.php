<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoUsersMetricsbyCATest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoUsersMetricsbyCA
     */
    private $userMetricsCA;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->userMetricsCA = Populate::createVoUsersmetricsCA();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->userMetricsCA->getId());
        $this->assertEquals($this->datasource['umca.ca'], $this->userMetricsCA->getCa());
        $this->assertEquals($this->datasource['umca.day_date'], $this->userMetricsCA->getDayDate());
        $this->assertEquals($this->datasource['umca.nbtotal'], $this->userMetricsCA->getNbtotal());

    }
}
