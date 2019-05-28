<?php

namespace AppBundle\Services\OpsLavoisier;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 23/10/13
 */

use \Lavoisier\Query;

abstract class BaseService
{
    protected $hostname;
    protected $lastQuery;
    public $mockQueryInstance; // for unit test only


    public function __construct($hostname)
    {
        if($hostname === null) {
            throw new \Exception('Unable to instanciate service, hostname parameter is null');
        }

        $this->hostname = $hostname;
        $this->mockQueryInstance = null;
    }

    // for unit test only
    public function createQueryInstance($hostname, $view = '', $operation = 'lavoisier' ) {
        if($this->mockQueryInstance === null) {
            return new Query($hostname, $view, $operation);
        }
        else return $this->mockQueryInstance;
    }

    // for unit test only
    public function setMockQueryInstance($mock) {
       $this->mockQueryInstance = $mock;
    }

    public function convertWhereClauses($whereClauses,$map)
    {
        // convert keys
        $convertedWhereClauses = array();
        foreach ($whereClauses as $key => $item) {
            if (isset($map[$key])) {
                $convertedWhereClauses[$map[$key]] = $item;
            }
        }
        return $convertedWhereClauses;

    }

}