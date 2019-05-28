<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoRegexpTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoRegexp
     */
    private $regexp;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->regexp = Populate::createVoRegexp();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->regexp->getId());
        $this->assertEquals($this->datasource['rxp.description'], $this->regexp->getDescription());
        $this->assertEquals($this->datasource['rxp.regexpression'], $this->regexp->getRegexpression());

    }
}
