<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoScopeTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoScope
     */
    private $scope;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->scope = Populate::createVoScope();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->scope->getId());
        $this->assertEquals($this->datasource['s.scope'], $this->scope->getScope());
        $this->assertEquals($this->datasource['s.roc'], $this->scope->getRoc());
        $this->assertEquals($this->datasource['s.decommissioned'], $this->scope->getDecommissioned());

    }
}