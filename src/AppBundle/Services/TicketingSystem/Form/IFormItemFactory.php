<?php

namespace AppBundle\Services\TicketingSystem\Form;

/**
 * Interface for factories dedicated to FormItem component (Widget+step+validator) creation
 * @author Olivier LEQUEUX
 */
interface IFormItemFactory {
   
   public function createFormItem($stepId) ; 

}


