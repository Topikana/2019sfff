<?php
namespace AppBundle\Services\TicketingSystem\Ticket;

/**
 * provide ticket interface for workflow and helpdesk components
 * @author Olivier LEQUEUX
 */
interface ITicket {


    // import/export ticket to helpdesk/workflow message format
    function toHelpdesk();
    function toWorkflow();
    function fromHelpdesk($data);
    function fromWorkflow(array $step);

    function fromTicket(ITicket $src);

    // check ticket validity
    function isValidForCreation($safe);
    function isValidForModification();
    function getLastValidationError();
    function getHelpdeskValue($id);

    function getCommunity();
    function getPriority();
    function getStatus();
    function getCloseDate();


}


