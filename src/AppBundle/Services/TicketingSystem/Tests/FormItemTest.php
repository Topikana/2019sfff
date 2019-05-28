<?php
require_once dirname(__FILE__) . '/../Form/FormItem.php';

require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidget.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetForm.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormInput.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormInputText.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormInput.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormChoiceBase.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/widget/sfWidgetFormSelect.class.php';

require_once dirname(__FILE__) . '/../../../symfony/lib/validator/sfValidatorBase.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/validator/sfValidatorString.class.php';
require_once dirname(__FILE__) . '/../../../symfony/lib/validator/sfValidatorChoice.class.php';

use TicketingSystem\Form\FormItem;

class FormItemTest extends \PHPUnit_Framework_TestCase {
   
    
    public function testConstructInputWidget()
    {
        $stepItem =array(
            'id' => 'Comment',
            'label' => 'Body',
            'required' => true,
            'value' =>  'Please add your comment here...',
            'visibility' => true,
            'help' => 'this is help'
        );
        
        $w = new sfWidgetFormInputText();
    	$v = new sfValidatorString(array('min_length' => 5,'max_length' => 255));
        $fi = new FormItem($stepItem, $w, $v);
        
        $this->assertEquals($fi->isRequired(), true );
        $this->assertEquals($fi->getLabel(), 'Body' );
        $this->assertEquals($fi->getHelp(), 'this is help' );
        
    }
    
     public function testConstructorSelectWidget()
    {
        $stepItem =array(
            'id' => 'Priority',
            'required' => false,
            'value' =>  'Please add your comment here...',
            'visibility' => false
        );
        
        $choices = array('one','two', 'three');
        $w = new sfWidgetFormSelect(array('choices' => $choices));
    	$v = new sfValidatorChoice(array('choices' => array_keys($choices)));
        $fi = new FormItem($stepItem, $w, $v);
        
        $this->assertEquals($fi->isRequired(), false );
        $this->assertEquals($fi->getLabel(), 'Priority' );
        $this->assertEquals($fi->isVisible(), false );
        $this->assertEquals($fi->getHelp(), null );
        $this->assertEquals($fi->getId(), 'Priority' );
        
        $widget = $fi->getWidget();
        $this->assertEquals(($widget instanceof sfWidgetFormSelect), true );
        
        $validator = $fi->getValidator();
        $this->assertEquals(($validator instanceof sfValidatorChoice), true );

        
    }
 
    
}

