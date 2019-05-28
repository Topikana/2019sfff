<?php

namespace AppBundle\Services\TicketingSystem\Ticket;

use AppBundle\Services\TicketingSystem\Ticket\GgusTicket;
use AppBundle\Services\TicketingSystem\Ticket\ITicket;

/**
 * GGUS Ticket dedicated to GGUS_HISTORY helpdesk
 * @author Olivier LEQUEUX
 */
class HistoryTicket extends GgusTicket implements ITicket
{

    /**
     * check basic rules for a ticket to update
     * @param <bool> $safe, set to true to disable TicketValidation Exception
     * @return <bool> true if ticket is valid, index name otherwise
     * @author olivier lequeux
     */
    public function isValidForModification($safe = false)
    {

        try {
            $id = trim($this->fields['GHD_Request_ID']);
            if (empty($id)) {
                $this->lastValidationError = "Missing required 'GHD_Request_ID' field";
                return false;
            }
            return $this->isValid();
        } catch (\Exception $e) {
            if($safe === true) {
                return false;
            }
            else {
                throw new GgusObjectValidationException("Unable to check GgusTicket validity for modification", null, $e);
            }
        }
    }

    /**
     * check basic rules for a new ticket
     * @param <bool> $safe, set to true to disable TicketValidation Exception
     * @return <bool> true if ticket is valid, index name otherwise
     * @author olivier lequeux
     */
    public function isValidForCreation($safe = false)
    {

        try {
            return $this->isValid();
        } catch (\Exception $e) {
            if ($safe === true) {
                return false;
            } else {
                throw new GgusObjectValidationException("Unable to check GgusTicket validity for creation", null, $e);
            }
        }
    }

    public function getDiaryOfSteps()
    {
        return $this->fields->getValue('DiaryOfSteps');
    }

    public function getInternalDiary()
    {
        return $this->fields->getValue('InternalDiary');
    }

}
