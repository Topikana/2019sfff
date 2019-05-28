<?php

require_once dirname(__FILE__) . '/../Ticket/ArrayFields.php';
require_once dirname(__FILE__) . '/../Ticket/GgusObject.php';
require_once dirname(__FILE__) . '/../Ticket/IGgusObject.php';

use TicketingSystem\Ticket\ArrayFields;
use TicketingSystem\Ticket\IGgusObject;

class ArrayFieldsTest extends \PHPUnit_Framework_TestCase {

    function testfromStdClass() {
        
        $at = new ArrayFields;
        $at->foo = 3;
        $at->bar = 4;
        $refArray = array(
            'foo'  => 3,
            'bar'  => 4,
            'foo2' => 'tree',
            'bar2' => 'flower'
        );
        
        $at->fromArray($refArray);
        
        
        $this->assertEquals($at->getData(), $refArray);
        $this->assertEquals($at['bar2'], 'flower');
         $this->assertEquals($at['bar'], 4);
    }

   
    function testOffsetMethods() {
    
        $at = new ArrayFields;
        $this->assertEquals($at->toto, false);
        
        $at->titi = 'hello world';
        $this->assertEquals(isset($at->titi), true);
        
        unset($at->titi);
        $this->assertEquals(isset($at->titi), false);
    }
}