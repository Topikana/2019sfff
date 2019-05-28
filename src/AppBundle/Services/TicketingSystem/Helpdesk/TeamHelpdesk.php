<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;

use AppBundle\Services\TicketingSystem\Ticket\TeamTicket;
use AppBundle\Services\TicketingSystem\Helpdesk\GgusHelpdesk;
use AppBundle\Services\TicketingSystem\Helpdesk\IHelpdesk;


class TeamHelpdesk extends GgusHelpdesk implements IHelpdesk {

    public function __construct(array $config, $name, $historyService, $lavoisierService) {
        parent::__construct($config, $name, $historyService, $lavoisierService);
    }

    public function createGgusObjectInstance($setDefaultValues = true) {
        $t = new TeamTicket($this->getGgusFieldInstance());
        if(true === $setDefaultValues) {
            $t->setHelpdeskValues($this->getTicketDefaults());
        }
        return $t;
     }

    public function getTicketInstance($setDefaultValues = true)
    {

        return $this->createGgusObjectInstance($setDefaultValues);
    }




}