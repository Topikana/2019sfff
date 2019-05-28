<?php
namespace AppBundle\Services\OpsLavoisier\Hydrators;

use \Lavoisier\Hydrators\IHydrator;

/**
 * @author Olivier LEQUEUX
 */
class SiteInfoHydrator implements IHydrator {

    function hydrate($str){
        $sites = simplexml_load_string($str);

        $result = new \ArrayObject();
        foreach ($sites as $site) {
            $result[] = $this->getAttributesAsArray($site);
        }
        return $result;
    }

    function getAttributesAsArray(\SimpleXMLElement $site) {

        $attr = array();
        $n_attr = $site->attributes();
        foreach ($n_attr as $key => $value) {
           $attr[strval($key)] = strval($value);
        }

        return $attr;
    }
}
