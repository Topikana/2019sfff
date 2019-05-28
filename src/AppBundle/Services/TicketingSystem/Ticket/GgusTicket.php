<?php

namespace AppBundle\Services\TicketingSystem\Ticket;

use \GGUSHelpdesk\Exceptions\GgusObjectValidationException;
use \GGUSHelpdesk\Exceptions\InvalidFieldException;
use \GGUSHelpdesk\GgusObject;
use \GGUSHelpdesk\Fields;

/**
 * dedicated to basic GGUS ticket behavior
 * @author Olivier LEQUEUX
 */
abstract class GgusTicket extends GgusObject implements IAttachmentHostable
{
    protected $attachmentData = null;
    protected $attachmentName = "attachment";

    public function __construct(Fields $fields)
    {
        $this->fields = $fields;
        $this->lastValidationError = null;
        $fields->setExtendedFieldsFactory(new EntriesFieldsFactory());
        $fields->initExtendedFields();
    }



    public function fromHelpdesk($data)
    {

        $this->fields->fromArray(get_object_vars($data));
        if (isset($data->GHD_Soap_Client_Data)) {
//            $this->fields->setXmlFieldsString($data->GHD_Soap_Client_Data);
            $this->fields->setExtendedFieldsFromSerializedObject($data->GHD_Soap_Client_Data);
        }
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

        // specific cases of attachment, not directly managed by GGUS helpdesk
        if(isset($step['AttachmentData'])) {
            $this->attachmentData = $step['AttachmentData'];
        }
        if(isset($step['AttachmentName'])) {
            $this->attachmentName = $step['AttachmentName'];
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

            $subject = trim($this->fields['GHD_Subject']);
            $modifier = trim($this->fields['GHD_Last_Modifier']);
            $commId = trim($this->fields->getValue('Community'));
            $stepId = trim($this->fields->getValue('Step'));
            $HId = trim($this->fields->getValue('Helpdesk'));
            $WId = trim($this->fields->getValue('Workflow'));

            if (empty($subject)) {
                $this->lastValidationError = "Missing required field 'Subject'";

                return false;
            }
            if (empty($commId)) {
                $this->lastValidationError = "Missing required field 'Community' ";

                return false;
            }
            if (empty($modifier)) {
                $this->lastValidationError = "Missing field 'Modifier'";

                return false;
            }
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
            throw new GgusObjectValidationException("Unable to check GgusTicket validity", null, $e);
        }
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

    public function setCloseDate($date = null)
    {
        if ($date === null) {
            $date = gmdate("Y-m-d", time());
        }
        $this->fields->setValue('CloseDate', $date);
    }

    public function getCloseDate()
    {
        try {
            return $this->fields->getValue('CloseDate');
        } catch (InvalidFieldException $ife) {
            return '';
        }
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
        return $this->fields->getValue('Community');
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
        return $this->fields->getValue('GroupId');
    }

    public function setGroupUrl($str)
    {
        $this->fields->setValue('GroupUrl', $str);
    }

    public function getGroupUrl()
    {
        return $this->fields->getValue('GroupUrl');
    }

    public function setEndDate($str)
    {
        $this->fields->setValue('EndDate', $str);
    }

    public function getEndDate()
    {
        return $this->fields->getValue('EndDate');
    }


    //-------- common cMAP fields accessors ---------


    public function setAuthor($str)
    {
        $this->fields->setValue('Author', $str);
    }

    public function getAuthor()
    {
        return $this->fields->getValue('Author');
    }

    public function setAuthorDn($str)
    {
        $this->fields->setValue('AuthorDn', $str);
    }

    public function getAuthorDn()
    {
        return $this->fields->getValue('AuthorDn');
    }

    public function setAuthorEmail($str)
    {
        $this->fields->setValue('AuthorEmail', $str);
    }

    public function getAuthorEmail()
    {
        return $this->fields->getValue('AuthorEmail');
    }

    public function setSubject($str)
    {
        $this->fields->setValue('Subject', $str);
    }

    public function getSubject()
    {
        return $this->fields->getValue('Subject');
    }

    public function setDescription($str)
    {
        $this->fields->setValue('Description', $str);
    }

    public function getDescription()
    {
        return $this->fields->getValue('Description');
    }

    public function setComment($str)
    {
        $this->fields->setValue('Comment', $str);
    }

    public function getComment()
    {
        return $this->fields->getValue('Comment');
    }

    public function setSolution($str)
    {
        $this->fields->setValue('Solution', $str);
    }

    public function getDiary()
    {
        return $this->fields->getValue('Diary');
    }

    public function setDiary($str)
    {
        $this->fields->setValue('Diary', $str);
    }

    public function getSolution()
    {
        return $this->fields->getValue('Solution');
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

    public function getMetaStatus()
    {
        try {
            return $this->fields->getValue('MetaStatus');
        } catch (InvalidFieldException $ife) {
            return '';
        }
    }


    public function setProblemType($str)
    {
        $this->fields->setValue('ProblemType', $str);
    }

    public function getProblemType()
    {
        return $this->fields->getValue('ProblemType');
    }

    public function setPriority($str)
    {
        $this->fields->setValue('Priority', $str);
    }

    public function getPriority()
    {
        return $this->fields->getValue('Priority');
    }

    public function setCarbonCopy($str)
    {
        $this->fields->setValue('CarbonCopy', $str);
    }

    public function getCarbonCopy()
    {
        try {
            return $this->fields->getValue('CarbonCopy');
        } catch (InvalidFieldException $ife) {
            return '';
        }
    }

    public function setInvolve($str)
    {
        $this->fields->setValue('Involve', $str);
    }

    public function getInvolve()
    {
        return $this->fields->getValue('Involve');
    }

    public function setSite($str)
    {
        $this->fields->setValue('Site', $str);
    }

    public function getSite()
    {
        return $this->fields->getValue('Site', true);
    }

    public function setNgi($str)
    {
        $this->fields->setValue('Ngi', $str);
    }

    public function getNgi()
    {
        return $this->fields->getValue('Ngi', true);
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

    public function getCreationDate()
    {
        return $this->fields->getValue('CreationDate');
    }

    public function setResponsibleUnit($str)
    {
        $this->fields->setValue('ResponsibleUnit', $str);
    }

    public function getResponsibleUnit()
    {
        return $this->fields->getValue('ResponsibleUnit');
    }

    public function setNotificationStrategy($str)
    {
        $this->fields->setValue('NotificationStrategy', $str);
    }

    public function getNotificationStrategy()
    {
        return $this->fields->getValue('NotificationStrategy');
    }

    public function setVo($str)
    {
        $this->fields->setValue('Vo', $str);
    }

    public function getVo()
    {
        return $this->fields->getValue('Vo');
    }

    public function getXmlField()
    {
        return $this->fields['GHD_Soap_Client_Data'];
    }

    public function getRelatedIssue()
    {
        return $this->fields->getValue('RelatedIssue');
    }

    public function setRelatedIssue($str)
    {
        return $this->fields->setValue('RelatedIssue', $str);
    }


    public function getMetaEntry()
    {

        $allEntries = '';
        if ($this->fields->getValue('Comment', true)) {
            $allEntries = sprintf(
                "<h5>Public diary : </h5>%s",
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



    /**
     * @param string $attachmentName
     */
    public function setAttachmentName($attachmentName)
    {
        $this->attachmentName = $attachmentName;
    }

    /**
     * @return string
     */
    public function getAttachmentName()
    {
        return $this->attachmentName;
    }

    /**
     * @param string $attachmentData
     */
    public function setAttachmentData($attachmentData)
    {
        $this->attachmentData = $attachmentData;
    }

    /**
     * @return null if not set, string otherwise
     */
    public function getAttachmentData()
    {
        return $this->attachmentData;
    }



}