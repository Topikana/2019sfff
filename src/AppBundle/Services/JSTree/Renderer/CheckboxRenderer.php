<?php

namespace AppBundle\Services\JSTree\Renderer;

use AppBundle\Services\JSTree\Renderer\TargetRenderer;

class CheckboxRenderer extends TargetRenderer
{

    /**
     *  Update $result  with all requested Ids nedeed to check boxes
     * @param <array> $Ids, array from jstree arranged
     */
    static private function updateCheckBoxList($Ids, &$result)
    {

        foreach ($Ids as $item) {
            if (is_array($item)) {
                self::updateCheckBoxList($item, $result);
            } else {
                $result[] = $item;
            }
        }
    }

    /**
     * transform data from db to Json usable format
     * @author <Olivier Lequeux>
     * @param <array> $tree, data stored
     */
    public function render()
    {
        $prunedTree = self::convert($this->tree, self::CHECKBOX_PRUNING);
        $res = array();
        $tmp_res = array();
        // preserve first (key = tree Id)
        $count = 0;
        foreach ($prunedTree as $key => $branch) {
            self::updateCheckBoxList($branch, $tmp_res);

            $res[$count]['tree'] = $key;
            $res[$count]['values'] = $tmp_res;
            $count++;
            unset($tmp_res);
        }
        return $res;
    }


}