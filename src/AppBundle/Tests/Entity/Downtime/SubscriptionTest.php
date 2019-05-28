<?php

namespace AppBundle\Tests\Entity\Downtime;



use Doctrine\Common\Collections\ArrayCollection;

class SubscriptionTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;

    /**
     * @var \AppBundle\Entity\Downtime\Subscription
     */
    private $subscription;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->subscription = Populate::createSubscription();

    }

    public function testInstance(){

        $this->assertEquals($this->datasource['subscription.isActive'], $this->subscription->getIsActive());
        $this->assertEquals($this->datasource['subscription.rule'], $this->subscription->getRule());
        $this->assertEquals($this->datasource['subscription.region'], $this->subscription->getRegion());
        $this->assertEquals($this->datasource['subscription.site'], $this->subscription->getSite());
        $this->assertEquals($this->datasource['subscription.node'], $this->subscription->getNode());
        $this->assertEquals($this->datasource['subscription.vo'], $this->subscription->getVo());
        $this->assertEquals($this->datasource['subscription.adding'], $this->subscription->isAdding());
        $this->assertEquals($this->datasource['subscription.beginning'], $this->subscription->isBeginning());
        $this->assertEquals($this->datasource['subscription.ending'], $this->subscription->isEnding());

    }

    public function testSubscriptionCommunications(){

        $communication1 = Populate::createCommunication();
        $communication2 = Populate::createCommunication();
        $communication3 = Populate::createCommunication();

        $communications = new ArrayCollection();
        $communications->add($communication1);
        $communications->add($communication2);
        $this->subscription->setCommunications($communications);
        $this->assertEquals($communications,$this->subscription->getCommunications());

        $communications->add($communication3);
        $this->subscription->addCommunication($communication3);
        $this->assertEquals($communications,$this->subscription->getCommunications());

        $communications->removeElement($communication2);
        $this->subscription->removeCommunication($communication2);

    }

    public function testSubscriptionUser(){

        $user = Populate::createUser();
        $this->subscription->setUser($user);
        $this->assertEquals($user, $this->subscription->getUser());

    }
}
