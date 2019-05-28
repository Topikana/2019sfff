<?php

namespace AppBundle\Services\TicketingSystem\Workflow\Exceptions;

class InvalidRequestParameterException extends \Exception
{
    function __construct($message, $code = null, $previous = null) 
    {
    	if($previous) {
    		$message = $message." : "."\n".$previous->getTraceAsString() ."\n".'from '.$previous->getMessage();	
		}
     	parent::__construct('[InvalidStepException] '.$message, $code, $previous);
    }
    
}