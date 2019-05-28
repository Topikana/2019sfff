<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoYearlyValidationTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoYearlyValidation
     */
    private $voYearlyValidation;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->voYearlyValidation = Populate::createVoYearlyValidation();
    }

    public function testInstance(){

        $this->assertEquals(null, $this->voYearlyValidation->getId());
        $this->assertEquals($this->datasource['vy.serial'], $this->voYearlyValidation->getSerial());
        $this->assertEquals($this->voYearlyValidation->getDateValidation(), $this->voYearlyValidation->getDateValidation());
        $this->assertEquals($this->datasource['vy.date_last_email_sending'], $this->voYearlyValidation->getDateLastEmailSending());

    }
}