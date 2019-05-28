<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoTestsTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoTests
     */
    private $tests;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->tests = Populate::createVoTests();
    }

    public function testInstance(){

        $this->assertEquals($this->datasource['t.roc_name'], $this->tests->getRocName());
        $this->assertEquals($this->datasource['t.test_name'], $this->tests->getTestName());

    }
}
