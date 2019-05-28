<?php 

require_once dirname(__FILE__) . '/../Helpdesk/IHelpdesk.php';
require_once dirname(__FILE__) . '/../Helpdesk/GgusSoapService.php';
require_once dirname(__FILE__) . '/../Helpdesk/GgusHelpdesk.php';
require_once dirname(__FILE__) . '/../Helpdesk/OpsHelpdesk.php';
require_once dirname(__FILE__) . '/../Helpdesk/HistoryService.php';
require_once dirname(__FILE__) . '/../Form/FormItem.php';
require_once dirname(__FILE__) . '/../Form/IFormItemFactory.php';
require_once dirname(__FILE__) . '/../Form/Widget/OpWidgetFormCarbonCopy.php';

require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidget.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetForm.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormInput.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormInputText.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormInput.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormChoiceBase.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormTextarea.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormSelect.class.php';

require_once dirname(__FILE__) . '/../../../symfony/lib/validator/sfValidatorBase.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/validator/sfValidatorPass.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/validator/sfValidatorString.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/validator/sfValidatorChoice.class.php';

use TicketingSystem\Form\DefaultFactory;
use TicketingSystem\Helpdesk\GgusSoapService;
use TicketingSystem\Helpdesk\OpsHelpdesk;
use TicketingSystem\Helpdesk\HistoryService;
use TicketingSystem\Helpdesk\GgusLavoisierService;
use TicketingSystem\Form\Widget\OpWidgetFormCarbonCopy;

class DefaultFactoryTest extends \PHPUnit_Framework_TestCase {


	private $historyConfig = array(
			'urls' => array(
				'wsdl' => "https://myHistoryHelpdesk/wsdl",
				'site' => "https:/myHistoryHelpdesk"
			),
			'authentication' => array(
				'userName' => 'foo',
				'password' => 'bar'
				)
		);
	

	private $helpdeskConfig = array(

			'urls' => array(
				'wsdl' => "https://myHelpdesk/wsdl" ,
				'site' => "https:/myHelpdesk"
			),
			'authentication' => array(
				'userName' => 'foo',
				'password' => 'bar'
				)
		
		);

	private $lavoisierConfig = array(
		'url.rest' => 'http://cclavoisiertest.in2p3.fr:9000/LavoisierService/view/' ,
		'url.wsdl' => 'http://cclavoisiertest.in2p3.fr:8000/LavoisierService?WSDL',
	);	


	public function testConstruct() {

        $factory = $this->getConstruct();
        //do a test !
    }

 	public function getConstruct() {

        $history = new HistoryService($this->historyConfig, 'GGUS_OPS_HIST');
        $lavoisier = new GgusLavoisierService($this->lavoisierConfig);
        $helpdesk  = new OpsHelpdesk($this->helpdeskConfig, 'GGUS_OPS', $history, $lavoisier);
        $factory = new DefaultFactory($helpdesk);

        return $factory;
    }

    public function testPriority() {

    	$pItem = array(
    		'id' => 'Priority',
    		'value' =>  'Low',
    		'visibility' => false,
    		'required' => false
    		);

    	$factory = $this->getConstruct();
    	$formItem = $factory->Priority($pItem);
    	$w = $formItem->getWidget();
    	$v = $formItem->getValidator();

    	$this->assertTrue( is_a($w, 'sfWidgetFormSelect') );
    	$this->assertEquals($w->getOption('label'),'Priority'); 
    	$this->assertEquals($w->getAttribute('hidden'),true); 
    	$this->assertEquals($w->getOption('default'),'Low'); 
    	$this->assertTrue($w->getAttribute('readonly'));

    	$this->assertTrue( is_a($v, 'sfValidatorChoice') );
    	$this->assertEquals($v->getChoices(), 
    		array(
            'less urgent' => 'less urgent', 
            'urgent' => 'urgent', 
            'very urgent' => 'very urgent',
            'top priority' => 'top priority', 
            'normal' => 'normal'));
    	$this->assertFalse($v->getOption('required'));

    }

    public function testSupportUnits() {

    	$pItem = array(
    		'id' => 'ResponsibleUnit',
    		'value' =>  'NGI_FRANCE',
    		'label' => 'Assigned to',
    		'visibility' => true,
    		'required' => false
    		);

    	$factory = $this->getConstruct();
    	$formItem = $factory->ResponsibleUnit($pItem);
    	$w = $formItem->getWidget();
    	$v = $formItem->getValidator();

    	$this->assertTrue( is_a($w, 'sfWidgetFormSelect') );
    	$this->assertEquals($w->getOption('label'),'Assigned to'); 
    	$this->assertEquals($w->getAttribute('hidden'),false); 
    	$this->assertEquals($w->getOption('default'),'NGI_FRANCE'); 
    	$this->assertFalse($w->getAttribute('readonly'));

    	$this->assertTrue( is_a($v, 'sfValidatorChoice') );
    	$this->assertFalse($v->getOption('required'));


    }

    public function testStatus() {

    	$pItem = array(
    		'id' => 'Status',
    		'value' =>  'Opened',
    		'visibility' => true,
    		'required' => false
    		);

    	$factory = $this->getConstruct();
    	$formItem = $factory->Status($pItem);
    	$w = $formItem->getWidget();
    	$v = $formItem->getValidator();

    	$this->assertTrue( is_a($w, 'sfWidgetFormSelect') );
    	$this->assertEquals($w->getOption('label'),'Status'); 
    	$this->assertEquals($w->getAttribute('hidden'),false); 
    	$this->assertEquals($w->getOption('default'),'Opened'); 
    	$this->assertFalse($w->getAttribute('readonly'));

    	$this->assertTrue( is_a($v, 'sfValidatorChoice') );
    	$this->assertFalse($v->getOption('required'));


    }

     public function testSubject() {

    	$pItem = array(
    		'id' => 'Subject',
    		'value' =>  'This a subject of a ticket',
    		'visibility' => true,
    		'required' => true
    		);

    	$factory = $this->getConstruct();
    	$formItem = $factory->Subject($pItem);
    	$w = $formItem->getWidget();
    	$v = $formItem->getValidator();

    	$this->assertTrue( is_a($w, 'sfWidgetFormInputText') );
    	$this->assertEquals($w->getOption('default'),'This a subject of a ticket'); 
    	$this->assertFalse($w->getAttribute('readonly'));

    	$this->assertTrue( is_a($v, 'sfValidatorString') );
    	$this->assertTrue($v->getOption('required'));
    	$this->assertEquals($v->getOption('min_length'),5); 
    	$this->assertEquals($v->getOption('max_length'),255);

    	// try with generic create
    	$formItem_g = $factory->createFormItem($pItem); 
		$this->assertEquals($formItem_g,$formItem);    	

	}

	public function testComments() {

    	$pItem = array(
    		'id' => 'Comment',
    		'value' =>  'Comment.....................................................',
    		'visibility' => true,
    		'required' => true
    		);

    	$factory = $this->getConstruct();
    	$formItem = $factory->Comment($pItem);
    	$w = $formItem->getWidget();
    	$v = $formItem->getValidator();

    	$this->assertTrue( is_a($w, 'sfWidgetFormTextArea') );
    	$this->assertEquals($w->getOption('default'),'Comment.....................................................'); 
    	$this->assertFalse($w->getAttribute('readonly'));

    	$this->assertTrue( is_a($v, 'sfValidatorString') );
    	$this->assertTrue($v->getOption('required'));
    	$this->assertEquals($v->getOption('min_length'),5); 
    	$this->assertEquals($v->getOption('max_length'),4000);

    	// try with generic create
    	$formItem_g = $factory->createFormItem($pItem); 
		$this->assertEquals($formItem_g,$formItem);    	

	}

	public function testModifier() {

    	$pItem = array(
    		'id' => 'Modifier',
    		'value' =>  'Olivier',
    		'visibility' => false,
    		'required' => false
    		);

    	$factory = $this->getConstruct();
    	$formItem = $factory->Modifier($pItem);
    	$w = $formItem->getWidget();
    	$v = $formItem->getValidator();

    	$this->assertTrue( is_a($w, 'sfWidgetFormInputText') );
    	$this->assertEquals($w->getOption('default'),'Olivier'); 
        $this->assertTrue($w->getAttribute('hidden')); 
    	$this->assertTrue($w->getAttribute('readonly'));

    	$this->assertTrue( is_a($v, 'sfValidatorPass') );
    	$this->assertFalse($v->getOption('required'));
	}

    public function testCarbonCopy() {

        $pItem = array(
            'id' => 'CarbonCopy',
           // 'value' =>  'Olivier', use it for default
            'visibility' => true,
            'required' => false,
            'json_param' => '{"foo@ngi.com": "ngi", "bar@site.com": "site"}'
            );

        $factory = $this->getConstruct();
        $formItem = $factory->CarbonCopy($pItem);
        $w = $formItem->getWidget();
        $v = $formItem->getValidator();

        $this->assertTrue( is_a($w, 'TicketingSystem\Form\Widget\OpWidgetFormCarbonCopy') );
        $this->assertEquals($w->getOption('choices'),array("foo@ngi.com"=> "ngi", "bar@site.com"=> "site")); 
        $this->assertFalse($w->getAttribute('readonly'));

        $this->assertTrue( is_a($v, 'sfValidatorChoice') );
        $this->assertFalse($v->getOption('required'));
    }


}