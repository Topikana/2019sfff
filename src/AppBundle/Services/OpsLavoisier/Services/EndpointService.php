<?php

namespace AppBundle\Services\OpsLavoisier\Services;

/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 23/10/13
 */

use \Lavoisier\Query;
use \Lavoisier\Hydrators\StringHydrator;
use \Lavoisier\Hydrators\EntriesHydrator;
use \OpsLavoisier\Entries\EndpointEntries;
use \OpsLavoisier\Entries\EndpointEntry;
use \OpsLavoisier\BaseService;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;


class EndpointService extends BaseService
{
    private $service_map;
    private $endPointHydrator;

    public function __construct($hostname)
    {
        // flavour / endpoint services : mapping
        $this->service_map = EndpointEntries::$FLAVOUR_SERVICE_MAP;
        $this->endPointHydrator = new EntriesHydrator();
        $this->endPointHydrator->setDefaultBinding("\OpsLavoisier\Entries\EndpointEntry");
        $this->endPointHydrator->setRootBinding("\OpsLavoisier\Entries\EndpointEntries");
        parent::__construct($hostname);
    }

    private function getServiceHostPredicate($service_type, $hostname)
    {
        $service_list = array($service_type);
        if (isset($this->service_map[$service_type])) {
            $service_list = array_merge($service_list, $this->service_map[$service_type]);
        }
        $service_predicate = array();
        $count = 0;
        foreach ($service_list as $service) {
            $tmp_predicate = sprintf("(@hostname='%s' and @service_type='%s')", $hostname, $service);
            if ($count > 0) $service_predicate = sprintf("$service_predicate or $tmp_predicate");
            else $service_predicate = $tmp_predicate;
            $count++;
        }

        return $service_predicate;
    }

    // get a list of  endpoints using one attribute
    public function findByIndex($index_type, array $entities)
    {


        $types = array('key', 'site', 'ngi');
        if (!in_array($index_type, $types)) {
            throw new \Exception('Unknown index type, please use one of the following : ' . implode('|', $types));
        }
        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_endpoints_post');
        try {
            $path = sprintf(
                "/results/*[%s]",
                Query::buildPredicate("@$index_type", $entities, 'or')
            );
            $lquery->setMethod('POST');
            $lquery->setPostFields(array('xpath' => $path));
            $lquery->setHydrator($this->endPointHydrator);
            $result = $lquery->execute();
        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }

        return $result;

    }

    // get a list of endpoints using service/host mapping
    public function findByTuple(array $host_service, $site = null)
    {
        $sh_predicates = array();
        foreach ($host_service as $values) {
            $sh_predicates[] = $this->getServiceHostPredicate($values['service_type'], $values['hostname']);
        }
        $sh_predicates = implode(' or ', $sh_predicates);
        $lquery = $this->createQueryInstance($this->hostname, 'OPSCORE_endpoints_post');

        try {

            $predicate = $sh_predicates;
            if ($site !== null) {
                $predicate = "@site='$site' and (" . $sh_predicates . ")";
            }
            $path = sprintf("/results/e:entries[%s]", $predicate);
            $lquery->setMethod('POST');
            $lquery->setPostFields(array('xpath' => $path));
            $lquery->setHydrator($this->endPointHydrator);
            $result = $lquery->execute();

        } catch (\Exception $e) {
            throw new OpsLavoisierServiceException($lquery, $e);
        }

        return $result;

    }

}