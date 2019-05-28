<?php

namespace AppBundle\Tests\Entity\Downtime;

use AppBundle\Entity\Downtime\Communication;
use AppBundle\Entity\Downtime\EmailStatus;
use AppBundle\Entity\Downtime\Subscription;
use AppBundle\Entity\Downtime\User;

class Populate extends \PHPUnit\Framework\TestCase
{

    /**
     * @return User
     */
    public static function createUser(){

        $data = Populate::datasource();

        $user = new User($data["user.dn"], array("ROLE_USER"));
        $user->setName($data["user.name"]);
        $user->setEmail($data["user.email"]);
        $user->setDn($data["user.dn"]);

        return $user;

    }

    /**
     * @return Subscription
     */
    public static function createSubscription(){

        $data = Populate::datasource();

        $subscription = new Subscription();
        $subscription->setIsActive($data["subscription.isActive"]);
        $subscription->setRule($data["subscription.rule"]);
        $subscription->setRegion($data["subscription.region"]);
        $subscription->setSite($data["subscription.site"]);
        $subscription->setNode($data["subscription.node"]);
        $subscription->setVo($data["subscription.vo"]);
        $subscription->setAdding($data["subscription.adding"]);
        $subscription->setBeginning($data["subscription.beginning"]);
        $subscription->setEnding($data["subscription.ending"]);

        return $subscription;

    }

    /**
     * @return Communication
     */
    public static function createCommunication(){
        $data = Populate::datasource();

        $communication = new Communication();
        $communication->setType($data["communication.type"]);
        $communication->setValue($data["communication.value"]);

        return $communication;

    }

    /**
     * @return EmailStatus
     */
    public static function createEmailStatus(){
        $data = Populate::datasource();

        $emailStatus = new EmailStatus();
        $emailStatus->setPrimaryKey($data["email_status.primary_key"]);
        $emailStatus->setSubscriptionId($data["email_status.subscription_id"]);
        $emailStatus->setEmail($data["email_status.email"]);
        $emailStatus->setAddingSent($data["email_status.adding_sent"]);
        $emailStatus->setBeginningSent($data["email_status.beginning_sent"]);
        $emailStatus->setEndingSent($data["email_status.ending_sent"]);

        return $emailStatus;

    }

    /**
     * data source for populate
     * @return array
     */
    public static function datasource(){

        $data = [];

        $data["user.name"] = "Thibaut Salanon";
        $data["user.email"] = "thibaut.salanon@cc.in2p3.fr";
        $data["user.dn"] = "/C=FR/O=CNRS/OU=USR6402/CN=Thibaut Salanon/emailAddress=thibaut.salanon@cc.in2p3.fr";

        $data["subscription.isActive"] = true;
        $data["subscription.rule"] = "1";
        $data["subscription.region"] = "NGI_FRANCE";
        $data["subscription.site"] = "site";
        $data["subscription.node"] = "node";
        $data["subscription.vo"] = "vo";
        $data["subscription.adding"] = true;
        $data["subscription.beginning"] = true;
        $data["subscription.ending"] = true;

        $data["communication.type"] = 0;
        $data["communication.value"] = "mail@mail.com";

        $data["email_status.primary_key"] = "1234566789";
        $data["email_status.subscription_id"] = "185";
        $data["email_status.email"] = "thibaut.salanon@cc.in2p3.fr";
        $data["email_status.adding_sent"] = true;
        $data["email_status.beginning_sent"] = true;
        $data["email_status.ending_sent"] = true;

        return $data;
    }
}
