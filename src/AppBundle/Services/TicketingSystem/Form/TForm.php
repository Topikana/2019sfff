<?php

namespace AppBundle\Services\TicketingSystem\Form;

use AppBundle\Services\TicketingSystem\Workflow\Step;

class TForm extends \opBaseForm
{

    private $formItemFactory;
    private $step;

    public function __construct(IFormItemFactory $factory, Step $step)
    {

        $this->formItemFactory = $factory;
        $this->step = $step;
        parent::__construct();
    }


    /**
     * render global error message to display a the begining of a form
     * @return <string> formatted error message
     */
    public function renderGlobalErrorMessage()
    {
        $message = "";
        $nberror = count($this->getErrorSchema());
        if ($nberror > 0) {
            $tmp = $this->getErrorSchema()->getErrors();
            $message = "<div class='alert alert-danger'>Please check your form, $nberror field(s) seem(s) not correctly filled </br>";
            foreach ($tmp as $key => $e) {
                if ($key == 'Description'){
                    $message .= "- Problem description field is too long (4000 characters max): Please use verbose mode and reduce this field.";
                }

            }
            $message .= "</div>";
        }
        return $message;
    }


    public function configure()
    {

        foreach ($this->step as $item) {

            $formItem = $this->formItemFactory->createFormItem($item);
            $widget = $formItem->getWidget();
            $this->setWidget($formItem->getId(), $widget);
            $this->setValidator($formItem->getId(), $formItem->getValidator());

            // multiple default values must be set by the form after widget creation
            if ($widget->getOption('hasMultipleDefaultValue') === true) {
                $this->setDefault($formItem->getId(), $widget->getMultipleDefaultValue());
            }
        }

        $this->widgetSchema->setNameFormat('TMetaForm[%s]');
        $this->errorSchema = new \sfValidatorErrorSchema($this->validatorSchema);
        $this->setOpFormFormats();

    }

    public function isItemVisible($id)
    {
        return $this->step->isItemVisible($id);
    }

    public function isItemQuick($id)
    {
        return $this->step->isItemQuick($id);
    }

    // ol : awful but no way to define how to output fields in sf 1.4
    public function getTSValues()
    {
        $taintedValues = $this->getTaintedValues();
        if (isset($taintedValues['EndDate'])) {
            $taintedValues['EndDate'] = sprintf(
                '%s-%s-%s',
                $taintedValues['EndDate']['year'],
                $taintedValues['EndDate']['month'],
                $taintedValues['EndDate']['day']
            );
        }

        if (isset($taintedValues['CarbonCopy'])) {
            $taintedValues['CarbonCopy'] = implode(';', $taintedValues['CarbonCopy']);
        } else {
            // widget set but with empty values => replace old values if existing ones
            $wg = null;
            try { // getWidget will throw an Exception if widget "CarbonCopy" does not exist.
                $wg = $this->getWidget('CarbonCopy');
                $taintedValues['CarbonCopy'] = '';
            } catch (\InvalidArgumentException $iae) {
                // do nothing
            }

        }

        return $taintedValues;
    }

}