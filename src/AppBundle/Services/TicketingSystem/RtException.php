<?php
/**
 * Created by PhpStorm.
 * Copyright Operations Portal - CCIN2P3
 * User: cyril
 * Date: 01/06/15
 * Time: 14:54
 * 
 */

namespace AppBundle\Services\TicketingSystem;


class RTObjectValidationException extends \Exception
{
    function __construct($message, $code = null, $previous = null)
    {
        if($previous) {
            $message = $message." : "."\n".$previous->getTraceAsString() ."\n".'Cause By '.$previous->getMessage();
        }
        parent::__construct('[RTtValidationException] '.$message, $code, $previous);
    }

}