<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 29/02/16
 * Time: 14:06
 */

namespace AppBundle\Tests\Entity\BroadcastMailingLists;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Tests\Entity\Broadcast\Populate;


class BroadcastTest extends KernelTestCase {

    private  $dataSource;

    /**
     * @var \AppBundle\Entity\Broadcast\BroadcastMailingLists
     */
    private $broadcastMailingLists;


    /**
     * @var \AppBundle\Entity\Broadcast\BroadcastMessage
     */
    private $broadcastMessage;

    /**
     * @var \AppBundle\Entity\Broadcast\MailMessage
     */
    private $mailMessage;

    /**
     * Init
     *
     * datasource = references data
     *
     * set BroadcastMailingLists for test :
     *
     */
    public function setUp(){
        static::bootKernel();
        $this->lavoisierUrl = static::$kernel->getContainer()->getParameter('lavoisierUrl');

        $this->datasource = Populate::datasource();
        $this->broadcastMailingLists = Populate::createBroadcastMailingLists();
        $this->broadcastMessage = Populate::createBroadcastMessage();
        $this->mailMessage = Populate::createMailMessage();
    }


    public function  testInstanceBdMailingLists() {
        $this->assertEquals($this->datasource["broadcastMailingList.name"], $this->broadcastMailingLists->getName());
        $this->assertEquals($this->datasource["broadcastMailingList.value"], $this->broadcastMailingLists->getValue());
        $this->assertEquals($this->datasource["broadcastMailingList.user_id"], $this->broadcastMailingLists->getUserId());
        $this->assertEquals($this->datasource["broadcastMailingList.scope"], $this->broadcastMailingLists->getScope());

    }


    public function testInstanceBdMessage() {
        $this->assertEquals($this->datasource["broadcastMessage.author_email"], $this->broadcastMessage->getAuthorEmail());
        $this->assertEquals($this->datasource["broadcastMessage.author_cn"], $this->broadcastMessage->getAuthorCn());
        $this->assertEquals($this->datasource["broadcastMessage.subject"], $this->broadcastMessage->getSubject());
        $this->assertEquals($this->datasource["broadcastMessage.body"], $this->broadcastMessage->getBody());
        $this->assertEquals($this->datasource["broadcastMessage.cc"], $this->broadcastMessage->getCc());
        $this->assertEquals($this->datasource["broadcastMessage.targets_mail"], $this->broadcastMessage->getTargetsMail());
        $this->assertEquals($this->datasource["broadcastMessage.targets_label"], $this->broadcastMessage->getTargetsLabel());
        $this->assertEquals($this->datasource["broadcastMessage.targets_id"], $this->broadcastMessage->getTargetsId());
        $this->assertEquals($this->datasource["broadcastMessage.publication_type"], $this->broadcastMessage->getPublicationType());
    }

    public function  testInstanceMailMessage() {
        $this->assertEquals($this->datasource["mailMessage.message"], $this->mailMessage->getMessage());

    }
}