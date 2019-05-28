<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;

use AppBundle\Services\TicketingSystem\Ticket\ITicket;
use Lavoisier\Hydrators\EntriesHydrator;
use AppBundle\Services\TicketingSystem\Helpdesk\Exceptions\HelpdeskOperationException;
use AppBundle\Services\TicketingSystem\Helpdesk\AttachmentService;

use AppBundle\Services\TicketingSystem\Helpdesk\GgusTicketSoapService;

abstract class GgusHelpdesk extends GgusTicketSoapService
{
    private $lavoisierService;
    protected $attachmentService;
    protected $historyService;
    protected $name;
    protected $serviceIdentifier;
    protected $lavoisierNotification;
    protected $ticketValidation;

    public function __construct(array $config, $name, $historyService, $lavoisierService)
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

    public function setAttachementService(AttachmentService $service) {
        $this->attachmentService = $service;
    }

    public function getIdentifier()
    {
        return $this->serviceIdentifier;
    }

    protected function getIdParamName()
    {
        return 'GHD_Request_ID';
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
        if ($this->lavoisierNotification === true) {
            sleep(1);
            $arrayCommunity = (isset($community)) ? array($community) : $this->communities;

            foreach ($arrayCommunity as $community)
                $this->lavoisierService->notify($this->getLavoisierView($community, $subCommunity));
        }
    }

    public function createTicket(ITicket $ticket)
    {
        $errorMessage = null;
        if ($this->ticketValidation === true) {
            $validity = $this->isTicketWellformed($ticket);
            if ($validity === true) {
                $validity = $ticket->isValidForCreation(false);
                $errorMessage = $ticket->getLastValidationError();
            } else {
                $errorMessage = $this->getLastGgusObjectFormatError();
            }
        } else {
            $validity = true;
        }
        if ($validity === true) {
            $result = $this->__setGgusObject(
                $ticket,
                $this->config['methods']['create'],
                $this->getIdParamName());
            if ($this->lavoisierNotification === true) {
                $this->notifyLavoisier($ticket->getCommunity(), $ticket->getSubCommunity());
            }
            return $result;
        } else {
            throw new HelpdeskOperationException(
                sprintf("Ticket creation aborted, ticket is not properly set : %s ", $errorMessage));
        }

    }

    public function setTicketCloseDate(ITicket &$ticket)
    {
        if (in_array($ticket->getStatus(), array(
            'solved',
            'unsolved',
            'verified',
            'closed'))
        ) {
            $closeDate = $ticket->getCloseDate();
            // to prevent double 'setCloseDate' with sequence of Close & Verify steps from portal
            if(empty($closeDate)) {
                $ticket->setCloseDate();
            }
        }
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
                $errorMessage = $this->getLastGgusObjectFormatError();
            }
        } else {
            $validity = true;
        }
        if ($validity === true) {
            $this->setTicketCloseDate($ticket);

            $result = $this->__setGgusObject(
                $ticket,
                $this->config['methods']['update'],
                $this->getIdParamName());
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

        $ticket = $this->__getGgusObject(
            $id,
            $this->config['methods']['get'],
            $this->getIdParamName());

        // Ggus do not return Id field : prepare ticket for update
        if (null !== $ticket) {
            $ticket->setId($id);
        }
        return $ticket;
    }

    public function getTicketHistory($id)
    {
        return $this->historyService->getList($id);
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
        $hydrator->setDefaultBinding('\TicketingSystem\Ticket\GGUSTicketEntry');
        $hydrator->setKeyBinding(array('GHD_Soap_Client_Data' => '\Lavoisier\Entries\Entries'));

        if ($community === null)
            return $this->getGgusOpenedTickets();
        else
            return $this->lavoisierService->findTickets(
                $this->getLavoisierView($community, $subCommunity),
                $whereClauses,
                $hydrator
            );
    }

    public function getTerminateTicketsWithOrphanGroups($community = null, $whereClauses = array(), $subCommunity = null)
    {
        $hydrator = new EntriesHydrator();
        $hydrator->setDefaultBinding('\TicketingSystem\Ticket\GGUSTicketEntry');
        $hydrator->setKeyBinding(array('GHD_Soap_Client_Data' => '\Lavoisier\Entries\Entries'));

        return $this->lavoisierService->findTickets(
        $this->getLavoisierView($community, $subCommunity, 'view_orphangroups'),
        $whereClauses,
        $hydrator);}



    public function getCounters($entityType, $whereClauses, $community, $subCommunity)
    {

        $result = $this->lavoisierService->getCounters(
            $entityType,
            $whereClauses,
            $this->getLavoisierView($community, $subCommunity));


        return $result->getArrayCopy();
    }

    public function getLavoisierView($community, $subCommunity = null, $view_type="view")
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
        return $this->buildGgusObjectPermaLink($pLinkId);
    }

    public function getPriorities()
    {
        return array(
            'less urgent',
            'urgent',
            'very urgent',
            'top priority');
    }

    // 'verified' and 'closed' statuses must not (!!) be set by operations portal or any other interfaced system
    public function getStatuses()
    {
        return array(
            'new',
            'assigned',
            'in progress',
            'on hold',
            'waiting for reply',
            'reopened',
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
            ($ticket->getStatus() !== 'verified') &&
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


    public function finalizeTicket(ITicket $ticket) {

        $diaryTemplate = <<<EOF
Dashboard related fields
------------------------
Expiration date : %s
Current step : %s
Community : %s
EOF;
        $community = $ticket->getCommunity();
        $subCommunity = $ticket->getSubCommunity();
        if(!empty($subCommunity)) {
            $community .= "/$subCommunity";
        }

        $diary = sprintf($diaryTemplate,
            $ticket->getEndDate(),
            $ticket->getStepLabel(),
            $community
        );

        $ticket->setDiary($diary);
        return $ticket;

    }


}