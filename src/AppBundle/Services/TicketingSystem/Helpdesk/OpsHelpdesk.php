<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;

use AppBundle\Services\TicketingSystem\Ticket\ITicket;
use AppBundle\Services\TicketingSystem\Ticket\OpsTicket;
use AppBundle\Services\TicketingSystem\Helpdesk\GgusLavoisierService;
use AppBundle\Services\TicketingSystem\Helpdesk\IHelpdesk;
use AppBundle\Services\TicketingSystem\Helpdesk\GgusHelpdesk;
use AppBundle\Services\TicketingSystem\Helpdesk\AttachmentService;

class OpsHelpdesk extends GgusHelpdesk implements IHelpdesk
{

    public function __construct(array $config, $name, $historyService, $lavoisierService)
    {
        parent::__construct($config, $name, $historyService, $lavoisierService);
    }

    public function getTicketInstance($setDefaultValues = true)
    {

        return $this->createGgusObjectInstance($setDefaultValues);
    }

    public function getSupportUnits()
    {

        $cicSU = parent::getSupportUnits();
        $cicSU += array('COD');

        return $cicSU;
    }

    function createGgusObjectInstance($setDefaultValues = true)
    {
        $t = new OpsTicket($this->getGgusFieldInstance());
        if (true === $setDefaultValues) {
            $t->setHelpdeskValues($this->getGgusObjectDefaults());
        }
        return $t;
    }


    public function createTicket(ITicket $ticket)
    {
        $tId = parent::createTicket($ticket);
        if($ticket->getAttachmentData() !== null){
            sleep(1);
            $ticket->setId($tId);
            $this->attachmentService->createAttachmentFromHost($ticket);
        }
        return $tId;
    }


}
