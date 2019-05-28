<?php

namespace AppBundle\Services\TicketingSystem\Workflow;


/**
 * Description of ArrayTicket
 *
 * @author Olivier LEQUEUX
 */
class Step extends \ArrayObject
{

    public function __construct($input)
    {
        parent::__construct($input, \ArrayObject::ARRAY_AS_PROPS);
    }

    public function sort()
    {
        $this->uksort(array($this, 'cmp'));
    }

    public function cmp($a, $b)
    {


        if (!isset($this[$a]['order'])) {
            $aOrder = 999;
        } else {
            $aOrder = (int)$this[$a]['order'];
        }

        if (!isset($this[$b]['order'])) {
            $bOrder = 999;
        } else {
            $bOrder = (int)$this[$b]['order'];
        }


        if ($aOrder > $bOrder) {
            return 1;
        } else {
            return -1;
        }
    }

    public function isItemVisible($id)
    {
        return $this->getItemValue($id, 'visibility');
    }

    public function isItemQuick($id)
    {
        return $this->getItemValue($id, 'quick');
    }

    public function getItemValue($id, $valueName)
    {
        foreach ($this as $item) {
            if ($item['id'] === $id) {
                if (isset($item[$valueName])) {
                    return (bool)$item[$valueName];

                }
            }
        }

        return null;
    }

    public function hideItems($itemsToHide)
    {

        if (is_array($itemsToHide)) {
            $all = (count($itemsToHide) === 0);
            foreach ($this as $index => $item) {

                if (($all === true) || (in_array($item['id'], $itemsToHide))) {
                    $this[$index]['visibility'] = false;
                }
            }
        }
    }

}