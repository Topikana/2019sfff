<?php
namespace AppBundle\Services\OpsLavoisier\Hydrators;

use \Lavoisier\Hydrators\IHydrator;

/**
 * @author Olivier LEQUEUX
 */
class NGISitesHydrator implements IHydrator {

    function hydrate($str){
        $NGIs = simplexml_load_string($str);

        $result = new \ArrayObject();
        foreach ($NGIs as $ngi) {
            $n_attr = $ngi->attributes();
            foreach ($ngi->Sites->Site as $site) {
                $s_attr = $site->attributes();
                $site_name = strval($s_attr['name']);
                $result[strval($n_attr['name'])][$site_name] = $site_name;
            }
        }
        return $result;
    }
}
