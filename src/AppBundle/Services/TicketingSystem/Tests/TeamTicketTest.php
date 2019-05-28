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
require_once dirname(__FILE__) . '/../Ticket/TeamTicket.php';


use TicketingSystem\Ticket\GgusFields;
use TicketingSystem\Ticket\TeamTicket;

class TeamTicketTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->map = array(
            'Vo' => 'GHD_VO_Specific',
            'AuthorEmail' => 'GHD_E-Mail',
            'Login' => 'GHD_Last_Login',
            'Modifier' => 'GHD_Last_Modifier',
            'AuthorDn' => 'GHD_Cert_DN',
            'Author' => 'GHD_Submitter',
            'Subject' => 'GHD_Subject',
            'Type' => 'GHD_TicketType',
            'Status' => 'GHD_Status',
            'ResponsibleUnit' => 'GHD_Responsible_Unit',
            'Comment' => 'GHD_Public_Diary',
            'Involve' => 'GHD_Involve',
            'Site' => 'GHD_Affected_Site',
            'CarbonCopy' => 'GHD_Carbon_Copy',
            'XMLFields' => 'GHD_Soap_Client_Data',
            'Description' => 'GHD_Description',
            'Priority' => 'GHD_Priority',
            'ProblemType' => 'GHD_ProblemType',
            'Id' => 'GHD_Request_ID',
            'Solution' => 'GHD_Detailed_Solution',
            'ResponsibleEmail' => 'GHD_Assigned_To',
            'MetaStatus' => 'GHD_Meta_Status',
            'ModificationDate' => 'GHD_Modified_Date',
            'CreationDate' => 'GHD_Create_Date',
            'NotificationStrategy' => 'GHD_User_Notification'
        );

        $this->xmlKeyList = array(
            'Helpdesk' => '',
            'Workflow' => '',
            'Step' => '',
            'WorkflowStepLabel' => '',
            'Community' => '',
            'SubCommunity' => '',
            'EndDate' => '',
            'GroupId' => '',
            'GroupUrl' => ''
        );

        $this->gf = new GgusFields($this->map, $this->xmlKeyList);
        $this->gf->setValue('Login', 'myLogin');
    }

    public function testAccessors()
    {
        $this->gf->setValue('CreationDate', '2012-12-45');
        $this->gf->setValue('ModificationDate', '2009-45-18');
        $this->gf->setValue('Id', '456786');
        $gt = new TeamTicket($this->gf);

        $gt->setDescription('foo');
        $this->assertEquals($gt->getDescription(), 'foo');

        $gt->setSubject('bar');
        $this->assertEquals($gt->getSubject(), 'bar');

        $gt->setComment('comm');
        $this->assertEquals($gt->getComment(), 'comm');

        $gt->setSolution('sol');
        $this->assertEquals($gt->getSolution(), 'sol');

        $gt->setStatus('status');
        $this->assertEquals($gt->getStatus(), 'status');

        $gt->setSite('site');
        $this->assertEquals($gt->getSite(), 'site');

        $gt->setPriority('priority');
        $this->assertEquals($gt->getPriority(), 'priority');

        $gt->setProblemType('pbtype');
        $this->assertEquals($gt->getProblemType(), 'pbtype');

        $gt->setAuthor('author');
        $this->assertEquals($gt->getAuthor(), 'author');

        $gt->setEndDate('endate');
        $this->assertEquals($gt->getEndDate(), 'endate');

        $gt->setCommunity('ci');
        $this->assertEquals($gt->getCommunity(), 'ci');

        $gt->setModifier('Olivier');
        $this->assertEquals($gt->getModifier(), 'Olivier');

        $this->assertEquals($gt->getId(), '456786');
        $this->assertEquals($gt->getCreationDate(), '2012-12-45');
        $this->assertEquals($gt->getModificationDate(), '2009-45-18');

    }

    public function testSpecificAccessors()
    {
        $ticket = new TeamTicket($this->gf);
        $cc = 'foo@bar.com';

        $ticket->setResponsibleUnit('SU');
        $ticket->setCarbonCopy($cc);
        $ticket->setAuthorDn("CN=4545/DN=cc");
        $ticket->setAuthorEmail('foo@cc.in2p3.fr');

        $this->assertEquals('SU', $ticket->getResponsibleUnit());
        $this->assertEquals($cc, $ticket->getCarbonCopy());
        $this->assertEquals("CN=4545/DN=cc", $ticket->getAuthorDn());
        $this->assertEquals('foo@cc.in2p3.fr', $ticket->getAuthorEmail());
    }


    public function testIsValidForModification()
    {

        $this->gf->setValue('Id', '258');
        $ticket = new TeamTicket($this->gf);

        $ticket->setModifier('');
        $this->assertEquals($ticket->isValidForModification(), false);
        $ticket->setModifier('olivier');
        $this->assertEquals($ticket->isValidForModification(), false);
        $ticket->setWorkflow('WFID1');
        $this->assertEquals($ticket->isValidForModification(), false);
        $ticket->setHelpdesk('HDID1');
        $this->assertEquals($ticket->isValidForModification(), false);
        $ticket->setSubject('a subject');
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setCommunity('commId');
        $ticket->setStep("");
        $this->assertNotEquals($ticket->isValidForModification(), true);
        $ticket->setStep('step1');

        if ($ticket->isValidForModification() === false) {
            var_dump("[LAST VALIDATION ERROR] " . $ticket->getLastValidationError());
        }

        $this->assertEquals($ticket->isValidForModification(), true);

    }

}


