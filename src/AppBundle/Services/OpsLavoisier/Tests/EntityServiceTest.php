<?php
require_once dirname(__FILE__) . '/../../Lavoisier/IHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/EntriesHydrator.php';
require_once dirname(__FILE__) . '/../BaseService.php';
require_once dirname(__FILE__) . '/../Services/EntityService.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IEntries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Entries.php';
require_once dirname(__FILE__) . '/../Hydrators/NGISitesHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/DefaultHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/StringHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Query.php';
require_once dirname(__FILE__) . '/../Exceptions/OpsLavoisierServiceException.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Exceptions/HTTPStatusException.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Exceptions/cURLException.php';

use \Lavoisier\Hydrators\IHydrator;
use \Lavoisier\IEntries;
use \Lavoisier\Entries\Entries;
use \Lavoisier\Hydrators\DefaultHydrator;
use \Lavoisier\Hydrators\EntriesHydrator;
use \OpsLavoisier\Services\EntityService;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;
use \Lavoisier\Exceptions\HTTPStatusException;
use \Lavoisier\Exceptions\cURLException;
use \OpsLavoisier\BaseService;



class EntityServiceTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {

        $this->stub = $this->getMock('Lavoisier\Query', array('curl'), array('localhost'));
        $this->entityService = new EntityService('localhost');
        $this->entityService->setMockQueryInstance($this->stub);
    }

    public function testGetSite()
    {

        $this->stub->expects($this->any())
            ->method('curl')
            ->will($this->returnValue(array('content' =>
        file_get_contents(dirname(__FILE__) . "/resources/site_names.xml"))));

        $output_expected = unserialize(file_get_contents(dirname(__FILE__) . "/resources/site_names.array"));
        $this->assertEquals($output_expected, $this->entityService->getSiteNames()->getArrayCopy());

    }

    public function testGetNGINames()
    {
        $this->stub->expects($this->any())
            ->method('curl')
            ->will($this->returnValue(array('content' =>
        file_get_contents(dirname(__FILE__) . "/resources/ngi_names.xml"))));
        $output_expected = unserialize(file_get_contents(dirname(__FILE__) . "/resources/ngis.array"));
        $this->assertEquals($output_expected, $this->entityService->getNGINames()->getArrayCopy());
    }

    public function testGetNGISites()
    {

        $this->stub->expects($this->any())
            ->method('curl')
            ->will($this->returnValue(array('content' =>
        file_get_contents(dirname(__FILE__) . "/resources/ngi_sites.xml"))));

        $output_expected = unserialize(file_get_contents(dirname(__FILE__) . "/resources/ngi_sites.array"));
        $output_actual = $this->entityService->getTreeNames()->getArrayCopy();
        $this->assertEquals($output_expected, $output_actual);

    }

    public function  testGetMap()
    {

        $query = $this->entityService->getMapQuery('ngi', 'name', 'rod_email', "@key='NGI_CH' or @key='NGI_FRANCE'");
        $this->assertEquals(
            "/e:entries/e:entry[@key='NGI_CH' or @key='NGI_FRANCE']", $query->getPath());

        $post_value_expected = array(
            'entity_type' => 'ngi',
            'entry_key' => 'name',
            'entry_value' => 'rod_email'
        );
        $this->assertEquals($post_value_expected, $query->getPostFields());

        $this->stub->expects($this->any())
            ->method('curl')
            ->will($this->returnValue(array('content' =>
        file_get_contents(dirname(__FILE__) . "/resources/ggus_entities_map.xml"))));
        $output_expected = array
        (
            "ROC_Asia/Pacific" => "AsiaPacific",
            "ROC_CERN" => "CERN",
            "EGI.eu" => "EGI.eu",
            "NGI_AEGIS" => "NGI_AEGIS",
            "NGI_ARMGRID" => "NGI_ARMGRID",
            "NGI_BG" => "NGI_BG",
            "NGI_CH" => "NGI_CH",
            "NGI_CZ" => "NGI_CZ",
            "NGI_DE" => "NGI_DE",
            "NGI_FI" => "NGI_FI",
            "NGI_FRANCE" => "NGI_FRANCE",
            "NGI_GE" => "NGI_GE",
            "NGI_GRNET" => "NGI_GRNET",
            "NGI_HR" => "NGI_HR",
            "NGI_HU" => "NGI_HU",
            "NGI_IBERGRID" => "NGI_IBERGRID",
            "NGI_IL" => "NGI_IL",
            "NGI_IT" => "NGI_IT",
            "NGI_MARGI" => "NGI_MARGI",
            "NGI_MD" => "NGI_MD",
            "NGI_ME" => "NGI_ME",
            "NGI_NDGF" => "NGI_NDGF",
            "NGI_NL" => "NGI_NL",
            "NGI_PL" => "NGI_PL",
            "NGI_RO" => "NGI_RO",
            "NGI_SI" => "NGI_SI",
            "NGI_SK" => "NGI_SK",
            "NGI_TR" => "NGI_TR",
            "NGI_UA" => "NGI_UA",
            "NGI_UK" => "NGI_UK",
            "NGI_ZA" => "NGI_ZA",
            "ROC_Canada" => "ROC_Canada",
            "ROC_LA" => "ROC_LA",
            "ROC_Russia" => "Russia");
        $output_actual = $this->entityService->getSupportUnits()->getArrayCopy();
        $this->assertEquals($output_expected, $output_actual);

    }
}
