<?php

require_once dirname(__FILE__) . '/../RecipientCollection.php';
require_once dirname(__FILE__) . '/../Renderer/TargetRenderer.php';
require_once dirname(__FILE__) . '/../Renderer/EmailRenderer.php';

use AppBundle\Services\JSTree\RecipientCollection;
use AppBundle\Services\JSTree\Renderer\TargetRenderer;
use AppBundle\Services\JSTree\Renderer\EmailRenderer;


class EmailRendererTest extends \PHPUnit_Framework_TestCase
{
    // site admin from CERN & NGI_ZA sites except ZA-CHPC
    public $treeFixtures = array('CERN' =>
    array(
        'check_all' => 'sa_CERN',
        0 => '34G0',
        1 => '18G0',
        2 => '37G0',
        3 => '227G0',
        4 => '2G0',
        5 => '268G0',
        6 => '12G0',
        7 => '19G0',
        8 => '3G0',
        9 => '21G0',
        10 => '10G0',
        11 => '20G0',
        12 => '38G0',
        13 => '26G0',
        14 => '22G0',
        15 => '11G0',
        16 => '46G0',
        17 => '45G0',
        18 => '17G0',
        19 => '27G0'),
        'NGI_ZA' =>
        array(
            0 => '64750G0',
            1 => '68809G0',
            2 => '64600G0')
    );


    public function testConstruct()
    {
        $recipientFixtures = unserialize(file_get_contents(dirname(__FILE__) . '/Fixtures/recipients_fixtures1.txt'));
        var_dump(unserialize(file_get_contents(dirname(__FILE__) . '/Fixtures/recipients_fixtures1.txt')));

        $rColl = new RecipientCollection('sa', 'Site administrators', $recipientFixtures);
        $eR = new EmailRenderer($this->treeFixtures,$rColl);
        return $eR;
    }

    /**
     * @depends testConstruct
     */
    public function testRender(EmailRenderer $eR)
    {
        $resultFixture = unserialize(file_get_contents(dirname(__FILE__) . '/Fixtures/emails_fixtures1.txt'));
        $this->assertEquals($resultFixture, $eR->render());
    }

}
