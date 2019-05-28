<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 15/10/12
 */

namespace AppBundle\Services\JSTree;

use AppBundle\Services\JSTree\Exceptions\InvalidRootBehaviourException;

class RecipientCollection
{

    CONST DATA_TYPE_EMAIL = 'email';
    CONST DATA_TYPE_ID = 'id';
    CONST DATA_TYPE_LABEL = 'label';

    CONST ROOT_ID_SUFFIX = 'all';

    //use one of those constants for input name of a <li> root like jstree[targetId][*]
    CONST ROOT_AS_MAILING_LIST = 'global_email'; // mailing-list is existing and have to be retrieved in members list INSTEAD of children
    CONST ROOT_AS_PARENT_FLAG = 'check_all'; // no mailing-list, just to know if all children are checked or not
    CONST ROOT_AS_MAILING_LIST_CC = 'global_email_cc'; // mailing-list is existing and have to be retrieved in members list and ADDED to recipients with children

    private $identifier;
    private $name;
    private $recipients;

    public function __construct($identifier, $name, array $recipients= array())
    {
        $this->recipients = new \ArrayObject($recipients);
        $this->availablesBehaviours = array(
            self::ROOT_AS_MAILING_LIST,
            self::ROOT_AS_MAILING_LIST_CC,
            self::ROOT_AS_PARENT_FLAG
        );
        $this->identifier = $identifier;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }


    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function getRecipientLabel($id)
    {
        $internalId = $this->generateInternalId($id);
        if (isset($this->recipients[$internalId])) {
            return $this->recipients[$internalId][self::DATA_TYPE_LABEL];
        } else return null;
    }

    public function getRecipientMail($id)
    {
        $internalId = $this->generateInternalId($id);
        if (isset($this->recipients[$internalId])) {
            return $this->recipients[$internalId][self::DATA_TYPE_EMAIL];
        } else return null;
    }

    private function generateInternalId($id)
    {
        return sprintf("%s_%s", $this->identifier, $id);
    }

    public function add($id, $label, $email)
    {
        $internalId = $this->generateInternalId($id);
        $this->recipients[$internalId] = array(
            self::DATA_TYPE_LABEL => $label,
            self::DATA_TYPE_EMAIL => $email,
        );
    }

    public function addRootBehaviour($email, $rootBehaviour = self::ROOT_AS_MAILING_LIST)
    {
        if(in_array($rootBehaviour, $this->availablesBehaviours)) {
            $this->add(self::ROOT_ID_SUFFIX, $rootBehaviour, $email);
        }
        else {
            throw new InvalidRootBehaviourException("Unknown $rootBehaviour root behaviour");
        }
    }

    public function getRootBehaviour() {

        $internalId = $this->generateInternalId(self::ROOT_ID_SUFFIX);
        if(isset($this->recipients[$internalId])) {
            return $this->recipients[$internalId];
        }
        else return null;
    }


    /**
     * check if mapping is correct
     * @param <string> $id, an id to retrieve
     * @param <string> $type, set data type user want to retrieve : self::DATA_TYPE_ID | DATA_TYPE_EMAIL | DATA_TYPE_LABEL
     * @return <bool> true if found, false otherwise
     */
    public function checkItem($internalId, $type)
    {
        $strIID = strval($internalId);


        if (isset($this->recipients[$strIID]) && is_array($this->recipients[$strIID])) {
            if (isset($this->recipients[$strIID][$type]) && !empty($this->recipients[$strIID][$type])) {
                return true;
            }
        }

        return false;
    }

    /**
     * match member's data with a given ID and data type
     * @param <string> $id, an id to retrieve
     * @param <string> $type, set data type user want to retrieve : self::DATA_*
     * @apram <bool> $internalId type of id parameter, set to true to use internal id directly
     * @return <string> value asked or 'ERR:value' otherwise
     */
    public function matchItem($id, $type, $internalId= false)
    {


        if($internalId === false) {
            $id = $this->generateInternalId($id);
        }
        switch ($type) {
            case self::DATA_TYPE_ID:
                return strval($id);
                break;
            default :
                if ($this->checkItem($id, $type)) {
                    return $this->recipients[strval($id)][$type];
                } else {


                    return "ERR:".$id;
                }
                break;
        }
    }

    public function populate(\SimpleXMLElement $xml_recipients)
    {
        $this->recipients = array();
        foreach($xml_recipients->Recipient as $value)
        {
            $element = array(
                self::DATA_TYPE_EMAIL=>(string)$value->email,
                self::DATA_TYPE_LABEL=>(string)$value->label
            );
            $Gid=$this->generateInternalId((string)$value->id);
            $this->recipients[$Gid] = $element;
        }
    }
}