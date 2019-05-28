<?php

require_once dirname(__FILE__) . '/../Ticket/Exceptions/InvalidFieldException.php';
require_once dirname(__FILE__) . '/../Ticket/Exceptions/InvalidXMLException.php';
require_once dirname(__FILE__) . '/../Ticket/Exceptions/GgusObjectValidationException.php';
require_once dirname(__FILE__) . '/../Ticket/GgusObject.php';
require_once dirname(__FILE__) . '/../Ticket/ITicket.php';
require_once dirname(__FILE__) . '/../Ticket/ArrayFields.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IEntries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Entries.php';
require_once dirname(__FILE__) . '/../Ticket/GgusFields.php';
require_once dirname(__FILE__) . '/../Ticket/GgusObject.php';
require_once dirname(__FILE__) . '/../Ticket/GgusTicket.php';
require_once dirname(__FILE__) . '/../Ticket/OpsTicket.php';


use TicketingSystem\Ticket\GgusFields;
use TicketingSystem\Ticket\OpsTicket;

class OpsTicketTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->cMap = array(
            'Vo'                   => 'GHD_VO_Specific',
            'AuthorEmail'          => 'GHD_E-Mail',
            'Login'                => 'GHD_Last_Login',
            'Modifier'             => 'GHD_Last_Modifier',
            'AuthorDn'             => 'GHD_Cert_DN',
            'Author'               => 'GHD_Submitter',
            'Subject'              => 'GHD_Subject',
            'Type'                 => 'GHD_TicketType',
            'Status'               => 'GHD_Status',
            'ResponsibleUnit'      => 'GHD_Responsible_Unit',
            'Comment'              => 'GHD_Public_Diary',
            'Involve'              => 'GHD_Involve',
            'Site'                 => 'GHD_Affected_Site',
            'CarbonCopy'           => 'GHD_Carbon_Copy',
            'XMLFields'            => 'GHD_Soap_Client_Data',
            'Description'          => 'GHD_Description',
            'Priority'             => 'GHD_Priority',
            'ProblemType'          => 'GHD_ProblemType',
            'Id'                   => 'GHD_Request_ID',
            'Solution'             => 'GHD_Detailed_Solution',
            'ResponsibleEmail'     => 'GHD_Assigned_To',
            'MetaStatus'           => 'GHD_Meta_Status',
            'ModificationDate'     => 'GHD_Modified_Date',
            'CreationDate'         => 'GHD_Create_Date',
            'NotificationStrategy' => 'GHD_User_Notification'
        );

        $this->xmlKeyList = array(
            'Helpdesk'          => '',
            'Workflow'          => '',
            'Step'              => '',
            'WorkflowStepLabel' => '',
            'Community'         => '',
            'SubCommunity'      => '',
            'EndDate'           => '',
            'GroupId'           => '',
            'GroupUrl'          => '',
            'CloseDate'         => ''
        );

        $this->gf = new GgusFields($this->cMap, $this->xmlKeyList);
        $this->gf->setValue('Login', 'myLogin');
    }


    public function testAccessors()
    {
        $ticket = new OpsTicket($this->gf);
        $cc = 'foo@bar.com;bar@foo.com';
        $ticket->setCarbonCopy($cc);
        $ticket->setInvolve('involve');
        $ticket->setResponsibleUnit('SU');

        $this->assertEquals($cc, $ticket->getCarbonCopy());
        $this->assertEquals('involve', $ticket->getInvolve());
        $this->assertEquals('SU', $ticket->getResponsibleUnit());
    }

    public function testisValidForCreation()
    {

        $ticket = new OpsTicket($this->gf);

        $ticket->setSubject('');
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setSubject('subject');
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setCommunity(false);
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setCommunity('commId');
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setModifier('');
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setModifier('olivier');
        $ticket->setStep("");
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setStep('step1');

        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setWorkflow('WFID1');
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setHelpdesk('HDID1');

        if ($ticket->isValidForCreation() === false) {
            var_dump("[LAST VALIDATION ERROR] " . $ticket->getLastValidationError());
        }

        $this->assertEquals($ticket->isValidForCreation(), true);


    }

    public function testisValidForModification()
    {

        $ticket = new OpsTicket($this->gf);
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setCommunity(false);
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setCommunity('commId');
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setStep(null);
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setStep('step1');
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setModifier('');
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setModifier('olivier');
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setId(1234);
        $ticket->setSubject('subject');
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setWorkflow('WFID1');
        $this->assertNotEquals($ticket->isValidForCreation(), true);
        $ticket->setHelpdesk('HDID1');

        if ($ticket->isValidForModification() === false) {
            var_dump("[LAST VALIDATION ERROR] " . $ticket->getLastValidationError());
        }

        $this->assertEquals($ticket->isValidForModification(), true);

    }


}

?>
