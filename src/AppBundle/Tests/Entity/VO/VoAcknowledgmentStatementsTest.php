<?php

namespace AppBundle\Tests\Entity\VoAcknowledgmentStatements;

use AppBundle\Tests\Entity\VO\Populate;

class VoAcknowledgmentStatementsTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoAcknowledgmentStatements
     */
    private $voAcknowledgmentStatements;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->voAcknowledgmentStatements = Populate::createVoAcknowledgmentStatements();
    }

    public function testInstance(){
        $this->assertEquals(null, $this->voAcknowledgmentStatements->getId());
        $this->assertEquals($this->datasource['as.serial'], $this->voAcknowledgmentStatements->getSerial());
        $this->assertEquals($this->datasource['as.type_as'], $this->voAcknowledgmentStatements->getTypeAs());
        $this->assertEquals($this->datasource['as.grantid'], $this->voAcknowledgmentStatements->getGrantid());
        $this->assertEquals($this->datasource['as.suggested'], $this->voAcknowledgmentStatements->getSuggested());
        $this->assertEquals($this->datasource['as.relationShip'], $this->voAcknowledgmentStatements->getRelationShip());
        $this->assertEquals($this->datasource['as.publicationUrl'], $this->voAcknowledgmentStatements->getPublicationUrl());


    }
}