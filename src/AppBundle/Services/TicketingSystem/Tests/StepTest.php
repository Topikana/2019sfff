<?php

require_once dirname(__FILE__) . '/../Workflow/Step.php';

use TicketingSystem\Workflow\Step;


class StepTest extends \PHPUnit_Framework_TestCase
{
    public $step = array(
        'new' => array
        (
            'EndDate' => array
            (
                'id' => 'EndDate',
                'label' => 'essaie 1234',
                'order' => 1000
            ),
            'Priority' => array
            (
                'id' => 'Priority',
                'value' => 'Low',
                'visibility' => false,
                'order' => 400
            ),
            'Modifier' => array
            (
                'id' => 'Modifier',
                'label' => 'Submitter',
                'visibility' => false

            ),
            'Comment' => array
            (
                'id' => 'Comment',
                'label' => 'Body',
                'required' => 1,
                'value' => 'Dear Site Admins and NGI Helpdesk, We have detected a problem at +C.site_name+.
Failure detected on : +C.test_date+ View failure history and details on NAGIOS portal :  +C.test_history+ View some details about the test description : +C.test_help+
Additional comments or logs for alarm -------------------------------------- Could you please have a look ? Thank you +T.Modifier+ - +C.ngi_name+ NGI'
            ),
            'ResponsibleUnit' => array
            (
                'id' => 'ResponsibleUnit',
                'label' => 'Assign to',
                'value' => '+C.site_name+'
            ),
            'Status' => array
            (
                'id' => 'Status',
                'visibility' => 1,
                'value' => 'Opened'
            ),
            'Subject' => array
            (
                'id' => 'Subject',
                'value' => 'NAGIOS  *+C.test_name+* failed on +C.node+@+C.site_name+',
                'required' => 1
            ),
            'CarbonCopy' => array
            (
                'id' => 'CarbonCopy',
                'factory_param' => '+C.carbon_copy_param+',
                'order' => 3
            ),
        ),
        'esc3' => array(
            '0' => array
            (
                'id' => 'EndDate',
                'label' => 'Should be finish on',
                'required' => false,
                'visibility' => 1
            ),
            '1' => array
            (
                'id' => 'Priority',
                'value' => 'Low',
                'visibility' => false,
                'required' => false
            ),
            '2' => array
            (
                'id' => 'Modifier',
                'visibility' => false,
                'label' => 'Submitter',
                'required' => false
            ),
            '3' => array
            (
                'id' => 'Comment',
                'value' => 'Dear COds... Admins and NGI Helpdesk, ..... +T.Modifier+ - +C.ngi_name+ NGI',
                'required' => '1',
                'label' => "Body",
                'visibility' => '1'
            ),
            '4' => array
            (
                'id' => "ResponsibleUnit",
                'value' => '+C.site_name+',
                'label' => "Assign to",
                'required' => false,
                'visibility' => 1
            ),
            '5' => array
            (
                'id' => 'Status',
                'value' => 'Opened',
                'visibility' => false,
                'required' => false,
            ),
            '6' => array
            (
                'id' => "Subject",
                'value' => 'NAGIOS  *+C.test_name+* failed on +C.node+@++C.site_name+',
                'required' => 1,
                'visibility' => 1,
            ),
            '7' => array
            (
                'id' => 'CarbonCopy',
                'factory_param' => '+C.carbon_copy_param+',
                'required' => false,
                'visibility' => 1
            )));


    public function testConstruct()
    {
        $s = new Step($this->step['new']);
        $this->assertEquals($s->getArrayCopy(), $this->step['new']);

        return $s;
    }

    /**
     * @depends testConstruct
     */
    public function testHide(Step $s)
    {
        $this->assertEquals($s->isItemVisible('Status'), true);

        // hide all items
        $s->hideItems(array());


        $this->assertEquals($s->isItemVisible('Status'), false);

    }

    /**
     * @depends testConstruct
     */
    public function testSort(Step $s)
    {
        $item1_array = $s->getArrayCopy();
        $item1 = array_pop($item1_array);
        $s->sort();
        $item2_array = $s->getArrayCopy();
        $item2 = array_pop($item2_array);
        $this->assertNotEquals($item1, $item2);
    }


    /**
     * @depends testConstruct
     */
    public function testGetSet(Step $s)
    {
        $this->assertEquals($s->getItemValue('foo','required'), null);
        $this->assertEquals($s->getItemValue('Modifier','bar'), null);
        $this->assertEquals($s->isItemVisible('Modifier'), false);
    }


}