<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;

use AppBundle\Services\TicketingSystem\Ticket\ITicket;
use Lavoisier\Hydrators\EntriesHydrator;
use AppBundle\Services\TicketingSystem\Helpdesk\Exceptions\HelpdeskOperationException;
use AppBundle\Services\TicketingSystem\Helpdesk\RtTicketRestService;
use AppBundle\Services\TicketingSystem\Ticket\RtTicket;
use AppBundle\Services\TicketingSystem\Helpdesk\RequestTracker;

class RtHelpdesk extends RtTicketRestService
{
    private $lavoisierService;
    protected $attachmentService;
    protected $historyService;
    protected $name;
    protected $serviceIdentifier;
    protected $lavoisierNotification;
    protected $ticketValidation;

    public function __construct(array $config, $name, $lavoisierService, $historyService = null)
    {
        $this->name = $name;
        $this->serviceIdentifier = $config['service_identifier'];
        $this->historyService = $historyService;
        $this->lavoisierService = $lavoisierService;
        $this->lavoisierNotification = true;
        $this->ticketValidation = true;
        $this->attachmentService = null;
        parent::__construct($config, $name);
    }

    public function setAttachementService(AttachmentService $service)
    {
        $this->attachmentService = $service;
    }

    public function getQueue(){
        return $this->config['queue'];
    }

    public function getIdentifier()
    {
        return $this->serviceIdentifier;
    }

    protected function getIdParamName()
    {
        return 'id';
    }

    public function setLavoisierNotification($bool)
    {
        $this->lavoisierNotification = $bool;
    }

    public function setTicketValidation($bool)
    {
        $this->ticketValidation = $bool;
    }

    public function notifyLavoisier($community = null, $subCommunity = null)
    {
        sleep(2);
        if ($this->lavoisierNotification === true) {
            sleep(1);
            $arrayCommunity = (isset($community)) ? array($community) : $this->communities;

            foreach ($arrayCommunity as $community) {
                $this->lavoisierService->notify($this->getLavoisierView($community, $subCommunity));

            }
        }
        sleep(15);

    }

    public function createTicket($ticket)
    {
        $errorMessage = null;
        if ($this->ticketValidation === true) {
            $validity = $this->isTicketWellformed($ticket);
            if ($validity === true) {
                $validity = $ticket->isValidForCreation(false);
                $errorMessage = $ticket->getLastValidationError();
            }
            else {
                $errorMessage = $this->getLastGgusObjectFormatError();
            }
        } else {
            $validity = true;
        }
        if ($validity === true) {
            $result = parent::createTicket($ticket->tohelpdesk());
            if ($this->lavoisierNotification === true) {
                $this->notifyLavoisier("CSI");

            }
            return $result;
        } else {
            throw new HelpdeskOperationException(
                sprintf("Ticket creation aborted, ticket is not properly set : %s ", $errorMessage));
        }

    }

    public function setTicketClosed(ITicket &$ticket)
    {
        $ticket->setCloseDate();

    }


    public function updateTicket(ITicket $ticket)
    {

        $errorMessage = null;
        if ($this->ticketValidation === true) {

            $validity = $this->isTicketWellformed($ticket);

            if ($validity === true) {
                $validity = $ticket->isValidForModification();
                $errorMessage = $ticket->getLastValidationError();
            } else {
                $errorMessage = "Format Error on the ticket";
            }
        } else {
            $validity = true;
        }
        if ($validity === true) {

            if ($ticket->getStep()==='close') {
                $this->setTicketClosed($ticket);
                $ticket->setStatus('resolved');
            }
            $result = $this->doTicketComment($ticket->getId(),array('Text'=> $ticket->getComment()));
            $result = $this->editTicket($ticket->getId(),$ticket->toHelpdesk());



            if ($this->lavoisierNotification === true) {
                $this->notifyLavoisier($ticket->getCommunity(), $ticket->getSubCommunity());
            }
            return $result;
        } else
            throw new HelpdeskOperationException(
                sprintf("Ticket modification aborted, ticket is not properly set : %s ", $errorMessage));
    }


    public function getTicket($id)
    {

        $hydrator = new EntriesHydrator();
        $hydrator->setDefaultBinding('\TicketingSystem\Ticket\RTTicketEntry');

        $ticket = $this->lavoisierService->findTicket(
            "Csi_RT_Ticket_full_byID",
            array("id"=>$id),
            $hydrator

        );

        $rtTicket = $this->createRtObjectInstance($ticket[0]);
        return $rtTicket;
    }

    function createRtObjectInstance($setDefaultValues = true)
    {


        $t = new RtTicket($this->getRtFieldInstance());



            if (true === $setDefaultValues) {
                $t->setHelpdeskValues($this->getRtObjectDefaults());
            } else {
                $t->setHelpdeskValues($setDefaultValues->toHelpdesk());
            }

            return $t;

    }

    public function getRtObjectDefaults($id = null)
    {
        if (null === $id) {
            if (isset($this->config['defaults'])) {
                return $this->config['defaults'];
            }
        } else {
            if (isset($this->config['defaults'][$id])) {
                return $this->config['defaults'][$id];
            }
        }

        return null;
    }

    protected function getOpenedTicketQuery()
    {

        //  should work but don't, unable to aplly any specific request
        //    $str = sprintf(
        //    "'GHD_Last Login'=\"%s\" AND 'GHD_Meta Status'=\"Open\" AND 'GHD_Last Modifier'=\"SYSTEM\" AND 'GHD_Ticket Type'=\"%s\"",
        //    $this->config['authentication']['userName'], $this->config['defaults']['GHD_Ticket_Type']);
        $str = '';
        return array('Qualification' => $str);

    }

    public function getOpenedTickets($community = null, $whereClauses = array(), $subCommunity = null)
    {
        $hydrator = new EntriesHydrator();

        $hydrator->setDefaultBinding('\TicketingSystem\Ticket\RTTicketEntry');
//        $hydrator->setKeyBinding(array('GHD_Soap_Client_Data' => '\Lavoisier\Entries\Entries'));


        return $this->lavoisierService->findTickets(
            $this->getLavoisierView("CSI", $subCommunity),
            $whereClauses,
            $hydrator
        );

//        return $this->lavoisierService->findTickets(
//            "Csi_RT_Ticket_List_byEntity",
//            null
//        );


    }

    public function getTerminateTicketsWithOrphanGroups($community = null, $whereClauses = array(), $subCommunity = null)
    {
//        $hydrator = new EntriesHydrator();
//        $hydrator->setDefaultBinding('\TicketingSystem\Ticket\GGUSTicketEntry');
//        $hydrator->setKeyBinding(array('GHD_Soap_Client_Data' => '\Lavoisier\Entries\Entries'));
//
//        return $this->lavoisierService->findTickets(
//            $this->getLavoisierView($community, $subCommunity, 'view_orphangroups'),
//            $whereClauses,
//            $hydrator);

        return null;
   }


    public function getCounters($entityType, $whereClauses, $community, $subCommunity)
    {

        $result = $this->lavoisierService->getCounters(
            $entityType,
            $whereClauses,
            $this->getLavoisierView($community, $subCommunity));

        return $result->getArrayCopy();
    }

    public function getLavoisierView($community, $subCommunity = null, $view_type = "view")
    {

        if (isset($this->communities[$community])) {
            if (null !== $subCommunity && !empty($subCommunity)) {
                if (isset($this->communities[$community][$subCommunity])) {
                    if (isset($this->communities[$community][$subCommunity][$view_type])) {
                        return $this->communities[$community][$subCommunity][$view_type];
                    } else {
                        throw new \Exception(
                            "Matching lavoisier view for '" . $subCommunity . "/" . $community .
                            "' subcommunity/community is not available");
                    }
                } else {
                    throw new \Exception("SubCommunity :'" . $subCommunity . "' is not available");
                }
            } else {
                if (isset($this->communities[$community][$view_type])) {
                    return $this->communities[$community][$view_type];
                } else {
                    throw new \Exception("Matching lavoisier view for '" . $community . "' community is not available");
                }
            }
        } else {
            throw new \Exception("Community '" . $community . "' is not available");
        }

    }

    public function getGgusOpenedTickets()
    {
        return $this->__getGgusObjectList($this->config['methods']['list'], $this->getOpenedTicketQuery());
    }

    public function getTicketPermaLink($pLinkId)
    {
        return $this->buildRtObjectPermaLink($pLinkId);
    }

    public function getPriorities()
    {
        return array(
            'less urgent',
            'urgent',
            'very urgent',
            'top priority');
    }

    public function getTicketInstance($setDefaultValues = true)
    {

        return $this->createRtObjectInstance($setDefaultValues);
    }

    // 'verified' and 'closed' statuses must not (!!) be set by operations portal or any other interfaced system
    public function getStatuses()
    {
        return array(
            'new',
            'open',
            'assigned',
            'in progress',
            'on hold',
            'waiting for reply',
            'resolved',
            'solved',
            'verified',
            'unsolved',
            'closed'
        );
    }


    public function isTicketOpened(ITicket $ticket)
    {
        return (
            ($ticket->getStatus() !== 'solved') &&
            ($ticket->getStatus() !== 'resolved') &&
            ($ticket->getStatus() !== 'closed') &&
            ($ticket->getStatus() !== 'unsolved')
        );
    }


    public function getSupportUnits()
    {

        $cicSU = array();
        try {
            $cicSU += $this->lavoisierService->getSupportUnits()->getArrayCopy();
            return $cicSU;
        } catch (Exception $e) {
            throw new \Exception("Sorry, unable to retreive SUPPORT UNITS for $this->identifier helpdesk.", $e);
        }
    }

    public function getCommunities()
    {
        return array_keys($this->communities);
    }

    public function getNotificationStrategies()
    {
        return array('Every Change', 'Solution', 'Never');
    }


    public function finalizeTicket(ITicket $ticket)
    {

        $diaryTemplate = <<<EOF
Dashboard related fields
------------------------
Expiration date : %s
Current step : %s
Community : %s
EOF;
        $community = $ticket->getCommunity();
        $subCommunity = $ticket->getSubCommunity();
        if (!empty($subCommunity)) {
            $community .= "/$subCommunity";
        }

        $diary = sprintf($diaryTemplate,
            $ticket->getEndDate(),
            $ticket->getStepLabel(),
            $community
        );

//        $ticket->setDescription($diary);
        return $ticket;

    }

}