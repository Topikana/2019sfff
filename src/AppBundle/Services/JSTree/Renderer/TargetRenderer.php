<?php
    /**
     * Created by JetBrains PhpStorm.
     * User: Olivier LEQUEUX
     * Date: 15/10/12
     */

namespace AppBundle\Services\JSTree\Renderer;

use AppBundle\Services\JSTree\RecipientCollection;

abstract class TargetRenderer {

    CONST DISPLAY_PRUNING = 0;
    CONST MAIL_PRUNING = 1;
    CONST CHECKBOX_PRUNING = 3;
    CONST DEFAULT_DISPLAY_VALUE = 'ALL';


    protected $tree;

    public function __construct($tree) {

        $this->tree = $tree;
    }


    abstract public function render();

    static private function prune($tree, $cuttingMode = self::DISPLAY_PRUNING)
    {
        $current_root_value = null;
        if (is_array($tree)) {

            if (self::hasAnyRootBehaviour($tree)) {
                // store current root value
                if (isset($tree[RecipientCollection::ROOT_AS_MAILING_LIST])) {
                    $current_root_value = $tree[RecipientCollection::ROOT_AS_MAILING_LIST];
                }
                if (isset($tree[RecipientCollection::ROOT_AS_PARENT_FLAG])) {
                    $current_root_value = $tree[RecipientCollection::ROOT_AS_PARENT_FLAG];
                }
                if (isset($tree[RecipientCollection::ROOT_AS_MAILING_LIST_CC])) {
                    $current_root_value = $tree[RecipientCollection::ROOT_AS_MAILING_LIST_CC];
                }

                if ($cuttingMode == self::DISPLAY_PRUNING) {
                    $tree = self::DEFAULT_DISPLAY_VALUE;
                }

                if ($cuttingMode == self::CHECKBOX_PRUNING) {
                    $tree = array($current_root_value);
                }

                if ($cuttingMode == self::MAIL_PRUNING) {

                    if (isset($tree[RecipientCollection::ROOT_AS_MAILING_LIST])) {
                        $tree = array($current_root_value);
                    }

                    if (isset($tree[RecipientCollection::ROOT_AS_PARENT_FLAG])) {
                        unset($tree[RecipientCollection::ROOT_AS_PARENT_FLAG]);
                    }

                    // in case of ROOT_AS_MAILING_LIST : keep original structure
                }
            }
        }

        return $tree;
    }


    static function hasAnyRootBehaviour($tree) {
        return(
            isset($tree[RecipientCollection::ROOT_AS_MAILING_LIST]) ||
                isset($tree[RecipientCollection::ROOT_AS_PARENT_FLAG]) ||
                isset($tree[RecipientCollection::ROOT_AS_MAILING_LIST_CC]));
    }

    static public function convert($tree, $cuttingMode)
    {

        $tree = self::prune($tree, $cuttingMode);
        if (is_array($tree)) {
            foreach ($tree as $key => $item) {
                if (is_array($item)) {
                    $tree[$key] = self::convert($item, $cuttingMode);
                }
            }
        }

        return $tree;
    }

}
