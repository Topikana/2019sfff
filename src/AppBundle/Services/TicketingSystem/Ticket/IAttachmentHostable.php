<?php
namespace AppBundle\Services\TicketingSystem\Ticket;

/**
 * provide ticket interface for workflow and helpdesk components
 * @author Olivier LEQUEUX
 */
interface IAttachmentHostable
{

    function getAttachmentName();

    function getAttachmentData();

    function getId();

    function getAuthor();


}


