<?php

require_once dirname(__FILE__) . '/../RecipientCollection.php';
require_once dirname(__FILE__) . '/../Renderer/TargetRenderer.php';
require_once dirname(__FILE__) . '/../Renderer/CheckboxRenderer.php';

use AppBundle\Services\JSTree\RecipientCollection;
use AppBundle\Services\JSTree\Renderer\TargetRenderer;
use AppBundle\Services\JSTree\Renderer\CheckboxRenderer;


class CheckboxRendererTest extends \PHPUnit_Framework_TestCase
{


    public function testConstruct()
    {
        $treeFixtures = unserialize(file_get_contents(dirname(__FILE__) . '/Fixtures/targets_start_fixtures1.txt'));
        $cR = new CheckboxRenderer($treeFixtures);
        return $cR;
    }

    /**
     * @depends testConstruct
     */
    public function testRender(CheckboxRenderer $cR)
    {
        $resultFixture = unserialize(file_get_contents(dirname(__FILE__) . '/Fixtures/targets_end_fixtures1.txt'));
        $this->assertEquals($resultFixture, $cR->render());
    }
}
