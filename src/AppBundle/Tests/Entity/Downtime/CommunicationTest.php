<?php

namespace AppBundle\Tests\Entity\Downtime;


class CommunicationTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\Downtime\Communication
     */
    private $communication;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->communication = Populate::createCommunication();

    }

    public function testInstance(){

        $this->assertEquals($this->datasource["communication.type"], $this->communication->getType());
        $this->assertEquals($this->datasource["communication.value"], $this->communication->getValue());

    }

    public function testCommunicationSubscription(){

        $subscription = Populate::createSubscription();
        $this->communication->setSubscription($subscription);
        $this->assertEquals($subscription,$this->communication->getSubscription());

    }
}
