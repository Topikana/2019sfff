<?php

namespace AppBundle\Services\OpsLavoisier\Entries;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 29/10/13
 */

use \Lavoisier\Entries\Entries;

class EndpointEntries extends Entries
{
    // [nagios notification service flavour] => array(matching GOCDB service endpoints)
    static public $FLAVOUR_SERVICE_MAP = array(
        'MPICH2,OPENMPI,MPICH' => array('APEL'),
        'SRMv2' => array('SRM', 'Classic-SE'),
        'CE' => array('CREAM-CE', 'ARC-CE'),
        'MPI' => array('CREAM-CE', 'CE', 'ARC-CE'),
        'BDII' => array('Top-BDII', 'Site-BDII'),
        'LFC' => array('Local-LFC', 'Central-LFC'));

    // search HostnameFlavour matching entry key
    public function matchHostnameFlavour($hostname, $flavour)
    {

        if (isset($this[$hostname . $flavour])) {
            return $this[$hostname . $flavour];
        } else {

            if (isset(self::$FLAVOUR_SERVICE_MAP[$flavour])) {
                $se = self::$FLAVOUR_SERVICE_MAP[$flavour];
                foreach ($se as $value) {
                    if (isset($this[$hostname . $value])) {
                        return $this[$hostname . $value];
                    }
                }
            }
        }
        return null;
    }

    public function init() {}

}