<?php

namespace AppBundle\Services\TicketingSystem;

use AppBundle\Services\TicketingSystem\Ticket\ITicket;
use AppBundle\Services\TicketingSystem\Helpdesk\IHelpdesk;

class HelpdeskTasks
{
    private $logs = array();
    private $logLevel = self::INFO;
    const DEBUG = 0;
    const INFO = 1;
    const WARN = 2;
    const ERROR = 3;

    public function setLogLevel($logLevel)
    {
        $this->loglevel = $logLevel;
    }

    public function isTicketOpened(ITicket $ticket)
    {

        $status = (
            ($ticket->getStatus() !== 'solved') &&
            ($ticket->getStatus() !== 'verified') &&
            ($ticket->getStatus() != 'closed') &&
            ($ticket->getStatus() != 'unsolvable')

        );

        return
            (strcasecmp(trim($ticket->getModifier()), 'Guenter Grein') !== 0) && ($status === true);

    }

    /**
     * quick close a list of tickets with a static message
     */
    public function quickCloseByIds(IHelpdesk $helpdesk, array $ids)
    {

        $get_success = array();
        foreach ($ids as $id) {
            try {
                $t = $helpdesk->getTicket($id);
                if ($t !== null) {
                    array_push($get_success, $t);
                } else {
                    $this->pushLog(sprintf("[ERROR] 'NULL' RETURNED, UNABLE TO GET TICKET : %s", $id));
                }
            } catch (\Exception $e) {
                $this->pushLog(sprintf("[ERROR] EXCEPTION RAISED, UNABLE TO GET TICKET : %s", $id));

            }
        }

        $this->quickClose($helpdesk, $get_success);
    }


    /**
     * quick close a list of tickets with a static message
     */
    public function quickClose(IHelpdesk $helpdesk, array $tickets)
    {

        $get_success = array();
        $helpdesk->setLavoisierNotification(false);
        $helpdesk->setTicketValidation(false);

        foreach ($tickets as $t) {

            try {
                if (($this->isTicketOpened($t) === true)) {
                    array_push($get_success, $t);
                } else {
                    if ($this->logLevel >= self::WARN) {
                        $this->pushLog(sprintf("[WARN] TICKET ALREADY CLOSED: %s",
                            $helpdesk->getTicketPermaLink($t->getId())));
                    }
                }

            } catch (\Exception $e) {
                $this->pushLog(sprintf("[ERROR] FAILED TO GET TICKET: %s" . "\n" . "%s" . "\n" . "%s",
                    $helpdesk->getTicketPermaLink($t->getId()),
                    $e->getMessage(),
                    $e->getTraceAsString()));
            }
        }

        $this->pushLog(sprintf("[INFO] >> Found %s tickets to close..." . "\n\n", count($get_success)));

        //close tickets
        foreach ($get_success as $t) {
            try {
                $t->setStatus('closed');
                $t->setSolution("Ticket closed by OPS-TEAM");
                $t->setCommunity("UNKNOWN");
                $helpdesk->updateTicket($t);

                $this->pushLog(sprintf("[INFO] CLOSE SUCCESSFUL: %s - %s ",
                        $t->getId(),
                        $helpdesk->getTicketPermaLink($t->getId())
                    )
                );
            } catch (\Exception $e) {
                $this->pushLog(
                    sprintf("[ERROR] CLOSE FAILED : %s - %s " . "\n" . "%s" . "\n" . "%s",
                        $t->getId(),
                        $helpdesk->getTicketPermaLink($t->getId()),
                        $e->getMessage(),
                        $e->getTraceAsString()));
            }
        }


    }

    private function pushLog($msg, $out = true)
    {
        array_push($this->logs, $msg);
        if ($out === true) {
            echo $msg . "\n";
        }
    }

    private function cleanLogs()
    {
        $this->logs = array();
    }

    public function dumpLogs()
    {
        foreach ($this->logs as $log) {
            echo $log . "\n";
        }
    }

    public function wellFormedConvert($result)
    {
        try {
            $formed = true;

           if (!isset($result['Workflow']))
                $formed = false;
            if (!isset($result['Step']))
                $formed = false;
            if (!isset($result['StepLabel']))
                $formed = false;
            if (!isset($result['Community']))
                $formed = false;
            if (!isset($result['Id']))
                $formed = false;

            return $formed;

        } catch (exception $e) {
            throw new exception($e->getMessage());
        }
    }


    public function convertTickets($helpdesk, $results, $env = "test")
    {

        $results = $results->getArrayCopy();
        $wellFormed = 0;
        $malFormed = 0;

        foreach ($results as $result) {

            if ($this->wellFormedConvert($result)) {
                $wellFormed++;
                //$this->convertTicket($helpdesk, $result);
                $this->setNGIs($helpdesk, $result);
            } else {
                $malFormed++;
                $this->pushLog(sprintf("[ERROR] CONVERSION ERROR : %s - %s ",
                    $result['Id'],
                    $helpdesk->getTicketPermaLink($result['Id'])
                ));
            }
        }
        $this->pushLog(sprintf("[INFO] TICKETS WELL FORMED : %s ", $wellFormed));
        $this->pushLog(sprintf("[INFO] TICKETS MALFORMED : %s ", $malFormed));
    }


    public
    function setNGIs($helpdesk, $result)
    {

        $ticket = $helpdesk->getTicketInstance();
        $ticket->fromWorkflow($result);
        $id = $ticket->getId();
        $ggusTicket = $helpdesk->getTicket($id);

        $TNgi = null;
        try {
            $TNgi = $ggusTicket->getNgi();
        } catch (\Exception $e) {
            $this->pushLog(sprintf("[WARN] Ticket NGI not FOUND : %s",
                $helpdesk->getPermaLink($ggusTicket->getId())));
        }

        if ((null === $TNgi) || (empty($TNgi))) {
            $eService = new \OpsLavoisier\Services\EntityService('cclavoisier01.in2p3.fr');

            try {
                $ngiName = $eService->getNGINameByGgusSU($ggusTicket->getResponsibleUnit());
            } catch (\Exception $e) {
                $this->pushLog(sprintf("[WARN] Unable to deduce NGI from GGUS SU : %s",
                    $ggusTicket->getResponsibleUnit()));
            }
            if ((null === $ngiName) || (empty($ngiName))) {
                $ggusTicket->setNgi($ngiName);
                $this->pushLog(sprintf("[INFO]Set NGI : %s - %s "));
            }

        }
    }


}