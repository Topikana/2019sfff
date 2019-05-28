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



class DowntimeService extends BaseService
{
    /**
     * @param <string> $entityType 'ngi' | 'site'
     * @return /Lavoisier/Entries
     */

    private $map;

    public function __construct($hostname)
    {
        $this->map = array(
        'ngi' => 'NGI',
        'site' => 'HOSTED_BY'
        );
        parent::__construct($hostname);
    }

    public function count($entityType,$whereClauses)
    {
        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_downtime_count');
        try {
            $convertedWhereClauses = $this->convertWhereClauses($whereClauses,$this->map);
            $prePath = sprintf(
                "/e:entries/e:entry[e:entry[%s]]",
                Query::buildEntriesPredicate($convertedWhereClauses));

            $lquery->setMethod('POST');
            $array_POST=array('entity_type' => $entityType);
            if ($entityType=='site' && isset($whereClauses['ngi']))
             $array_POST=array(
                 'entity_parent' => $whereClauses['ngi'],
                 'entity_type' => $entityType,
                 'prepath'=>$prePath,
             );
            else
                $array_POST=array(
                    'entity_parent' => 'none',
                    'entity_type' => $entityType,
                    'prepath'=>$prePath,
                );

            $lquery->setPostFields($array_POST);


            $lquery->setHydrator(new EntriesHydrator());
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }

        return $result;



    }

    public function findOnGoingDowntimes( $whereClauses)
    {
        $convertedWhereClauses = $this->convertWhereClauses($whereClauses,$this->map);
        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_downtime_ongoing');

        try {
            $path = sprintf(
                "/e:entries/e:entry[e:entry[%s]]",
                Query::buildEntriesPredicate($convertedWhereClauses)
            );

            $lquery->setPath($path);
            $hydrator = new EntriesHydrator();
            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;

    }



}