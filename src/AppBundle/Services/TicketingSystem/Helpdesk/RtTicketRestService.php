<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;


use AppBundle\Services\TicketingSystem\Ticket\RtTicket;
use RtHelpdesk\Fields;
use AppBundle\Services\TicketingSystem\Helpdesk\RequestTracker;

/**
 * RtTicketRestServiceService
 * provide basic access to RT ticket  service
 *
 *
 *
 * @author Pierre FREBAULT
 */

abstract class RtTicketRestService extends RequestTracker
{
    protected $communities;
    protected $config;
    protected $baseUrl;


    public function __construct(array $config, $name)
    {
        $this->config = $config;
        $this->baseUrl = sprintf($config['urls']['wsdl'] . '/%s', $name);
        parent::__construct($this->baseUrl,
            $config['authentication']['userName'], $config['authentication']['password']);

        $this->communities = array();
        if (isset($config['communities'])) {
            $this->communities = $config['communities'];
        }
    }

    protected function isTicketWellformed($t)
    {
        $priority = $t->getPriority();
        if (isset($priority)) {
            if (!in_array($priority, $this->getPriorities()) && !empty($priority)) {
                $this->lastTicketFormatError = "Priority '" . $priority . "' is not available";
                return false;
            }
        }

        $status = $t->getStatus();
        if (isset($status)) {
            if (!in_array($status, $this->getStatuses()) && !empty($status)) {
                $this->lastTicketFormatError = "Status '" . $status . "' is not available";
                return false;
            }
        }
        return true;
    }

    protected function getRtFieldInstance()
    {
        $rtFields = new Fields(
            $this->config['maps']['cMap'],
            (isset($this->config['maps']['xMap']) ? $this->config['maps']['xMap']: array()));
//        try {
//            $rtFields->setValue('Login', $this->config['authentication']['userName']);
//        } catch (InvalidFieldException $e) {
//        }

        return $rtFields;

    }

    /**
     * return logical url pointing on a given ticket
     * @param <integer> $pLinkId
     * @return <string> url
     * @author olivier lequeux
     */
    public function buildRtObjectPermaLink($pLinkId)
    {
      //  $link = $this->baseUrl."/REST/1.0/ticket/" . $pLinkId;
        $link=str_replace("security","rt",$this->baseUrl)."/Ticket/Display.html?id=".$pLinkId;
        return $link;
    }


}