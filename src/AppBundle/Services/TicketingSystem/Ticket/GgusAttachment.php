<?php

namespace AppBundle\Services\TicketingSystem\Ticket;

use GGUSHelpdesk\Exceptions\GgusObjectValidationException;
use GGUSHelpdesk\GgusObject;

/**
 * dedicated to basic GGUS ticket behavior
 * @author Pierre FREBAULT
 */
class GgusAttachment extends GgusObject
{


    protected function isValid()
    {
        try {

            $name = trim($this->fields['GAT_Attachment_Name']);
            $modifier = trim($this->fields['GAT_Last_Modifier']);
            $data = $this->fields['GAT_Attachment_Data'];
            $size = trim($this->fields['GAT_Attachment_Orig_Size']);
            $id = trim($this->getTicketId());
            $login = trim($this->fields['GAT_Last_Login']);

            if (empty($name)) {
                $this->lastValidationError = "Missing required field 'Name'";
                return false;
            }
            if (empty($modifier)) {
                $this->lastValidationError = "Missing field 'Modifier'";
                return false;
            }
            if (empty($data)) {
                $this->lastValidationError = "Missing required field 'Data' ";
                return false;
            }
            if (empty($size)) {
                $this->lastValidationError = "Missing required field  'Size'";
                return false;
            }
            if (empty($id)) {
                $this->lastValidationError = "Missing required field 'Ticket Id' ";
                return false;
            }
            if (empty($login)) {
                $this->lastValidationError = "Missing required field  'Login' ";
                return false;
            }

            return true;
        } catch (\Exception $e) {
            throw new GgusObjectValidationException("Unable to check GgusAttachment validity", null, $e);
        }

    }


    //-------- common cMAP fields accessors ---------

    public function getName()
    {
        return $this->fields->getValue('Name');
    }

    public function setName($str)
    {
        return $this->fields->setValue('Name', $str);
    }

    public function getData()
    {
        return base64_decode($this->fields->getValue('Data'));
    }

    public function setData($str, $encode = true)
    {

        if($encode == true)
            $str = base64_encode($str);

        $this->setSize(strlen($str));
        return $this->fields->setValue('Data', $str);
    }

    public function getSize()
    {
        return $this->fields->getValue('Size');
    }

    public function setSize($str)
    {
        return $this->fields->setValue('Size', $str);
    }
    public function getTicketId()
    {
        return $this->fields->getValue('TicketId');
    }

    public function setTicketId($str)
    {
        return $this->fields->setValue('TicketId', $str);
    }

    public function getLogin()
    {
        return $this->fields->getValue('Login');
    }

    public function setModifier($str)
    {
        $this->fields->setValue('Modifier', $str);
    }

    public function getModifier()
    {
        return $this->fields->getValue('Modifier');
    }

}