<?php

namespace AppBundle\Services\TicketingSystem\Ticket;

use AppBundle\Services\TicketingSystem\Ticket\GgusTicket ;
use AppBundle\Services\TicketingSystem\Ticket\ITicket;
use GGUSHelpdesk\Exceptions\GgusObjectValidationException;

/**
 * GGUS Ticket dedicated to GGUS_TEAM helpdesk
 * @author Olivier LEQUEUX
 */
class TeamTicket extends GgusTicket implements ITicket{

  /**
  * check basic rules for a new ticket
  * @return <mixed> true if ticket is valid, index value whichis is failling if not
  * @author olivier lequeux
  */
  public function isValidForCreation($safe = false) {
       try {
            $pResult = parent::isValidForCreation($safe);
            if( $pResult !== true) { 
                return $pResult;
            }

            $certDN         = trim($this->fields['GHD_Cert_DN']);
            $email          = trim($this->fields['GHD_E_Mail']);

            if (empty($certDN))        {return "'AuthorDn' can't be empty";}
            if (empty($email))         {return "'AutorEMail' can't be empty";}

            return true;
        }
        catch(\Exception $e) {
            throw new GgusObjectValidationException("Unable to check TeamTicket validity for creation", null, $e);
        }
    }
}