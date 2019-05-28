<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoUsersHistoryTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoUsersHistory
     */
    private $userHistory;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->userHistory = Populate::createVoUsersHistory();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->userHistory->getId());
        $this->assertEquals($this->datasource['uh.vo'], $this->userHistory->getVo());
        $this->assertEquals($this->datasource['uh.u_month'], $this->userHistory->getUMonth());
        $this->assertEquals($this->datasource['uh.u_year'], $this->userHistory->getUYear());
        $this->assertEquals($this->datasource['uh.nbtotal'], $this->userHistory->getNbtotal());
        $this->assertEquals($this->datasource['uh.nbremoved'], $this->userHistory->getNbremoved());
        $this->assertEquals($this->datasource['uh.nbadded'], $this->userHistory->getNbadded());

    }
}
