<?php

namespace AppBundle\Services\JSTree\Exceptions;

class InvalidRootBehaviourException extends \Exception
{
    function __construct($message, $code = 0, $previous = null)
    {
        if($previous) {
            $message = $message." : "."\n".$previous->getTraceAsString() ."\n".'from '.$previous->getMessage();
        }
        parent::__construct('[InvalidRootBehaviourException] '.$message, $code, $previous);
    }

}