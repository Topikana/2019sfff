<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;

use AppBundle\Services\TicketingSystem\Ticket\GgusAttachment;
use AppBundle\Services\TicketingSystem\Helpdesk\Exceptions\HelpdeskOperationException;
use AppBundle\Services\TicketingSystem\Ticket\IAttachmentHostable;
use \GGUSHelpdesk\GgusSoapService;

class AttachmentService extends GgusSoapService {

    private $attachValidation = true;

    function createGgusObjectInstance($setDefaultValues = true)
    {
        $t = new GgusAttachment($this->getGgusFieldInstance());
        return $t;
    }

    public function buildGgusObjectPermaLink($pLinkId)
    {
        return sprintf('%s?mode=download&attid=%s', $this->config['urls']['site'], $pLinkId);
    }

    private function isAttachmentWellformed(GgusAttachment $attach){
        return parent::isGgusObjectWellformed($attach);
    }

    public function createAttachment(GgusAttachment $attach){
        $errorMessage = null;
        if ($this->attachValidation === true) {
            $validity = $this->isAttachmentWellformed($attach);
            if ($validity === true) {
                $validity = $attach->isValidForCreation(false);
                $errorMessage = $attach->getLastValidationError();
            } else {
                $errorMessage = $this->getLastGgusObjectFormatError();
            }
        } else {
            $validity = true;
        }
        if ($validity === true) {
            $result = $this->__setGgusObject(
                $attach,
                $this->config['methods']['create'],
                $this->getIdParamName());
            return $result;
        } else {
            throw new HelpdeskOperationException(
                sprintf("Attachment creation aborted, attachment is not properly set : %s ", $errorMessage));
        }

    }

    public function createAttachmentFromHost(IAttachmentHostable $host){
        $attachment = $this->createGgusObjectInstance();
        $attachment->setName($host->getAttachmentName());
        $attachment->setData($host->getAttachmentData());
        $attachment->setTicketId($host->getId());
        $attachment->setModifier($host->getAuthor());

        return $this->createAttachment($attachment);

    }

    protected function getIdParamName()
    {
        return 'GAT_Attachment_ID';
    }

}