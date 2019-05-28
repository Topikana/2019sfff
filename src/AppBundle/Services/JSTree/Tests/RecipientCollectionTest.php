<?php

require_once dirname(__FILE__) . '/../RecipientCollection.php';
require_once dirname(__FILE__) . '/../Exceptions/InvalidRootBehaviourException.php';

use AppBundle\Services\JSTree\RecipientCollection;
use AppBundle\Services\JSTree\Exceptions\InvalidRootBehaviourException;


class RecipientCollectionTest extends \PHPUnit_Framework_TestCase
{


    public function testConstruct()
    {
        $rc = new RecipientCollection('sm', 'Site Administrators');
        $this->assertEquals($rc->getName(), "Site Administrators");
        $this->assertEquals($rc->getIdentifier(), "sm");

        return $rc;
    }

    /**
     * @depends testConstruct
     */
    public function testAdd(RecipientCollection $rc)
    {

        $rc->add('45g45', 'IN2P3', 'admin@cc.in2p3.fr');
        $recipients = $rc->getRecipients();

        $this->assertEquals($recipients['sm_45g45']['label'], 'IN2P3');
        $this->assertEquals($rc->getRecipientLabel('45g45'), 'IN2P3');
        $this->assertEquals($rc->getRecipientMail('45g45'), 'admin@cc.in2p3.fr');
        $this->assertEquals($rc->getRecipientLabel('foo'), null);
        $this->assertEquals($rc->getRecipientMail('bar'), null);

    }


    /**
     * @depends testConstruct
     * @expectedException JSTree\Exceptions\InvalidRootBehaviourException
     */
    public function testAddBehaviourException(RecipientCollection $rc)
    {
        $rB = $rc->getRootBehaviour();
        $this->assertEquals($rB, null);
        $rc->addRootBehaviour('foo@bar.com', 'bar');
    }


    /**
     * @depends testConstruct
     */
    public function testAddBehaviour(RecipientCollection $rc)
    {

        $rc->addRootBehaviour('foo@bar.com');
        $rB = $rc->getRootBehaviour();
        $this->assertEquals($rB[RecipientCollection::DATA_TYPE_LABEL], \JSTree\RecipientCollection::ROOT_AS_MAILING_LIST);

    }

    /**
     * @depends testConstruct
     */
    public function testCheckItem(RecipientCollection $rc)
    {
        $this->assertEquals($rc->matchItem('45g45', RecipientCollection::DATA_TYPE_ID), 'sm_45g45');
        $this->assertEquals($rc->matchItem('45g45', RecipientCollection::DATA_TYPE_EMAIL), 'admin@cc.in2p3.fr');
        $this->assertEquals($rc->matchItem('45g45', RecipientCollection::DATA_TYPE_LABEL), 'IN2P3');
        $this->assertEquals($rc->matchItem('45g45', 'foo'), 'ERR:sm_45g45');

    }


}