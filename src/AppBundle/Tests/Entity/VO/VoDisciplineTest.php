<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class  VoDisciplineTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoDiscipline
     */
    private $discipline;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->discipline = Populate::createVoDiscipline();
    }

    public function testInstance(){
        $this->assertEquals(null, $this->discipline->getId());
        $this->assertEquals($this->datasource['d.description'], $this->discipline->getDescription());
        $this->assertEquals($this->datasource['d.discipline'], $this->discipline->getDiscipline());

    }
}
