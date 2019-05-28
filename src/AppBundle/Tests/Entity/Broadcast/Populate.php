<?php

namespace AppBundle\Tests\Entity\Broadcast;

use AppBundle\Entity\Broadcast\BroadcastMailingLists;
use AppBundle\Entity\Broadcast\BroadcastMessage;
use AppBundle\Entity\Broadcast\MailMessage;

class Populate extends \PHPUnit\Framework\TestCase
{

    /**
     * @return BroadcastMailingLists
     */
    public static function createBroadcastMailingLists(){

        $data = Populate::datasource();

        $bdMailingLists = new BroadcastMailingLists();
        $bdMailingLists->setName($data["broadcastMailingList.name"]);
        $bdMailingLists->setValue($data["broadcastMailingList.value"]);
        $bdMailingLists->setUserId($data["broadcastMailingList.user_id"]);
        $bdMailingLists->setScope($data["broadcastMailingList.scope"]);

        return $bdMailingLists;

    }

    /**
     * @return BroadcastMessage
     */
    public static function createBroadcastMessage(){

        $data = Populate::datasource();

        $bdMessage = new BroadcastMessage();

        $bdMessage->setAuthorEmail($data["broadcastMessage.author_email"]);
        $bdMessage->setAuthorCn($data["broadcastMessage.author_cn"]);
        $bdMessage->setSubject($data["broadcastMessage.subject"]);
        $bdMessage->setCc($data["broadcastMessage.cc"]);
        $bdMessage->setBody($data["broadcastMessage.body"]);
        $bdMessage->setTargetsMail($data["broadcastMessage.targets_mail"]);
        $bdMessage->setTargetsLabel($data["broadcastMessage.targets_label"]);
        $bdMessage->setTargetsId($data["broadcastMessage.targets_id"]);
        $bdMessage->setPublicationType($data["broadcastMessage.publication_type"]);

        return $bdMessage;

    }


    /**
     * @return MailMessage
     */
    public static function createMailMessage(){

        $data = Populate::datasource();

        $mailMessage = new MailMessage();

        $mailMessage->setMessage($data["mailMessage.message"]);


        return $mailMessage;

    }

    /**
     * data source for populate
     * @return array
     */
    public static function datasource(){

        $data = [];

        //broadcastmailinglists
        $data["broadcastMailingList.name"] = "Regional Admin of Operations Portal";
        $data["broadcastMailingList.value"] = "ops-portal@mailman.egi.eu";
        $data["broadcastMailingList.user_id"] = "/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Cyril Lorphelin";
        $data["broadcastMailingList.scope"] = "private";

        //broadcastmessage
        $data["broadcastMessage.author_email"] = "cic-information@cc.in2p3.fr";
        $data["broadcastMessage.author_cn"] = "Laure Souai";
        $data["broadcastMessage.subject"] = "test of vo supporting sites";
        $data["broadcastMessage.body"] = "test of vo supporting sites test of vo supporting sites test of vo supporting sites test of vo supporting sitestest of vo supporting sites test of vo supporting sites";
        $data["broadcastMessage.targets_mail"] = 'a:5:{s:22:"eugrid.support@sara.nl";s:26:"VO supporting sites/vlemed";s:24:"grid.support@surfsara.nl";s:26:"VO supporting sites/vlemed";s:26:"eugrid.support@surfsara.nl";s:26:"VO supporting sites/vlemed";s:23:"grid.sysadmin@nikhef.nl";s:26:"VO supporting sites/vlemed";s:23:"grid-beheer@list.rug.nl";s:26:"VO supporting sites/vlemed";}';
        $data["broadcastMessage.targets_label"] = 'a:1:{s:19:"VO supporting sites";a:2:{s:4:"item";a:1:{i:0;s:6:"vlemed";}s:5:"label";s:19:"VO supporting sites";}}';
        $data["broadcastMessage.targets_id"] = 'a:1:{s:2:"vs";a:1:{i:0;s:9:"vs_vlemed";}}';
        $data["broadcastMessage.publication_type"] = 1;
        $data["broadcastMessage.cc"] = "cic-information@cc.in2p3.fr,test@test.fr,testing@in2p3.fr";

        //maimmessage
        $data["mailMessage.message"] = "test of vo supporting sites test of vo supporting sites test of vo supporting sites test of vo supporting sitestest of vo supporting sites test of vo supporting sites";

        return $data;
    }


}
