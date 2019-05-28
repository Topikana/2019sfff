<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class  VoDisciplinesTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoDisciplines
     */
    private $disciplines;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->disciplines = Populate::createVoDisciplines();
    }

    public function testInstance(){
        $this->assertEquals($this->datasource['ds.discipline_id'], $this->disciplines->getDisciplineId());
        $this->assertEquals($this->datasource['ds.vo_id'], $this->disciplines->getVoId());

    }
}
