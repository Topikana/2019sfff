<?php

require_once dirname(__FILE__) . '/../Exceptions/WorkflowException.php';
require_once dirname(__FILE__) . '/../Workflow/Workflow.php';
require_once dirname(__FILE__) . '/../Workflow/Step.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IEntries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Entries.php';
require_once dirname(__FILE__) . '/../Ticket/ArrayFields.php';
require_once dirname(__FILE__) . '/../Ticket/GgusFields.php';
require_once dirname(__FILE__) . '/../Ticket/GgusObject.php';
require_once dirname(__FILE__) . '/../Ticket/ITicket.php';
require_once dirname(__FILE__) . '/../Ticket/GgusObject.php';
require_once dirname(__FILE__) . '/../Ticket/GgusTicket.php';
require_once dirname(__FILE__) . '/../Ticket/OpsTicket.php';

use TicketingSystem\Workflow\Workflow;
use TicketingSystem\Workflow\Step;
use TicketingSystem\Ticket\GgusFields;
use TicketingSystem\Ticket\OpsTicket;


class WorkflowTest extends \PHPUnit_Framework_TestCase
{

    public $schema = array(
        'new' => array('label' => 'E1', "next" => array('esc1' => '+1', 'upd' => '=', 'close' => '0')),
        'esc1' => array('label' => 'E2', "next" => array('esc2' => '+1', 'upd' => '=', 'close' => '0')),
        'esc2' => array('label' => 'E3', "next" => array('esc1' => '-1', 'esc3' => '+1', 'upd' => '=', 'close' => '0')),
        'esc3' => array('label' => 'E4', "next" => array('upd' => '=')));
    public $step = array(
        'new' => array
        (
            'EndDate' => array
            (
                'id' => 'EndDate',
                'label' => 'essaie 1234',
            ),
            'Priority' => array
            (
                'id' => 'Priority',
                'value' => 'Low',
                'visibility' => false
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
                'factory_param' => '+C.carbon_copy_param+'
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

    public $cMap = array(

        'Login' => 'GHD_Last_Login',
        'Id' => 'GHD_Request_ID',
        'Type' => 'GHD_TicketType',
        'Routing' => 'GHD_RoutingType',
        'Author' => 'GHD_Submitter',
        'Modifier' => 'GHD_Last_Modifier',
        'ResponsiblePeople' => 'GHD_Assigned_To',
        'Subject' => 'GHD_Subject',
        'Description' => 'GHD_Description',
        'Comment' => 'GHD_Public_Diary',
        'InternalComment' => 'GHD_Internal_Diary',
        'Solution' => 'GHD_Detailed_Solution',
        'Status' => 'GHD_Status',
        'MetaStatus' => 'GHD_Meta_Status',
        'Priority' => 'GHD_Priority',
        'CarbonCopy' => 'GHD_Carbon_Copy',
        'Involve' => 'GHD_Involve',
        'NotificationStrategy' => 'GHD_User_Notification',
        'ProblemType' => 'GHD_ProblemType',
        'ResponsibleUnit' => 'GHD_Responsible_Unit',
        'Site' => 'GHD_Affected_Site',
        'Vo' => 'GHD_VO_Specific',
        'ProblemDate' => 'GHD_Data_Time_Prob',
        'ModificationDate' => 'GHD_Modified_Date',
        'CreationDate' => 'GHD_Create_Date',
        'XMLFields' => 'GHD_Soap_Client_Data'

    );

    public $xMap = array(

        'Step' => 'Header_Step',
        'Community' => 'Header_Community',
    );


    public function testConstruct()
    {
        $formattedSteps = array();
        foreach ($this->step as $key => $value) {
            $formattedSteps[$key] = new Step($value);
        }

        $wf = new Workflow($this->schema, $formattedSteps, 'regular');
        $wf->setDefaultStepId('new');
        $this->assertNotNull($wf);
        return $wf;
    }

    /**
     * @depends testConstruct
     */
    public function testGetters(Workflow $wf)
    {

        $this->assertEquals($this->schema['esc1']['next'], $wf->getStepSuccessors('esc1'));
        $this->assertEquals($this->schema['esc1']['label'], $wf->getStepLabel('esc1'));
    }

    /**
     * @depends testConstruct
     */
    public function testCreateNewTicket(Workflow $wf)
    {

        $clientValues = array('test_date' => '2012-12-13', 'test_name' => 'CRLCheck', 'node' => 'ccli01.in2p3.fr', "site_name" => 'IN2P3');
        $new_subject_result = 'NAGIOS  *CRLCheck* failed on ccli01.in2p3.fr@IN2P3';

        $gf = new GgusFields($this->cMap, $this->xMap);
        $gf->setValue('Login', 'myLogin');
        $tmpTicket = new OpsTicket($gf);

        $nextTicket = $wf->getNextTicket($tmpTicket, $clientValues);
        $this->assertEquals($new_subject_result, $nextTicket->getSubject());

    }


    /**
     * @depends testConstruct
     */
    public function testGetNextTicket(Workflow $wf)
    {

        $clientValues = array('ngi_name' => 'NGI_FRANCE');
        $new_subject_result = 'Dear COds... Admins and NGI Helpdesk, ..... olivier - NGI_FRANCE NGI';

        $gf = new GgusFields($this->cMap, $this->xMap);
        $gf->setValue('Login', 'myLogin');
        $previousTicket = new OpsTicket($gf);

        $previousTicket->setSubject('This is a new subject...');
        $previousTicket->setCommunity('GoodCommunity');
        $previousTicket->setModifier('olivier');
        $previousTicket->setStep('esc2');

        $tmpTicket = new OpsTicket($gf);
        $nextTicket = $wf->getNextTicket($tmpTicket, $clientValues, $previousTicket, '+1');
        $this->assertEquals($new_subject_result, $nextTicket->getComment());

    }


    /**
     * @depends testConstruct
     */
    public function testGetNextStep(Workflow $wf)
    {

        $clientValues = array('ngi_name' => 'NGI_FRANCE');
        $new_subject_result = 'Dear COds... Admins and NGI Helpdesk, ..... olivier - NGI_FRANCE NGI';

        $gf = new GgusFields($this->cMap, $this->xMap);
        $gf->setValue('Login', 'myLogin');
        $previousTicket = new OpsTicket($gf);

        $previousTicket->setSubject('This is a new subject...');
        $previousTicket->setCommunity('GoodCommunity');
        $previousTicket->setModifier('olivier');
        $previousTicket->setStep('esc2');

        $nextStep = $wf->getNextStep($clientValues, $previousTicket, '+1');
        $this->assertEquals($new_subject_result, $nextStep['3']['value']);

    }

    /**
     * @expectedException TicketingSystem\Exceptions\WorkflowException
     * @depends testConstruct
     */
    public function testPrepareNextStepException2(Workflow $wf)
    {
        $gf = new GgusFields($this->cMap, $this->xMap);
        $gf->setValue('Login', 'myLogin');
        $previousTicket = new OpsTicket($gf);

        $tmpTicket = new OpsTicket($gf);
        $wf->getNextTicket($tmpTicket, array(), $previousTicket, '+5');
    }
}