<?php

require_once dirname(__FILE__) . '/../BaseService.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IHydrator.php';
require_once dirname(__FILE__) . '/../Hydrators/NGISitesHydrator.php';

use \Lavoisier\Query;
use \OpsLavoisier\Hydrators\NGISitesHydrator;


class NGISitesHydratorTest extends \PHPUnit_Framework_TestCase
{
    public function testHydrate()
    {
        $input = file_get_contents(dirname(__FILE__) . '/resources/ngi_sites.xml');
        $output_expected = new \ArrayObject(unserialize(file_get_contents(dirname(__FILE__) . "/resources/ngi_sites.array")));
        $lh = new NGISitesHydrator();
        $this->assertEquals($output_expected, $lh->hydrate($input));
    }
}