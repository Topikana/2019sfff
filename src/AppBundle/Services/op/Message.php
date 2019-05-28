<?php

namespace AppBundle\Services\op;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Swift_Message;

/**
 *  Message provides a layout to add generic behaviour for messages sent with SwiftMailer library
 *
 * @author Olivier LEQUEUX
 */
class Message {

    private $subjectPrefix = '';

    private $container;

    /**
     * @var $mail Swift_Message
     */
    private $mail;

    public function __construct(ContainerInterface $container, $subject = null, $body = null, $to =null, $subjectPrefix = '') {


        $this->container = $container;
        $this->subjectPrefix = $subjectPrefix;

        $mail = \Swift_Message::newInstance();


        if ($this->container ->get("kernel")->getEnvironment() != 'prod') {
            if (is_array($to)) {
                $recipients = implode("\n", $to);
            } else {
                $recipients = implode("\n", array($to));
            }


            $message = $body."\n" . "---- SHOULD BE SENT TO ----" . "\n" . $recipients;
            $mail->setTo($this->getDefaultSender());


        } else {
            $message = $body;

            $mail->setTo($to);


        }

        $mail->setFrom($this->getDefaultSender());

        $mail->setReplyTo($to);

        $mail->setBody($message);


        $this->setMail($mail);

        $this->setSubject($subject);


    }

    /**
     * get swift mailer object
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }


    /**
     * @param mixed $mail
     */
    public function setMail(\Swift_Message $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @param $cc
     */
    public function setCc($cc) {
        $this->mail->setCc($cc);
    }


    public function addCc($cc) {
        $this->mail->addCc($cc);
    }


    public function addBcc($bcc) {
        $this->mail->addBcc($bcc);
    }

    /**
     * @param $to
     */
    public function setReplyTo($to) {
        $this->mail->setReplyTo($to);
    }


    /**
     * set subject with kernel env and prefix if exists
     * @param $subject
     */
    public function setSubject($subject) {

        if(!empty($this->subjectPrefix)) {
            $subject = sprintf("[ %s%s ] %s", $this->getSfEnv(), $this->subjectPrefix, $subject);
        } else {
            if ($this->getSfEnv() != '') {
                $subject = sprintf("[ %s ] %s", $this->getSfEnv(), $subject);
            }
        }

        $this->mail->setSubject($subject);
    }

    /**
     * set the content type
     */
    public function setContentType($type) {
        $this->mail->setContentType($type);

    }

    /**
     * get kernel env
     * @return string
     * @throws \Exception
     */
    private function getSfEnv() {
        return (($this->container ->get("kernel")->getEnvironment() == 'prod') ? '' :$this->container ->get("kernel")->getEnvironment() .'/');
    }

    /**
     * get default sender : cic-information
     * @return array
     */
    private function getDefaultSender() {
        return array($this->container ->getParameter("webMasterMail") => 'Operations-portal');
    }

}
