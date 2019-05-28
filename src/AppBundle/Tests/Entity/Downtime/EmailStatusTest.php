<?php

namespace AppBundle\Tests\Entity\Downtime;

use Doctrine\Common\Collections\ArrayCollection;

class EmailStatusTest extends \PHPUnit\Framework\TestCase
{

    private $datasource;
    /**
     * @var \AppBundle\Entity\Downtime\EmailStatus
     */
    private $emailStatus;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->emailStatus = Populate::createEmailStatus();

    }

    public function testInstance(){

        $this->assertEquals($this->datasource["email_status.primary_key"], $this->emailStatus->getPrimaryKey());
        $this->assertEquals($this->datasource["email_status.subscription_id"], $this->emailStatus->getSubscriptionId());
        $this->assertEquals($this->datasource["email_status.email"], $this->emailStatus->getEmail());
        $this->assertEquals($this->datasource["email_status.adding_sent"], $this->emailStatus->getAddingSent());
        $this->assertEquals($this->datasource["email_status.beginning_sent"], $this->emailStatus->getBeginningSent());
        $this->assertEquals($this->datasource["email_status.ending_sent"], $this->emailStatus->getEndingSent());

    }

}
