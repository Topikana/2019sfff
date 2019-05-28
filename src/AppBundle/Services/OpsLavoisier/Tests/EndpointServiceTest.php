<?php

require_once dirname(__FILE__) . '/../../Lavoisier/IEntries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Entries.php';
require_once dirname(__FILE__) . '/../BaseService.php';
require_once dirname(__FILE__) . '/../Services/EndpointService.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Query.php';
require_once dirname(__FILE__) . '/../Exceptions/OpsLavoisierServiceException.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/DefaultHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/EntriesHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/StringHydrator.php';
require_once dirname(__FILE__) . '/../Entries/EndpointEntry.php';
require_once dirname(__FILE__) . '/../Entries/EndpointEntries.php';

use \Lavoisier\Hydrators\IHydrator;
use \Lavoisier\IEntries;
use \Lavoisier\Entries\Entries;
use \Lavoisier\Query;
use \Lavoisier\Hydrators\DefaultHydrator;
use \Lavoisier\Hydrators\EntriesHydrator;
use \OpsLavoisier\Entries\EndpointEntries;
use \OpsLavoisier\Entries\EndpointEntry;
use \OpsLavoisier\Services\EndpointService;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;
use \OpsLavoisier\BaseService;


class EndpointServiceTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {

        $this->stub = $this->getMock('Lavoisier\Query', array('curl'), array('localhost'));
        $this->endpointService = new Endpointservice('localhost');
        $this->endpointService->mockQueryInstance = $this->stub;
        $this->stub->expects($this->any())
            ->method('curl')
            ->will($this->returnValue(array('content'=>
        file_get_contents(dirname(__FILE__) . "/resources/endpoints.xml"))));

    }

    public function testFindByTuple()
    {

        $output_expected = array(
            "HOSTNAME" => 'amga.ct.infn.it',
            "GOCDB_PORTAL_URL" => 'https://goc.egi.eu/portal/index.php?Page_Type=Service&id=2916',
            "SERVICE_TYPE" => 'AMGA',
            "IN_PRODUCTION" => 'Y',
            "NODE_MONITORED" => 'Y',
            "SITENAME" => "GRISU-COMETA-INFN-CT",
            "ROC_NAME" => "NGI_IT",
            'DOWNTIME_PRIMARY_KEY' => "90687G0",
            'DOWNTIME_GOCDB_PORTAL_URL' => "https://goc.egi.eu/portal/index.php?Page_Type=Downtime&id=12185",
            'DOWNTIME_SEVERITY' => "OUTAGE",
            'DOWNTIME_DESCRIPTION' => "hw repair",
            'DOWNTIME_END_DATE' => "1384441200",
            'DOWNTIME_STATUS_LABEL' => 'OUTAGE',
            'DOWNTIME_STATUS_CLASS' => 'danger',
            'STATUS_LABEL' => 'monitored and in production',
            'STATUS_NICK' => 'prod & monit',
            'STATUS_CLASS' => 'success'
        );

        $tuples = array(array(
            'hostname' => 'cream1.farm.particle.cz',
            'service_type' => 'APEL'));

        $res = $this->endpointService->findByTuple($tuples, 'praguelcg2')->pop()->getArrayCopy();
        $this->assertEquals($output_expected, $res);
    }


   public function testFindByIndex()
    {

        $output_expected = array(
            'HOSTNAME' => 'carceri.hec.lancs.ac.uk',
            'GOCDB_PORTAL_URL' => 'https://goc.egi.eu/portal/index.php?Page_Type=Service&id=753',
            'SERVICE_TYPE' => 'APEL',
            'IN_PRODUCTION' => 'Y',
            'NODE_MONITORED' => 'Y',
            'SITENAME' => 'UKI-NORTHGRID-LANCS-HEP',
            'ROC_NAME' => 'NGI_UK',
            'DOWNTIME_STATUS_LABEL' => 'OUTAGE',
            'DOWNTIME_STATUS_CLASS' => 'danger',
            'STATUS_LABEL' => 'monitored and in production',
            'STATUS_CLASS' => 'success',
            'DOWNTIME_PRIMARY_KEY' => "90687G0",
            'DOWNTIME_GOCDB_PORTAL_URL' => "https://goc.egi.eu/portal/index.php?Page_Type=Downtime&id=12185",
            'DOWNTIME_SEVERITY' => "OUTAGE",
            'DOWNTIME_DESCRIPTION' => "hw repair",
            'DOWNTIME_END_DATE' => "1384441200",
            'STATUS_NICK' => 'prod & monit'
        );
        $res = $this->endpointService->findByIndex('site', array('UKI-NORTHGRID-LANCS-HEP'))->offsetGet('carceri.hec.lancs.ac.ukAPEL')->getArrayCopy();
        $this->assertEquals($output_expected, $res);
    }



}