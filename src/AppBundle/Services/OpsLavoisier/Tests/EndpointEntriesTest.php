<?php

require_once dirname(__FILE__) . '/../../Lavoisier/IEntries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Entries.php';
require_once dirname(__FILE__) . '/../BaseService.php';
require_once dirname(__FILE__) . '/../Entries/EndpointEntry.php';
require_once dirname(__FILE__) . '/../Exceptions/OpsLavoisierServiceException.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/EntriesHydrator.php';

use \Lavoisier\Hydrators\IHydrator;
use \Lavoisier\IEntries;
use \Lavoisier\Entries\Entries;
use \OpsLavoisier\Entries\EndpointEntry;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;


class EndpointEntriesTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->input = array(
            'HOSTNAME' => 'carceri.hec.lancs.ac.uk',
            'GOCDB_PORTAL_URL' => 'https://goc.egi.eu/portal/index.php?Page_Type=Service&id=753',
            'SERVICE_TYPE' => 'APEL',
            'IN_PRODUCTION' => 'Y',
            'NODE_MONITORED' => 'N',
            'SITENAME' => 'UKI-NORTHGRID-LANCS-HEP',
            'ROC_NAME' => 'NGI_UK',
            'DOWNTIME_PRIMARY_KEY' => "90353G0",
            'DOWNTIME_GOCDB_PORTAL_URL' => "https://goc.egi.eu/portal/index.php?Page_Type=Downtime&id=12091",
            'DOWNTIME_SEVERITY' => "OUTAGE",
            'DOWNTIME_DESCRIPTION' => "machines down",
            'DOWNTIME_END_DATE' => "1383836400"
        );
        $this->endpointEntry = new EndpointEntry($this->input);
        $this->endpointEntry->init();
    }

    public function testInit()
    {
        $this->assertEquals($this->endpointEntry['DOWNTIME_STATUS_CLASS'], 'danger');
        $this->assertEquals($this->endpointEntry['DOWNTIME_STATUS_LABEL'], 'OUTAGE');
        $this->assertEquals($this->endpointEntry['STATUS_LABEL'], 'in production but not monitored');
        $this->assertEquals($this->endpointEntry['STATUS_CLASS'], 'warning');
    }

    public function testFunctions()
    {
        $this->assertTrue($this->endpointEntry->isInProduction());
        $this->assertTrue($this->endpointEntry->hasDowntime());
        $this->assertFalse($this->endpointEntry->isNodeMonitored());
    }
}