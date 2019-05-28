<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoMailingListTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoMailingList
     */
    private $mailingList;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->mailingList = Populate::createVoMailingList();
    }

    public function testInstance(){
        $this->assertEquals(null, $this->mailingList->getId());
        $this->assertEquals($this->datasource["ml.admins_mailing_list"], $this->mailingList->getAdminsMailingList());
        $this->assertEquals($this->datasource["ml.operations_mailing_list"], $this->mailingList->getOperationsMailingList());
        $this->assertEquals($this->datasource["ml.user_support_mailing_list"], $this->mailingList->getUserSupportMailingList());
        $this->assertEquals($this->datasource["ml.users_mailing_list"], $this->mailingList->getUsersMailingList());
        $this->assertEquals($this->datasource["ml.security_contact_mailing_list"], $this->mailingList->getSecurityContactMailingList());
        $this->assertEquals($this->datasource["ml.user"], $this->mailingList->getUser());
        $this->assertEquals($this->datasource["ml.insert_date"], $this->mailingList->getInsertDate());
        $this->assertEquals($this->datasource["ml.serial"], $this->mailingList->getSerial());
        $this->assertEquals($this->datasource["ml.validated"], $this->mailingList->getValidated());
        $this->assertEquals($this->datasource["ml.reject_reason"], $this->mailingList->getRejectReason());
        $this->assertEquals($this->datasource["ml.notify_sites"], $this->mailingList->getNotifySites());

    }
}