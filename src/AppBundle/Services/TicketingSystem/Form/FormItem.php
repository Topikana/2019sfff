<?php

/**
 * Provide FormItem class to be handled by Form object
 * @author Olivier LEQUEUX
 */

namespace AppBundle\Services\TicketingSystem\Form;


class FormItem
{

    private $widget;
    private $validator;
    private $stepItem;

    public function __construct(array $stepItem, $widget, $validator)
    {
        $this->widget = $widget;
        $this->validator = $validator;
        $this->stepItem = $stepItem;
        $this->multipleDefaultValue = null;

        // complete validator field
        $validator->setOption('required', $this->isRequired());
        // complete widget fields
        $this->widget->setOption('label', $this->getLabel());
        $this->widget->setAttribute('readonly', !$this->isVisible());
        $value = $this->getValue();
        if (!is_null($value) && !empty($value)) {
            $this->widget->setOption('default', $this->getValue());
        }

    }


    public function getId()
    {
        return $this->stepItem['id'];
    }

    public function isVisible()
    {
        return $this->stepItem['visibility'];
    }

    public function isRequired()
    {
        return $this->stepItem['required'];
    }

    public function getLabel()
    {
        (isset($this->stepItem['label']) ? $label = $this->stepItem['label'] : $label = $this->stepItem['id']);
        return $label;
    }

    public function getHelp()
    {
        (isset($this->stepItem['help']) ? $help = $this->stepItem['help'] : $help = null);
        return $help;
    }

    public function getWidget()
    {
        return $this->widget;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function getValue()
    {
        (isset($this->stepItem['value']) ? $value = $this->stepItem['value'] : $value = null);
        return $value;
    }

}

