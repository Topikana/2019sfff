<?php

namespace AppBundle\Services\TicketingSystem\Form\Widget;

//require_once dirname(__FILE__) . '/../../../../symfony/lib/widget/sfWidgetFormChoice.class.php';


/**
 * provide specific CarbonCopy widget
 * @author Olivier LEQUEUX
 */
class OpWidgetFormCarbonCopy extends \sfWidgetFormChoice
{

    private function filter_choices($var)
    {
        $var = trim($var);
        return (($var != null) && (!empty($var)));
    }

    public function __construct(array $choices)
    {

        $this->addOption('hasMultipleDefaultValue', true);

        // unset invalid choices
        array_walk($choices, array($this, 'filter_choices'));
        $options = array(
            'expanded' => true,
            'multiple' => true,
            'choices' => $choices,
            'renderer_options' => array('formatter' => array($this, 'bootstrapFormat')));

        parent::__construct($options, array());
    }

    public function getMultipleDefaultValue()
    {
        return explode(';', $this->getDefault());
    }


    public function bootstrapFormat($widget, $inputs)
    {

        $bootStrap = "";
        foreach ($inputs as $id => $input) {

            $inputAtt = $this->parseInput($input['input']);
            $label = $this->parseLabel($input['label']);

            $bootStrap .=
                "<label class='checkbox-inline'>" .
                $input['input'] . $label
                . "</label>";
        }

        return $bootStrap;

    }

    private function parseInput($str)
    {
        $matches = $res = array();
        $keywords = preg_split("/[\s]+/", $str);
        foreach ($keywords as $attr) {
            preg_match('#(.*)=#', $attr, $matches);
            if (isset($matches[1])) {
                $res[$matches[1]] = $attr;
            }
        }

        return $res;

    }

    private function parseLabel($str)
    {
        $matches = array();
        preg_match('#>(.*)<#', $str, $matches);
        return $matches[1];

    }


}