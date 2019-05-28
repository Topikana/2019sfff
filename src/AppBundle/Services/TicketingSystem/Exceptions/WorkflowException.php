<?php

namespace AppBundle\Services\TicketingSystem\Exceptions;

class WorkflowException extends \Exception 
{
    function __construct($message, $code = null, $previous = null) 
    {
       parent::__construct($message, $code, $previous);
    }
    
}