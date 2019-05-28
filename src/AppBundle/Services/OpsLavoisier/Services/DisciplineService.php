<?php

namespace AppBundle\Services\OpsLavoisier\Services;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 13/12/13
 */

use \Lavoisier\Query;
use \Lavoisier\Hydrators\StringHydrator;
use \Lavoisier\Hydrators\EntriesHydrator;
use \OpsLavoisier\BaseService;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;



class DisciplineService extends BaseService
{
    /**
     * @param <string> $entityType 'ngi' | 'site'
     * @return /Lavoisier/Entries
     */

    private $map;

    public function __construct($hostname)
    {

        parent::__construct($hostname);
    }

    public function listEntries($Void)
    {

        $lquery = $this->createQueryInstance($this->hostname, 'VoDisciplinesById');

        try {

            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;


    }

    public function findLabel($discplineId)
    {

        $lquery = $this->createQueryInstance($this->hostname, 'VoDisciplinesTree_Raw');

        try {

            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;

    }



}