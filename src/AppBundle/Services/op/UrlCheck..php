<?php

namespace AppBundle\Services\op;


/**
 * provides function to get data from Url tag
 *
 * @author Olivier LEQUEUX
 */
class UrlCheck {


    static public function isValidHTTPCode($httpCode) {
         return ((($httpCode > 0) && ($httpCode < 400)) ? true : false);
    }

//    public function getBooleanStatus() {
//        return self::isValidHTTPCode($this->getcode());
//    }
//
//    public function getFullDescription() {
//        return sprintf('%s : %s', $this->getcode(), $this->getdescription());
//    }


}
?>
