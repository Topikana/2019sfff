<?php

namespace AppBundle\Services\JSTree\Renderer;

use AppBundle\Services\JSTree\RecipientCollection;
use AppBundle\Services\JSTree\Renderer\TargetRenderer;

class EmailRenderer extends TargetRenderer
{
    const EMAIL_SEPARATOR_COMMA = ',';
    const EMAIL_SEPARATOR_SEMI_COMMA = ';';
    const REGEX_EMAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';

    // historical code
    private $globalTmpList = array();

    private $recipients;

    public function __construct($tree, RecipientCollection $recipients)
    {

        $this->recipients = $recipients;

        parent::__construct($tree);
    }

    /**
     *  check & add mail to globalTmpList member
     * @author <Olivier Lequeux>
     * @param <string> $email, email address to check
     * @param <bool> true if mail is valid, false otherwise
     */
    private function addEmailToGlobalTmpList($item, $address)
    {
        if (!strcmp($address, '') == 0) {
            if ((bool)(preg_match(self::REGEX_EMAIL, $address))) {
                $matchedItem = $this->recipients->matchItem(
                    $item,
                    RecipientCollection::DATA_TYPE_LABEL,
                    true);
                $this->globalTmpList[$address] = $this->recipients->getName() . '/' .$matchedItem ;
            }
        }
    }

    /**
     *  Update globalTmpList member with all requested Emails separated by a coma
     *  + trim address
     * @param <array> $Ids, array from jstree arranged
     * @author <Olivier Lequeux>
     */
    private function updateEmailList($Ids)
    {

        foreach ($Ids as $item) {
            if (is_array($item)) {
                $this->updateEmailList($item);
            } else {
                if ($this->recipients->checkItem($item, RecipientCollection::DATA_TYPE_EMAIL)) {
                    // trim start/end characters
                    $tmp_email = trim($this->recipients->matchItem(
                        $item,
                        RecipientCollection::DATA_TYPE_EMAIL,
                    true));
                    // replace spaces
                    $tmp_email = str_replace(' ', '', $tmp_email);
                    // manage  concatenated addresses
                    if (strpos($tmp_email, self::EMAIL_SEPARATOR_COMMA) !== false) {
                        $res = explode(self::EMAIL_SEPARATOR_COMMA, $tmp_email);

                        foreach ($res as $address) {
                            $this->addEmailToGlobalTmpList($item, $address);
                        }
                    } else if (strpos($tmp_email, self::EMAIL_SEPARATOR_SEMI_COMMA) !== false) {
                        $res = explode(self::EMAIL_SEPARATOR_SEMI_COMMA, $tmp_email);

                        foreach ($res as $address) {
                            $this->addEmailToGlobalTmpList($item, $address);
                        }
                    } else {
                        $this->addEmailToGlobalTmpList($item, $tmp_email);
                    }
                }
            }
        }
    }

    public function render()
    {
        $this->globalTmpList = array();
        $this->updateEmailList(self::convert($this->tree, self::MAIL_PRUNING));
        return $this->globalTmpList;
    }
}