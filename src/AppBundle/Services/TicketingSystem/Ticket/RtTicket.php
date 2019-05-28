<?php

namespace AppBundle\Services\TicketingSystem\Ticket;

use \RTHelpdesk\Exceptions\RtObjectValidationException;

use \RTHelpdesk\Exceptions\InvalidFieldException;
use RTHelpdesk\ExtendedFields\ExtendedFieldsFactoryInterface;
use \RTHelpdesk\Fields;


class RtTicket implements ITicket
{
    protected $attachmentData = null;
    protected $attachmentName = "attachment";

    public function __construct(Fields $fields)
    {
        $this->fields = $fields;
        $this->lastValidationError = null;

    }

    public function getLastValidationError()
    {
        return $this->lastValidationError;
    }

    public function isValidForModification($safe = false)
    {

        try {
            $id = trim($this->getId());
            if (empty($id)) {
                $this->lastValidationError = "Missing required 'Id' field";
                return false;
            }
            return $this->isValid();
        } catch (\Exception $e) {
            if ($safe === true) {
                return false;
            } else {
                throw new RtObjectValidationException("Unable to check RtObject validity for modification", null, $e);
            }
        }
    }

    public function fromHelpdesk($data)
    {

        $this->fields->fromArray(get_object_vars($data));
    }

    public function toHelpdesk()
    {
        return $this->fields->toIndexedArray();
    }

    public function toWorkflow()
    {
        return $this->fields->toMappedArray();
    }

    public function fromWorkflow(array $step)
    {
        foreach ($step as $key => $value) {
            try {
                $this->fields->setValue($key, $value);
            } catch (InvalidFieldException $e) {
                // field is ignored
            }
        }


    }

    public function getHelpdeskValue($id)
    {
        if (isset($this->fields[$id])) {
            return $this->fields[$id];
        } else {
            return null;
        }
    }

    public function isValidForCreation($safe = false)
    {
        try {
            return $this->isValid();
        } catch (\Exception $e) {
            if ($safe === true) {
                return false;
            } else {
                throw new RtObjectValidationException("Unable to check RtObject validity for creation", null, $e);
            }
        }
    }

    public function setHelpdeskValues(array $map = null)
    {

        if (is_array($map)) {
            foreach ($map as $key => $value) {
                $this->fields[$key] = $value;
            }
        }
    }

    public function fromTicket(ITicket $src)
    {
        $src_data = $src->toWorkflow();
        $this->fromWorkflow($src_data);
    }


    public function toText()
    {
        throw new \Exception("Please implement me!");
    }

    protected function isValid()
    {
        try {

            $subject = trim($this->fields['Subject']);
//            $modifier = trim($this->fields['GHD_Last_Modifier']);

            $commId = 'CSI';
            $stepId = trim($this->fields->getValue('Step'));
            $HId = trim($this->fields->getValue('Helpdesk'));
            $WId = trim($this->fields->getValue('Workflow'));

            if (empty($subject)) {
                $this->lastValidationError = "Missing required field 'Subject'";

                return false;
            }

//            if (empty($modifier)) {
//                $this->lastValidationError = "Missing field 'Modifier'";
//
//                return false;
//            }
            if (empty($stepId)) {
                $this->lastValidationError = "Missing required field  'Step'";

                return false;
            }
            if (empty($HId)) {
                $this->lastValidationError = "Missing required field 'Helpdesk' ";

                return false;
            }
            if (empty($WId)) {
                $this->lastValidationError = "Missing required field  'Workflow' ";

                return false;
            }

            return true;
        } catch (\Exception $e) {
            throw new RtObjectValidationException("Unable to check RtTicket validity", null, $e);
        }
    }


    public function getId(){
        return $this->fields->getValue('Id');
    }

    public function setId($id){
        $this->fields->setValue('Id', $id);
    }


    //--------  common xMAP fields accessors ---------

    public function setHelpdesk($str)
    {
        $this->fields->setValue('Helpdesk', $str);
    }

    public function getHelpdesk()
    {
        return $this->fields->getValue('Helpdesk');
    }

    public function setDescription($str){
        $this->fields->setValue('Text', $str);
    }

    public function getDescription(){
        return null;
    }

    public function setCloseDate($date = null)
    {
        if ($date === null) {
            $date = gmdate("Y-m-d h:m", time());
        }
        $this->fields->setValue('Resolved', $date);
    }

    public function getCloseDate()
    {
        try {
            return $this->fields->getValue('Resolved');
        } catch (InvalidFieldException $ife) {
            return '';
        }
    }

    public function getSolution()
    {
        return $this->fields->getValue('Text');
    }

    public function getComment()
    {
        return $this->fields->getValue('Text');
    }

    public function setWorkflow($str)
    {
        $this->fields->setValue('Workflow', $str);
    }

    public function getWorkflow()
    {
        return $this->fields->getValue('Workflow');
    }

    public function setStep($str)
    {
        $this->fields->setValue('Step', $str);
    }

    public function getStep()
    {
        return $this->fields->getValue('Step');
    }

    public function setStepLabel($str)
    {
        $this->fields->setValue('StepLabel', $str);
    }

    public function getStepLabel()
    {
        return $this->fields->getValue('StepLabel');
    }

    public function setCommunity($str)
    {
        $this->fields->setValue('Community', $str);
    }

    public function getCommunity()
    {

            return 'CSI';
    }

    public function setSubCommunity($str)
    {
        $this->fields->setValue('SubCommunity', $str);
    }

    public function getSubCommunity()
    {
        try {
            $sC = $this->fields->getValue('SubCommunity');
        } catch (InvalidFieldException $tE) {
            $sC = null;
        }

        return $sC;

    }

    public function setGroupId($str)
    {


        $this->fields->setValue('GroupId', $str);
    }

    public function getGroupId()
    {

      return  $this->fields->getValue('GroupId');

    }



    public function setCustomer($str)
    {
        $this->fields->setValue('Customer', $str);
    }

    public function getCustomer()
    {
        return $this->fields->getValue('Customer');
    }

    public function setGroupUrl($str)
    {
        $this->fields->setValue('GroupUrl', $str);
    }

    public function getGroupUrl()
    {
        return $this->fields->getValue('GroupUrl');
    }


    public function setDue($str)
    {
        $this->fields->setValue('EndDate', $str);
    }

    public function getDue()
    {
        return $this->fields->getValue('EndDate');
    }



    public function setEndDate($str)
    {
        $this->fields->setValue('Due', $str);
    }

    public function getEndDate()
    {
        return $this->fields->getValue('Due');
    }




    //-------- common cMAP fields accessors ---------


    public function setQueue($str)
    {
        $this->fields->setValue('Queue', $str);
    }

    public function getQueue()
    {
        return $this->fields->getValue('Queue');
    }

    public function setOwner($str)
    {
        $this->fields->setValue('Owner', $str);
    }

    public function getOwner()
    {
        return $this->fields->getValue('Owner');
    }

    public function setAuthor($str)
    {
        $this->fields->setValue('Creator', $str);
    }

    public function getAuthor()
    {
        return $this->fields->getValue('Creator');
    }

    public function setSubject($str)
    {
        $this->fields->setValue('Subject', $str);
    }

    public function getSubject()
    {
        return $this->fields->getValue('Subject');
    }

    public function setPriority($str)
    {
        $this->fields->setValue('Priority', $str);
    }

    public function getPriority()
    {
        return $this->fields->getValue('Priority');
    }

    public function setInitialPriority($str)
    {
        $this->fields->setValue('InitialPriority', $str);
    }

    public function getInitialPriority()
    {
        return $this->fields->getValue('InitialPriority');
    }

    public function setFinalPriority($str)
    {
        $this->fields->setValue('FinalPriority', $str);
    }

    public function getFinalPriority()
    {
        return $this->fields->getValue('FinalPriority');
    }

    public function setStatus($str)
    {
        $this->fields->setValue('Status', $str);
    }

    public function getStatus()
    {
        try {
            return $this->fields->getValue('Status');
        } catch (InvalidFieldException $ife) {
            return '';
        }
    }

    public function setRequestors($str)
    {
        $this->fields->setValue('Requestors', $str);
    }

    public function getRequestors()
    {
        return $this->fields->getValue('Requestors');
    }

    public function setCarbonCopy($str)
    {
        $this->fields->setValue('Cc', $str);
    }

    public function getCarbonCopy()
    {
        try {
            return $this->fields->getValue('Cc');
        } catch (InvalidFieldException $ife) {
            return '';
        }
    }

    public function setAdminCc($str)
    {
        $this->fields->setValue('AdminCc', $str);
    }

    public function getAdminCc()
    {
        return $this->fields->getValue('AdminCc');
    }

    public function setNgi($str)
    {
        $this->fields->setValue('ngi', $str);
    }

    public function getNgi()
    {
        return $this->fields->getValue('ngi', true);
    }

    public function setSite($str)
    {
        $this->fields->setValue('site', $str);
    }

    public function getSite()
    {
        return $this->fields->getValue('site', true);
    }

    public function setCreationDate($str)
    {
        $this->fields->setValue('Created', $str);
    }

    public function getCreationDate()
    {
        return $this->fields->getValue('Created', true);
    }


    public function getEntityName($entityType)
    {
        if (strcasecmp($entityType, 'ngi') === 0) {
            return $this->getNgi();
        } elseif (strcasecmp($entityType, 'site') === 0) {
            return $this->getSite();
        } else {
            throw new \Exception("Unknown entity type, please use case insensitive : ngi|site ");
        }
    }

    public function getStarts()
    {
        return $this->fields->getValue('Starts');
    }

    public function setStarts($str)
    {
        $this->fields->setValue('Starts', $str);
    }
    public function setStarted($str)
    {
        $this->fields->setValue('Started', $str);
    }

    public function getStarted()
    {
        return $this->fields->getValue('Started');
    }

    public function setResolved($str)
    {
        $this->fields->setValue('Resolved', $str);
    }

    public function getResolved()
    {
        return $this->fields->getValue('Resolved');
    }

    public function setTold($str)
    {
        $this->fields->setValue('Told', $str);
    }

    public function getTold()
    {
        return $this->fields->getValue('Told');
    }

    public function setModificationDate($date){
        $this->fields->setValue('LastUpdated', $date);
    }

    public function getModificationDate(){
        return $this->fields->getValue('LastUpdated');
    }

    public function setTimeEstimated($str)
    {
        $this->fields->setValue('TimeEstimated', $str);
    }

    public function getTimeEstimated()
    {
        return $this->fields->getValue('TimeEstimated');
    }

    public function setTimeWorked($str)
    {
        return $this->fields->setValue('TimeWorked', $str);
    }

    public function getTimeWorked()
    {
        return $this->fields->getValue('TimeWorked');
    }

    public function setTimeLeft($str)
    {
        return $this->fields->setValue('TimeLeft', $str);
    }

    public function getTimeLeft()
    {
        return $this->fields->getValue('TimeLeft');
    }

    public function setConstituency($str)
    {
        return $this->fields->setValue('Constituency', $str);
    }

    public function getConstituency()
    {
        return $this->fields->getValue('Constituency');
    }

    public function setState($str)
    {
        return $this->fields->setValue('State', $str);
    }

    public function getState()
    {
        return $this->fields->getValue('State');
    }

    public function setIp($str)
    {
        return $this->fields->setValue('Ip', $str);
    }

    public function getIp()
    {
        return $this->fields->getValue('Ip');
    }

    public function getMetaEntry()
    {

        $allEntries = '';
        if ($this->fields->getValue('Comment', true)) {
            $allEntries = sprintf(
                "<h5>Entry : </h5>%s",
                $this->fields->getValue("Comment", true)
            );
        }

        if ($this->fields->getValue('Solution', true)) {
            if (!empty($allEntries)) {
                $allEntries .= "\n" . "\n";
            }
            $allEntries .= sprintf(
                "<h5>Solution :</h5>%s",
                $this->fields->getValue("Solution", true)
            );
        }

        if ($this->fields["GHD_Internal_Diary"]) {
            if (!empty($allEntries)) {
                $allEntries .= "\n" . "\n";
            }
            $allEntries .= sprintf(
                "<h5>Internal diary :</h5>%s",
                $this->fields["GHD_Internal_Diary"]
            );
        }

        if ($this->fields->getValue('RelatedIssue', true)) {
            if (!empty($allEntries)) {
                $allEntries .= "\n" . "\n";
            }
            $allEntries .= sprintf(
                "<h5>Related issue :</h5>%s",
                $this->fields->getValue("RelatedIssue", true)
            );
        }

        if (isset($this->fields['GHD_Change_Diary'])) {
            $trimedCD = trim($this->fields['GHD_Change_Diary']);
            if (!empty($trimedCD)) {
                if (!empty($allEntries)) {
                    $allEntries .= "\n" . "\n";
                }
                $allEntries .= sprintf(
                    "<h5>Change Diary :</h5>%s",
                    $this->fields['GHD_Change_Diary']
                );
            }
        }

        if (empty($allEntries)) {
            $allEntries .= sprintf(
                "<h5>GGUS Status : </h5>%s",
                $this->fields->getValue("Status", true)
            );
        }

        return $allEntries;
    }
    public function getResponsibleUnit()
    {
        return "";
//        return $this->fields->getValue('ngi');
    }

}