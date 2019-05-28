<?php

/**
 * Provide class to handle a Workflow
 * @author Olivier LEQUEUX
 */

namespace AppBundle\Services\TicketingSystem\Workflow;

use AppBundle\Services\TicketingSystem\Exceptions\WorkflowException;
use AppBundle\Services\TicketingSystem\Workflow\Exceptions\InvalidRequestParameterException;
use AppBundle\Services\TicketingSystem\Form\TForm;
use AppBundle\Services\TicketingSystem\Ticket\ITicket;

class Workflow
{

    const MODE_VERBOSE = 'verb';

    protected $schema;
    protected $steps;
    protected $formItemFactoryClass;
    protected $defaultStepId;
    protected $serviceIdentifier;
    protected $mode;

    protected $stepInSchema;
    protected $nbStepInSchema;

    public function __construct($schema, array $steps, $serviceIdentifier)
    {

        $this->schema = $schema;
        $this->steps = $steps;
        $this->formItemFactoryClass = null;
        $this->defaultStepId = null;
        $this->mode = self::MODE_VERBOSE;
        $this->serviceIdentifier = $serviceIdentifier;
        $this->stepInSchema = array_keys($this->schema);
        $this->nbStepInSchema = count($this->schema) - 1; // -1 to remove upd case

    }

    public function getIdentifier()
    {
        return $this->serviceIdentifier;
    }

    public function getStepSuccessors($stepId)
    {
        return (isset($this->schema[$stepId]['next']) ? $this->schema[$stepId]['next'] : null);
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getStepLabel($stepId)
    {
        return (isset($this->schema[$stepId]['label']) ? $this->schema[$stepId]['label'] : null);
    }

    public function setDefaultStepId($stepId)
    {
        $this->defaultStepId = $stepId;
    }

    /*
     * return step rate : (step position / step number) * 100
     * @param <string> stepId
     */
    public function getStepRate($stepId)
    {

        $key = array_search($stepId, $this->stepInSchema);
        if ($key !== false) {
            return round((100 * ($key + 1)) / $this->nbStepInSchema);
        } else {
            return null;
        }
    }


    public function setMode($mode)
    {
        if ($mode === self::MODE_VERBOSE) {
            $this->$mode = $mode;
        } else {
            throw new \Exception  ("The mode '$mode' is unknown in workflow.");
        }
    }

    // consider that ticket has been filled with proper StepId($ticket->getStep())
    public function ticketFromForm(TForm $form, ITicket $ticket)
    {
        $ticket->fromWorkflow($form->getTSValues());
        // update step label if has changed
        $ticket->setStepLabel($this->getStepLabel($ticket->getStep()));
        return $ticket;

    }


    public function stepFromStepId($stepId, array $clientValues = array(), ITicket $previousTicket = null)
    {

        if (isset($this->steps[$stepId])) {
            $step = $this->steps[$stepId];
        } else {
            throw new InvalidRequestParameterException(
                "Unable to find stepId '$stepId' in '" . $this->getIdentifier() . "' workflow ");
        }


        return $this->parseStep($step, $clientValues, $previousTicket);
    }

    public function ticketFromStepId($stepId, ITicket $previousTicket, array $clientValues = array(), $updateStep = false)
    {
        if($updateStep === true) {
            $previousTicket->setStepLabel($this->getStepLabel($stepId));
            $previousTicket->setStep($stepId);
        }
        $step = $this->stepFromStepId($stepId, $clientValues, $previousTicket);
        $this->importStepInTicket($step, $previousTicket);

        return $previousTicket;
    }


    public function getNextStep(array $clientValues, ITicket $previousTicket = null, $offset = null)
    {

        $nextSafeStepId = $this->getNextSafeStepId($previousTicket, $offset);
        $nextStep = $this->parseStep($this->steps[$nextSafeStepId], $clientValues, $previousTicket);
        return $nextStep;
    }


    public function getNextTicket(ITicket $nextTicket, array $clientValues, ITicket $previousTicket = null, $offset = null)
    {

        $nextSafeStepId = $this->getNextSafeStepId($previousTicket, $offset);
        $nextStep = $this->parseStep($this->steps[$nextSafeStepId], $clientValues, $previousTicket);
        $this->importStepInTicket($nextStep, $nextTicket);
        return $nextTicket;
    }


    public function getNextStepId($stepId, $offset)
    {

        $stepSucc = $this->getStepSuccessors($stepId);
        if (null !== $stepSucc) {
            foreach ($stepSucc as $sId => $sOffset) {
                if (strcmp($offset, $sOffset) === 0) {
                    return $sId;
                }
            }
        } else {
            return null;
        }
    }


    public function getNextSafeStepId(ITicket $previousTicket = null, $offset = null)
    {
        if ($previousTicket === null) {
            $nextStepId = $this->defaultStepId;
        } else {

            $previousStepId = $previousTicket->getStep();
            $nextStepId = $this->getNextStepId($previousStepId, $offset);

            if ($nextStepId === null) {
                throw new WorkflowException("Unable to retreive next step Id with the tuple : $previousStepId / $offset");
            }
        }

        return $nextStepId;
    }


    /**
     * Replace %xxx% values of step configuration by values from user
     * sort step using 'order' field specified in each item
     * @param <Step> $step to parse
     * @param <array> $clientValues, associative array of values to replace from client
     * @param <ITicket> $ticket, ticket where ticketValues can be found
     * @param <array> $requestValues,  associative array of values to replace from request
     * @return <str> step with values replaced if found
     * @author olivier lequeux
     */
    public function parseStep(Step $step, array $clientValues, ITicket $ticket = null, array $requestValues = array())
    {

        $ticketValues = array();
        if (!is_null($ticket)) {
            $ticketValues = $ticket->toWorkflow();
        }

        $patternsC = $patternsT = $patternsR = array();

        foreach ($clientValues as $key => $item) {
            $patternsC[] = '/\+C.' . $key . '\+/';
        }

        foreach ($ticketValues as $key => $item) {
            $patternsT[] = '/\+T.' . $key . '\+/';
        }

        foreach ($requestValues as $key => $item) {
            $patternsR[] = '/\+R.' . $key . '\+/';
        }

        $stepItems = array();
        foreach ($step as $index => $item) {
            foreach ($item as $itemName => $value) {

                $stepItems[$index][$itemName] = preg_replace($patternsC, $clientValues, $value);
                $stepItems[$index][$itemName] = preg_replace($patternsT, $ticketValues, $stepItems[$index][$itemName]);
                $stepItems[$index][$itemName] = preg_replace($patternsR, $requestValues, $stepItems[$index][$itemName]);

            }
        }

        //create new Step and finally sort them
        $step = new Step($stepItems);
        $step->sort();

        return $step;

    }

    /**
     * browse each item of step and set matching value to ticket
     * @param <array> $step
     * @param <ITicket> &$ticket, ticket reference to modify
     * @return void
     */
    public function importStepInTicket($step, ITicket &$ticket)
    {

        $tmpMap = array();
        foreach ($step as $item) {

            if (isset($item['value'])) {
                $tmpMap[$item['id']] = $item['value'];
            }
        }
        $ticket->fromWorkflow($tmpMap);

    }

}

