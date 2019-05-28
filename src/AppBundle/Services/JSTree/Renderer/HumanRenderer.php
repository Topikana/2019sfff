<?php


namespace AppBundle\Services\JSTree\Renderer;

use AppBundle\Services\JSTree\RecipientCollection;
use AppBundle\Services\JSTree\Renderer\TargetRenderer;

class HumanRenderer extends TargetRenderer {

    private $recipients;

    public function __construct($tree, RecipientCollection $recipients) {

        $this->recipients = $recipients;

        parent::__construct($tree);
    }

    public function render() {

        $prunedTrees = array('item' => self::convert($this->tree, self::DISPLAY_PRUNING));
        $this->updateLabelList($prunedTrees);
        $prunedTrees["label"] = $this->recipients->getName();

        return $prunedTrees;
    }



    private function updateLabelList(&$tree) {

        foreach($tree as $key => $item ) {

            if(is_array($item)) {
                $this->updateLabelList($tree[$key]);
            }
            else {
                  if($item != self::DEFAULT_DISPLAY_VALUE) {
                    $tree[$key]=$this->recipients->matchItem(
                        $item,
                        RecipientCollection::DATA_TYPE_LABEL,
                        true);
                }
            }
        }

    }



}
