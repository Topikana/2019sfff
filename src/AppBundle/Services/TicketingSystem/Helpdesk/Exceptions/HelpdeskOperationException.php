<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk\Exceptions;

class HelpdeskOperationException extends \Exception 
{
    function __construct($message, $code = null, $previous = null) 
    {
    	if($previous) {
    		$message = $message." : "."\n".$previous->getTraceAsString() ."\n".'from '.$previous->getMessage();	
		}
     	parent::__construct('[HelpdeskOperationException] '.$message, $code, $previous);
    }
    
}