<?php

require_once dirname(__FILE__) . '/../../Lavoisier/IEntries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Entries.php';
require_once dirname(__FILE__) . '/../BaseService.php';
require_once dirname(__FILE__) . '/../Services/GGUSService.php';
require_once dirname(__FILE__) . '/../Services/EntityService.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Query.php';
require_once dirname(__FILE__) . '/../Exceptions/OpsLavoisierServiceException.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/DefaultHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/EntriesHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/StringHydrator.php';

use \Lavoisier\Hydrators\IHydrator;
use \Lavoisier\IEntries;
use \Lavoisier\Entries\Entries;
use \Lavoisier\Query;
use \Lavoisier\Hydrators\DefaultHydrator;
use \Lavoisier\Hydrators\EntriesHydrator;
use \OpsLavoisier\Services\EntityService;
use \OpsLavoisier\Services\GGUSService;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;
use \OpsLavoisier\BaseService;


class GGUSServiceTest extends \PHPUnit_Framework_TestCase
{

    public static $ggusService;

    public static function setUpBeforeClass()
    {
        self::$ggusService = new GGUSService('localhost');

    }

    public function testFindTickets()
    {

        $this->stub = $this->getMock('Lavoisier\Query', array('curl'), array('localhost'));
        self::$ggusService->setMockQueryInstance($this->stub);
        $this->stub->expects($this->any())
            ->method('curl')
            ->will($this->returnValue(array('content' =>
        file_get_contents(dirname(__FILE__) . "/resources/tickets.xml"))));


        $collection = self::$ggusService->findTickets('GGUS_ROD', array());
        $this->assertEquals(2, count($collection));

//        $oneTicket = $collection->pop();
        print_r($collection);

    }

    public static function tearDownAfterClass()
    {
        self::$ggusService = NULL;
    }


}