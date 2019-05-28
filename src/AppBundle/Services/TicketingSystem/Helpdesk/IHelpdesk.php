<?php
namespace AppBundle\Services\TicketingSystem\Helpdesk;

use AppBundle\Services\TicketingSystem\Ticket\ITicket;

/**
 *
 * @author Olivier LEQUEUX
 */
interface IHelpdesk {
    
    function createTicket(ITicket $ticket);
    function updateTicket(ITicket $ticket);
    function getTicket($id);
    function getTicketHistory($id);
    function getTicketPermaLink($pLinkId);
    function getOpenedTickets($community = null, $whereClauses = array());
    function finalizeTicket(ITicket $ticket);

    function getPriorities();
    function getStatuses();
    function getSupportUnits();
    function getNotificationStrategies();
    function getCommunities();
    
    function getTicketInstance($setDefaultValues = true);

    function getIdentifier();
    function isTicketOpened(ITicket $ticket);

    
}


