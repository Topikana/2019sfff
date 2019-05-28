<?php

namespace AppBundle\Services\TicketingSystem\Exceptions;

class HelpdeskException extends \Exception 
{
    private $userMessage = null;
    function __construct($userMessage, \Exception $e) 
    {
        $this->userMessage = $userMessage;
        parent::__construct($e);
    }
    
    function getUserMessage() 
    {
        return $this->userMessage;
    }
}