<?php
require_once dirname(__FILE__) . '/../Ticket/ArrayFields.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IEntries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Entries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/EntriesHydrator.php';
require_once dirname(__FILE__) . '/../Ticket/GgusFields.php';
require_once dirname(__FILE__) . '/../Ticket/Exceptions/InvalidFieldException.php';
require_once dirname(__FILE__) . '/../Ticket/Exceptions/InvalidXMLException.php';

use \TicketingSystem\Ticket\GgusFields;

class GgusFieldsTest extends \PHPUnit_Framework_TestCase
{


    public function setUp()
    {
        $this->map = array(
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
            'GroupUrl'          => ''
        );

        $this->gf = new GgusFields($this->map, $this->xmlKeyList);

    }


    public function testSetters()
    {
//         in xml
        $this->gf->setValue('Helpdesk', 'ONE');
        $this->assertEquals($this->gf->getValue('Helpdesk'), 'ONE');
//         in classic
        $this->gf->setValue('Description', 'This is a descr');
        $this->assertEquals($this->gf->getValue('Description'), 'This is a descr');
//         // in classic
        $this->gf->setValue('CarbonCopy', 'toto');
        $this->assertEquals($this->gf->getValue('CarbonCopy'), 'toto');
        return $this->gf;

    }


    /**
     * @expectedException TicketingSystem\Ticket\Exceptions\InvalidFieldException
     */
    public function testGetException()
    {
        // in xml
        $this->gf->getValue('toto');
    }

    /**
     * @expectedException TicketingSystem\Ticket\Exceptions\InvalidFieldException
     */
    public function testSetException()
    {
        // in xml
        $this->gf->setValue('toto', 0);
    }

    /**
     * @depends testSetters
     */
    public function testIndexedArray(GgusFields $gf)
    {
        $data = array(
            'GHD_Description'      => "This is a descr",
            'GHD_Carbon_Copy'      => 'toto',
            'GHD_Soap_Client_Data' => '<e:entries xmlns:e="http://software.in2p3.fr/lavoisier/entries.xsd"><e:entry key ="Helpdesk">ONE</e:entry><e:entry key ="Workflow"></e:entry><e:entry key ="Step"></e:entry><e:entry key ="WorkflowStepLabel"></e:entry><e:entry key ="Community"></e:entry><e:entry key ="SubCommunity"></e:entry><e:entry key ="EndDate"></e:entry><e:entry key ="GroupId"></e:entry><e:entry key ="GroupUrl"></e:entry></e:entries>'
        );

        $res = $gf->toIndexedArray();

        $this->assertEquals($res['GHD_Carbon_Copy'], $data['GHD_Carbon_Copy']);
        $this->assertEquals($res['GHD_Description'], $data['GHD_Description']);

        $this->assertXmlStringEqualsXmlString($res['GHD_Soap_Client_Data'], $data['GHD_Soap_Client_Data']);
        $this->assertEquals($gf->getValue(GgusFields::FIELD_ID), $data['GHD_Soap_Client_Data']);

    }

    /**
     * @expectedException TicketingSystem\Ticket\Exceptions\InvalidFieldException
     */
    public function testSetXMLField()
    {
        $this->gf->setValue(GgusFields::FIELD_ID, null);
    }

    public function testMappedArray()
    {
        $data = array(
            'Helpdesk'          => '',
            'Workflow'          => '',
            'Step'              => '',
            'WorkflowStepLabel' => '',
            'Community'         => '',
            'SubCommunity'      => '',
            'EndDate'           => '',
            'GroupId'           => '',
            'GroupUrl'          => '',
            'CreationDate'      => '2012-12-15'
        );

        $this->gf->setValue('CreationDate', '2012-12-15');
        $this->assertEquals($this->gf->toMappedArray(), $data);

    }

    public function testSetXmlFieldsString(){
        $data = '<e:entries xmlns:e="http://software.in2p3.fr/lavoisier/entries.xsd"><e:entry key ="Helpdesk"></e:entry><e:entry key ="Workflow"></e:entry><e:entry key ="Step"></e:entry><e:entry key ="WorkflowStepLabel"></e:entry><e:entry key ="Community"></e:entry><e:entry key ="SubCommunity"></e:entry><e:entry key ="EndDate"></e:entry><e:entry key ="GroupId"></e:entry><e:entry key ="GroupUrl"></e:entry></e:entries>';
        $this->gf->setXmlFieldsString($data);

        $this->assertEquals($this->xmlKeyList,$this->gf->getXmlField()->getArrayCopy());
    }
}
