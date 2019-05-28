<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class DisciplinesTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\Disciplines
     */
    private $disciplines;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->disciplines = Populate::createDisciplines();
    }

    public function testInstance(){
        $this->assertEquals($this->datasource['disciplines.id'], $this->disciplines->getDisciplineId());
        $this->assertEquals($this->datasource['disciplines.label'], $this->disciplines->getDisciplineLabel());

    }
}
