<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;

use GGUSHelpdesk\GgusObjectInterface;
use GGUSHelpdesk\GgusSoapService;

/**
 * ggusTicketSoapService
 * provide basic access to ggus ticket soap service
 *
 * ************************************* TICKET ERROR **************************************************************
 * const TICKET_ALREADY_CLOSED = "from ERROR (10023): ; GGUS ticket #50663 has already reached a terminal status.";
 * *****************************************************************************************************************
 * @author Pierre FREBAULT
 */
abstract class GgusTicketSoapService extends GgusSoapService
{

    protected $communities;


    public function __construct(array $config, $name)
    {

        parent::__construct($config, $name);

        $this->communities = array();
        if (isset($this->config['communities'])) {
            $this->communities = $this->config['communities'];
        }
    }


    /**
     * Check if ticket constants needed by helpdesk are properly set
     * @param <ITicket> ticket instance to check
     * @return <bool> true if ticket is well formed, false otherwise
     */
    protected function isTicketWellformed(GgusObjectInterface $t)
    {

        if(!parent::isGgusObjectWellformed($t)) return false;

        // check SubCommunity if needed
        if (!isset($this->communities[$t->getCommunity()])) {
            $this->lastTicketFormatError = "Community '" . $t->getCommunity() . "' is not available";
            return false;
        }

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

    /**
     * return logical url pointing on a given ticket
     * @param <integer> $pLinkId
     * @return <string> url
     */
    public function buildGgusObjectPermaLink($pLinkId)
    {
        return sprintf('%s?mode=ticket_info&ticket_id=%s', $this->config['urls']['site'], $pLinkId);
    }

}

