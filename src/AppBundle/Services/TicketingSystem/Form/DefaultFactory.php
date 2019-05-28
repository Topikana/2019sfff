<?php

/**
 * Build widgets and validators in given context
 *
 * @author Olivier LEQUEUX
 */

namespace AppBundle\Services\TicketingSystem\Form;

use AppBundle\Services\TicketingSystem\Workflow\Step;
use AppBundle\Services\TicketingSystem\Helpdesk\IHelpdesk;
use AppBundle\Services\TicketingSystem\Form\Widget\OpWidgetFormEndDate;
use AppBundle\Services\TicketingSystem\Form\Widget\OpWidgetFormCarbonCopy;
use AppBundle\Services\TicketingSystem\Form\IFormItemFactory;

// todo : Widget not exist in sf3
class DefaultFactory implements IFormItemFactory
{

    private $helpdesk;


    public function __construct($helpdesk)
    {
        $this->helpdesk = $helpdesk;
    }


    public function createFormItem($step)
    {
        $stepId = $step['id'];
        if (method_exists($this, $stepId)) {
            return $this->$stepId($step);
        } else {
            throw new \Exception(
                "'$stepId' method is not available in " . __CLASS__ . "class. Check 'id' values of the step",
                0,
                null);
        }
    }

    public function buildSelectComponents($stepItem, $choices, $combine = true)
    {
        if ($combine === true)
            $widget = new \sfWidgetFormSelect(array('choices' => array_combine($choices, $choices)));
        else
            $widget = new \sfWidgetFormSelect(array('choices' => $choices));
        $validator = new \sfValidatorPass();
        return new FormItem($stepItem, $widget, $validator);
    }

    public function buildInputComponent($stepItem)
    {
        $widget = new \sfWidgetFormInputText();
        $validator = new \sfValidatorPass();
        return new FormItem($stepItem, $widget, $validator);
    }


    public function Priority(array $stepItem)
    {
        $priorityList = $this->helpdesk->getPriorities();
        return $this->buildSelectComponents($stepItem, $priorityList);
    }

    public function ResponsibleUnit(array $stepItem)
    {
        $SUList = $this->helpdesk->getSupportUnits();
        return $this->buildSelectComponents($stepItem, $SUList, false);
    }

    public function Status(array $stepItem)
    {
        $statuses = $this->helpdesk->getStatuses();
        return $this->buildSelectComponents($stepItem, $statuses);
    }

    public function Modifier(array $stepItem)
    {
        return $this->buildInputComponent($stepItem);
    }

    public function Step(array $stepItem)
    {
        return $this->buildInputComponent($stepItem);
    }

    public function Author(array $stepItem)
    {
        return $this->buildInputComponent($stepItem);
    }

    public function GroupId(array $stepItem)
    {
        return $this->buildInputComponent($stepItem);
    }

    public function Community(array $stepItem)
    {
        return $this->buildInputComponent($stepItem);
    }

    public function SubCommunity(array $stepItem)
    {
        return $this->buildInputComponent($stepItem);
    }

    /**
     * init EndDate form item, with a 3 days hardcoded offset
     * @param <array> $stepItem, a Step unit
     * @return <FormItem>
     */
    public function EndDate(array $stepItem)
    {

        $widget = new OpWidgetFormEndDate($stepItem);
        $dateDay = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $validator = new \sfValidatorDate(
            array('min' => $dateDay),
            array('min' => "Expiration date must be greater than today", 'invalid' => 'Invalid date'));

        return new FormItem($stepItem, $widget, $validator);
    }


    public function Due(array $stepItem)
    {

        $widget = new OpWidgetFormEndDate($stepItem);
        $tomorow = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
        $validator = new \sfValidatorDate(
            array('min' => $tomorow),
            array('min' => "Expiration date must be greater than today", 'invalid' => 'Invalid date'));

        return new FormItem($stepItem, $widget, $validator);
    }

    /**
     * init Subject form item
     * @param <array> $stepItem, a Step unit
     * @return <FormItem>
     */
    public function Subject(array $stepItem)
    {

        $widget = new \sfWidgetFormInputText(array(), array('class' => 'span11'));
        $validator = new \sfValidatorString(array('min_length' => 5, 'max_length' => 255));
        return new FormItem($stepItem, $widget, $validator);
    }

    public function AttachmentData(array $stepItem)
    {
        $widget = new \sfWidgetFormTextarea(array(), array('style' => 'font-size:13px; font-family:Courier New', 'class' => 'span11', 'rows' => '20'));
        $validator = new \sfValidatorPass();
        return new FormItem($stepItem, $widget, $validator);
    }


    public function AttachmentName(array $stepItem)
    {
        $widget = new \sfWidgetFormInputText(array(), array('class' => 'span5'));
        $validator = new \sfValidatorPass(array());
        return new FormItem($stepItem, $widget, $validator);
    }


    /**
     * init Comment form item
     * @param <array> $stepItem, anStep unit
     * @return <FormItem>
     */
    public function Comment(array $stepItem)
    {
        $widget = new \sfWidgetFormTextarea(array(), array('style' => 'font-size:13px; font-family:Courier New', 'class' => 'span11', 'rows' => '20'));
        $validator = new \sfValidatorString(array('min_length' => 5, 'max_length' => 4000));
        return new FormItem($stepItem, $widget, $validator);
    }

    /**
     * init Internal Diary form item
     * @param <array> $stepItem, anStep unit
     * @return <FormItem>
     */
    public function Diary(array $stepItem)
    {
        $widget = new \sfWidgetFormTextarea(array(), array('style' => 'font-size:13px; font-family:Courier New', 'class' => 'span11', 'rows' => '20'));
        return new FormItem($stepItem, $widget, new \sfValidatorPass);
    }

    public function Description(array $stepItem)
    {
        return $this->Comment($stepItem);
    }

    public function Text(array $stepItem)
    {
        return $this->Comment($stepItem);
    }

    public function Solution(array $stepItem)
    {
        return $this->Comment($stepItem);
    }

    /**
     * init CarbonCopy check boxes form item
     * @param <array> $stepItem, a Step unit
     * @return <FormItem>
     */
    public function CarbonCopy(array $stepItem)
    {
        $stepId = $stepItem['id'];

        if (!isset($stepItem['json_param'])) {
            throw new \Exception(
                "Unable to build '$stepId' widget, 'json_param' field must be set with an array in step configuration",
                0,
                null);
        }

        $choices = json_decode($stepItem['json_param'], true);
        $widget = new OpWidgetFormCarbonCopy(((is_array($choices) ? $choices : array())));
//        $validator = new \sfValidatorChoice(array(
//            'choices' => array_keys($choices),l
//            'multiple' => true));
        $validator = new \sfValidatorPass(array());
        $formItem = new FormItem($stepItem, $widget, $validator);
        return $formItem;
    }

    /**
     * init CarbonCopySup check boxes form item
     * @param <array> $stepItem, a Step unit
     * @return <FormItem>
     */
    public function PersonalCarbonCopy(array $stepItem)
    {
        if(isset($stepItem['value']) && $stepItem['value'] === "+T.CarbonCopy+")
            $stepItem['value'] = "";
        if (isset($stepItem['json_param'])) {
            $choices = json_decode($stepItem['json_param'], true);
            $values = explode(';', $stepItem['value']);
            foreach ($choices as $choice => $value) {
                if (($key = array_search($choice, $values)) !== false) {
                    unset($values[$key]);
                }
            }
            $stepItem['value'] = implode(';', $values);
        }
        $widget = new \sfWidgetFormInputText(array(), array('class' => 'span5', 'placeholder' => 'Add mail addresses for notifying people on this ticket. Please use semi-colon as delimiter.'));
        $validator = new \sfValidatorEmailList(array());
        return new FormItem($stepItem, $widget, $validator);
    }

    /**
     * init Site form item
     * @param <array> $stepItem, a Step unit
     * @return <FormItem>
     */
    public function Site(array $stepItem)
    {

        $widget = new \sfWidgetFormInputText(array(), array('class' => 'span5'));
        $validator = new \sfValidatorPass(array());
        return new FormItem($stepItem, $widget, $validator);
    }

    /**
     * init Ngi form item
     * @param <array> $stepItem, a Step unit
     * @return <FormItem>
     */
    public function Ngi(array $stepItem)
    {

        $widget = new \sfWidgetFormInputText(array(), array('class' => 'span5'));
        $validator = new \sfValidatorPass(array());
        return new FormItem($stepItem, $widget, $validator);
    }

    /**
     * init Author email form item
     * @param <array> $stepItem, a Step unit
     * @return <FormItem>
     */
    public function AuthorEmail(array $stepItem)
    {

        $widget = new \sfWidgetFormInputText(array(), array('class' => 'span5'));
        $validator = new \sfValidatorPass(array());
        return new FormItem($stepItem, $widget, $validator);
    }

    public function Queue(array $stepItem){
        $widget = new \sfWidgetFormInputText(array(), array('class' => 'span5'));
        $validator = new \sfValidatorPass(array());
        return new FormItem($stepItem, $widget, $validator);
    }

}