<?php

namespace AppBundle\Services\OpsLavoisier\Exceptions;

use \Lavoisier\Query;

class OpsLavoisierServiceException extends \Exception
{
    private $userMessage = null;

    function __construct(Query $q, \Exception $e)
    {
        $this->userMessage = sprintf("Unable to process query : % s " . "\n" . "Caused by %s " . "\n" . " %s",
            $q->getUrl(), $e->getMessage(), $e->getTraceAsString());
        parent::__construct($this->userMessage, 0, $e);
    }


}