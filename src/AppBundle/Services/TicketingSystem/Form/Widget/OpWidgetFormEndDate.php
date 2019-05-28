<?php

namespace AppBundle\Services\TicketingSystem\Form\Widget;


/**
 * provide specific EndDate widget (+ 3 days)
 * @author Olivier LEQUEUX
 */
class OpWidgetFormEndDate extends \sfWidgetFormJQueryDate
{

    public function __construct(array $stepItem)
    {
        $options = array('culture' => 'en','image'=>'/img/calendar.png');

        parent::__construct($options, array());

        if (isset($stepItem['json_param'])) {
            $default = json_decode($stepItem['json_param'], true);
        } else {

            if (isset($stepItem['value']) && !empty($stepItem['value'])) {

                $date_tab = explode('-', $stepItem['value']);
                $default = array(
                    'year' => $date_tab[0],
                    'month' => $date_tab[1],
                    'day' => $date_tab[2]);
            } else {

                $t = $this->in3Days();
                $default = array(
                    'year' => date("Y", $t),
                    'month' => date("n", $t),
                    'day' => date("j", $t));
            }
        }
        $this->setOption('default', $default);
    }

    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        $attributes['class'] = 'span1';
        $rendering = parent::render($name, $value, $attributes, $errors);

        return $rendering;

    }


    /**
     * Build the date when a ticket should be finished
     *  this code is comming from ticket_creation_form.php CIC Portal V1
     *
     * @param <timestamp> $startdate start date, if null $date is initialized to today
     * @return <timestamp> date  + 3 days without week-ends
     * @author  an old CIC Portal developper and Olivier Lequeux
     */
    private function in3Days($startdate = NULL)
    {

        if (!$startdate) $startdate = mktime(0, 0, 0, date("m"), date("d") + 3, date("Y")); //today

        // if deadline is satuday or sunday, extend to next monday
        if (date("l", $startdate) == "Saturday") $startdate = mktime(0, 0, 0, date("m"), date("d") + 5, date("Y"));
        if (date("l", $startdate) == "Sunday") $startdate = mktime(0, 0, 0, date("m"), date("d") + 4, date("Y"));

        // if current day is Thursday or Friday, extend resp. until tuesday and Wednesday
        if (date("l") == "Thursday") $startdate = mktime(0, 0, 0, date("m"), date("d") + 5, date("Y"));
        if (date("l") == "Friday") $startdate = mktime(0, 0, 0, date("m"), date("d") + 6, date("Y"));

        return $startdate;

    }

}