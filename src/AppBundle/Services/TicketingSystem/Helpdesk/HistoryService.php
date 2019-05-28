<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;

use AppBundle\Services\TicketingSystem\Ticket\HistoryTicket;
use AppBundle\Services\TicketingSystem\Helpdesk\GgusTicketSoapService;

class HistoryService extends GgusTicketSoapService {
    
    public function getList($id) {
        $query=array('GHD_Request_ID' => $id);
        return $this->__getGgusObjectList($this->config['methods']['list'], $query);
    }

    function createGgusObjectInstance($setDefaultValues = true)
    {
        $t = new HistoryTicket($this->getGgusFieldInstance());
        return $t;
    }
}