<?php

namespace AppBundle\Tests\Entity\Downtime;

use Doctrine\Common\Collections\ArrayCollection;

class UserTest extends \PHPUnit\Framework\TestCase
{

    private $datasource;
    /**
     * @var \AppBundle\Entity\Downtime\User
     */
    private $user;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->user = Populate::createUser();

    }

    public function testInstance(){

        $this->assertEquals($this->datasource['user.name'], $this->user->getName());
        $this->assertEquals($this->datasource['user.dn'], $this->user->getDn());
        $this->assertEquals($this->datasource['user.email'], $this->user->getEmail());

    }

    public function testUserSubscription(){

        $subscription1 = Populate::createSubscription();
        $subscription2 = Populate::createSubscription();
        $subscription3 = Populate::createSubscription();

        $subscriptions = new ArrayCollection();
        $subscriptions->add($subscription1);
        $subscriptions->add($subscription2);
        $this->user->setSubscriptions($subscriptions);
        $this->assertEquals($this->user->getSubscriptions(), $subscriptions);

        $this->user->addSubscription($subscription3);
        $subscriptions->add($subscription3);
        $this->assertEquals($this->user->getSubscriptions(), $subscriptions);


        $this->user->removeSubscription($subscription2);
        $subscriptions->removeElement($subscription2);
        $this->assertEquals($this->user->getSubscriptions(), $subscriptions);


    }

}
