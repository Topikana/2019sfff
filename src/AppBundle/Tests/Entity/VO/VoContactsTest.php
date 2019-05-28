<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class  VoContactsTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoContacts
     */
    private $contact;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->contact = Populate::createVoContacts();
    }

    public function testInstance(){
        $this->assertEquals(null, $this->contact->getId());
        $this->assertEquals($this->datasource['c.first_name'], $this->contact->getFirstName());
        $this->assertEquals($this->datasource['c.last_name'], $this->contact->getLastName());
        $this->assertEquals($this->datasource['c.dn'], $this->contact->getDn());
        $this->assertEquals($this->datasource['c.email'], $this->contact->getEmail());
        $this->assertEquals($this->datasource['c.grid_body'], $this->contact->getGridBody());

    }
}
