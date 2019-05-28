<?php

namespace AppBundle\Services\OpsLavoisier\Services;

/**
 * User: CL
 * Date: 17/11/13
 */

use Lavoisier\Hydrators\IHydrator;
use \Lavoisier\Query;
use \Lavoisier\Hydrators\EntriesHydrator;
use \OpsLavoisier\BaseService;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;

class RTService extends BaseService
{
    private $entityService;
    private $map;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
        $this->entityService = new EntityService($this->hostname);
        $this->map = array(
            'ngi' => 'ngi',
            'site' => 'site',
            'id' => 'id'
        );
    }

    public function getSupportUnits()
    {
        $result = $this->entityService->getSupportUnits();
        // replace empty values by key
        foreach ($result as $key => $value) {
            $trim_values = trim($value);
            if (empty($trim_values)) {
                $result[$key] = $key;
            }
        }
        return $result;
    }

    public function notify($rt_service_id)
    {

        $lquery = $this->createQueryInstance($this->hostname, $rt_service_id, 'notify');
        try {
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;

    }


    public function findTicket($view, $whereClauses, IHydrator $hydrator = null)
    {



        $convertedWhereClauses = $this->convertWhereClauses($whereClauses, $this->map);
        $lquery = $this->createQueryInstance($this->hostname,$view );



        try {


            $lquery->setMethod('POST');
            $lquery->setPostFields(
                $convertedWhereClauses
            );

            if($hydrator == null) {
                $hydrator = new EntriesHydrator();
            }

            $lquery->setHydrator($hydrator);
            $result = $lquery->execute();



        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;
    }




    public function findTickets($view, $whereClauses, IHydrator $hydrator = null)
    {



        $convertedWhereClauses = $this->convertWhereClauses($whereClauses, $this->map);
        $lquery = $this->createQueryInstance($this->hostname,$view );



        try {
            $path = sprintf(
                "/e:entries/e:entries[e:entry[%s]]",
                Query::buildEntriesPredicate($convertedWhereClauses)
            );




            $lquery->setPath($path);



            if($hydrator == null) {
                $hydrator = new EntriesHydrator();
            }

            $lquery->setHydrator($hydrator);

            $result = $lquery->execute();



        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }
        return $result;
    }


    public function getCounters($entityType, $whereClauses, $view)
    {


        $lquery = $this->createQueryInstance($this->hostname, 'Csi_Metrics_Count');


        $convertedWhereClauses = $this->convertWhereClauses($whereClauses, $this->map);

            try {


                $prePath = sprintf(
                    "/e:entries/e:entries[e:entry[%s]]",
                    Query::buildEntriesPredicate($convertedWhereClauses)
                );
                $lquery->setMethod('POST');
                $lquery->setPostFields(array(
                    'entity_type' => $entityType,
                    'viewRT' => $view,
                    'pre_path' => $prePath
                ));


            $lquery->setHydrator(new EntriesHydrator());
            $result = $lquery->execute();


            foreach ($result as $key => $entry) {
                   $result[$key] = $entry['nbTickets'];
            }

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }

        return $result;
    }




}