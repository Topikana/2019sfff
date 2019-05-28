<?php
/**
 * Created by PhpStorm.
 * User: olequeux
 * Date: 22/10/14
 * Time: 13:58
 */

namespace AppBundle\Services\TicketingSystem\Ticket;

use \GGUSHelpdesk\ExtendedFields\ExtendedFieldsFactoryInterface;
use \Lavoisier\Entries\Entries;
use \Lavoisier\Hydrators\EntriesHydrator;

class EntriesFieldsFactory implements ExtendedFieldsFactoryInterface {

    function createInstanceFromFieldsList(array $fieldsList) {
        return new Entries($fieldsList);
    }

    function createInstanceFromSerializedObject($string){

        $entriesHydrator = new EntriesHydrator();
        return  $entriesHydrator->hydrate($string);
    }

    function serializeInstance($object){
        return $object->asXMLEntries();
    }

}