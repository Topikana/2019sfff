<?php

namespace AppBundle\Services\TicketingSystem\Ticket;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 30/01/14
 */

use \Lavoisier\Entries\Entries;
use GGUSHelpdesk\GgusObjectInterface;


class GGUSTicketEntry extends Entries implements ITicket,GgusObjectInterface
{

    public function init()
    {
        $this["Age"] = date_diff(new \DateTime(date("c")), new \DateTime($this["GHD_Create_Date"]))->days;
        $this['Age_Criticality'] = 'success';
        if ($this["Age"] > 20)
            $this['Age_Criticality'] = 'warning';
        if ($this["Age"] > 30)
            $this['Age_Criticality'] = 'danger';
    }

    public function getId()
    {
        return $this['GHD_Request_ID'];
    }

    public function getSite()
    {
        $site = 'N.A.';

        if (isset($this['GHD_Affected_Site']))
            $site = $this['GHD_Affected_Site'];

        return $site;
    }

    public function getNgi()
    {
        return $this['GHD_Responsible_Unit'];
    }

    public function getModifier()
    {
        return $this['GHD_Last_Modifier'];
    }

    public function getAuthor()
    {
        return $this['GHD_Submitter'];
    }

    public function getHelpdesk()
    {
        return $this['GHD_Soap_Client_Data']['Helpdesk'];
    }

    public function getWorkflow()
    {
        return $this['GHD_Soap_Client_Data']['Workflow'];
    }

    public function getAge()
    {
        return $this['Age'];
    }

    public function getAgeCriticality()
    {
        return $this['Age_Criticality'];
    }

    public function getStep()
    {
        return $this['GHD_Soap_Client_Data']['Step'];
    }

    public function getStepLabel()
    {
        if (isset($this['GHD_Soap_Client_Data']['StepLabel'])) {

            return $this['GHD_Soap_Client_Data']['StepLabel'];
        } else {
            return 'N.A.';
        }
    }

    public
    function getGroupId()
    {
        return $this['GHD_Soap_Client_Data']['GroupId'];
    }

    public function setGroupId($str)
    {
        $this['GHD_Soap_Client_Data']['GroupId'] = $str;
    }

    public
    function getGroupUrl()
    {
        return $this['GHD_Soap_Client_Data']['GroupUrl'];
    }

    public
    function getStatus()
    {
        return $this['GHD_Status'];
    }

    public
    function getMetaStatus()
    {
        return $this->getStatus();
    }

    public
    function getFreshness()
    {
        return $this['Status'];
    }

    public
    function getUpdatedAt()
    {
        return $this['GHD_Modified_Date'];
    }

    public
    function getSubject()
    {
        return $this['GHD_Subject'];
    }

    public
    function getEndDate()
    {
        return $this['GHD_Soap_Client_Data']['EndDate'];
    }

    public
    function getCloseDate() {
        return $this['GHD_Soap_Client_Data']['CloseDate'];
    }

    public
    function setCloseDate($date = null) {
        if ($date === null) {
            $date = gmdate("Y-m-d", time());
        }
        return $this['GHD_Soap_Client_Data']['CloseDate'] = $date;
    }


    public function getPriority() {
        return $this['GHD_Priority'];
    }

    public function getCommunity() {
        return $this['GHD_Soap_Client_Data']['Community'];
    }

    public function getSubCommunity() {
        return $this['GHD_Soap_Client_Data']['SubCommunity'];
    }

    // import/export ticket to helpdesk/workflow message format
    public function toHelpdesk(){
        $helpdeskFormat = $this->getArrayCopy();
        $helpdeskFormat["GHD_Soap_Client_Data"] = $this['GHD_Soap_Client_Data']->asXmlEntries();

        return $helpdeskFormat;
    }

    public function getModificationDate(){
        throw new \Exception("Please implement me !!");
    }

    public function toWorkflow(){
        throw new \Exception("Please implement me !!");
    }

    public function fromHelpdesk($data){
        throw new \Exception("Please implement me !!");
    }

    public function fromWorkflow(array $step){
        throw new \Exception("Please implement me !!");
    }

    // following functions are really usefull for creation phase
    public function isValidForCreation($safe) {return true;}
    public function isValidForModification() {return true;}
    public function getLastValidationError() {return true;}

    /**
     * Get value using helpdesk identifier
     * @param <string> $key of value to retreive
     * @return <mixed> value if found, null otherwise
     * @author olivier lequeux
     */
    public function getHelpdeskValue($id)
    {
        if (isset($this[$id])) {
            return $this[$id];
        } else {
            return null;
        }
    }

    /**
     * Set values using helpdesk identifiers
     * @param <array> associative array of values to set
     * @return void
     * @author olivier lequeux
     */
    public function setHelpdeskValues(array $map = null)
    {

        if (is_array($map)) {
            foreach ($map as $key => $value) {
                $this[$key] = $value;
            }
        }
    }

    public function fromTicket(ITicket $src) {
        $src_data = $src->toWorkflow();
        $this->fromWorkflow($src_data);
    }

}