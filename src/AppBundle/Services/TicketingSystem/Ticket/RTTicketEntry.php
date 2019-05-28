<?php

namespace AppBundle\Services\TicketingSystem\Ticket;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 30/01/14
 */

use \Lavoisier\Entries\Entries;
use GGUSHelpdesk\GgusObjectInterface;


class RTTicketEntry extends Entries implements ITicket,GgusObjectInterface
{

    public function init()
    {

        $this["Age"] = date_diff(new \DateTime(date("c")), new \DateTime($this["Created"]))->days;
        $this['Age_Criticality'] = 'success';
        if ($this["Age"] > 20)
            $this['Age_Criticality'] = 'warning';
        if ($this["Age"] > 30)
            $this['Age_Criticality'] = 'danger';
    }

    public function getId()
    {
        return $this['id'];
    }

    public function getSite()
    {
        $site = 'N.A.';

        if (isset($this['site']))
            $site = $this['site'];

        return $site;
    }

    public function getNgi()
    {
        return $this['ngi'];
    }

    public function getModifier()
    {
//        return $this['GHD_Last_Modifier'];
        return $this["Owner"];
    }

    public function getAuthor()
    {
        return $this['Creator'];
    }

//    public function getHelpdesk()
//    {
//        return $this['GHD_Soap_Client_Data']['Helpdesk'];
//    }
//
//    public function getWorkflow()
//    {
//        return $this['GHD_Soap_Client_Data']['Workflow'];
//    }

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
        return $this["Status"];
    }

    public function getStepLabel()
    {
       return $this["Status"];
    }

    public
    function getGroupId()
    {

        return $this["CF.{GroupOfIssuesId}"];
    }


//    public function setGroupId($str)
//    {
//        $this['GHD_Soap_Client_Data']['GroupId'] = $str;
//    }
//
//    public
//    function getGroupUrl()
//    {
//        return $this['GHD_Soap_Client_Data']['GroupUrl'];
//    }

    public
    function getStatus()
    {
        return $this['Status'];
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
        return $this['LastUpdated'];
    }

    public
    function getSubject()
    {
        return $this['Subject'];
    }

    public
    function getEndDate()
    {
        return $this['Due'];
    }

    public
    function getCloseDate() {
        return $this['Resolved'];
    }

    public
    function setCloseDate($date = null) {
        if ($date === null) {
            $date = gmdate("Y-m-d", time());
        }
        return $this['Resolved'] = $date;
    }


    public function getPriority() {
        return $this['Priority'];
    }

    public function getCommunity() {
        return 'CSI';
    }

    public function getSubCommunity() {
        return 'CSI';
    }

    // import/export ticket to helpdesk/workflow message format
    public function toHelpdesk(){
        $helpdeskFormat = $this->getArrayCopy();

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