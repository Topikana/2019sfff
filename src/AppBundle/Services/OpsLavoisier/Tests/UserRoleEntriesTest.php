<?php

require_once dirname(__FILE__) . '/../../Lavoisier/IEntries.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Entries.php';
require_once dirname(__FILE__) . '/../Entries/UserRoleEntries.php';
require_once dirname(__FILE__) . '/../Exceptions/OpsLavoisierServiceException.php';
require_once dirname(__FILE__) . '/../../Lavoisier/IHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/EntriesHydrator.php';

require_once dirname(__FILE__) . '/../../Lavoisier/Exceptions/HTTPStatusException.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Hydrators/DefaultHydrator.php';
require_once dirname(__FILE__) . '/../../Lavoisier/Query.php';
require_once dirname(__FILE__) . '/../BaseService.php';
require_once dirname(__FILE__) . '/../Services/UserRoleService.php';

use \Lavoisier\Hydrators\IHydrator;
use \Lavoisier\IEntries;
use \Lavoisier\Entries\Entries;
use \OpsLavoisier\Entries\UserRoleEntries;
use \Lavoisier\Hydrators\EntriesHydrator;
use \OpsLavoisier\Exceptions\OpsLavoisierServiceException;

use \Lavoisier\Exceptions\HTTPStatusException;
use \OpsLavoisier\Services\UserRoleService;


class UserRoleEntriesTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

        $this->user = new UserRoleEntries(array('user' => array(
            'PRIMARY_KEY' => '306G0',
            'CERTDN' => "/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Foo Bar",
            'CN' => 'Foo Bar',
            'EMAIL' => 'foo.bar@cc.in2p3.fr'
        )));
    }

    public function testHasRole()
    {

        $this->assertFalse($this->user->hasRole());
        // add a role
        $this->user[] = array(
            'ENTITY_TYPE' => 'site',
            'ON_ENTITY' => 'GRIDOPS-OPSPORTAL',
            'USER_ROLE' => 'Site Operations Manager',
        );
        $this->assertTrue($this->user->hasRole());
        $this->assertFalse($this->user->hasRole("foo manager"));
        $this->assertTrue($this->user->hasRole('Manager'));
        $this->assertTrue($this->user->hasRole(null, 'gridops-OPSPORTAL'));

        $this->user[] = array(
            'ENTITY_TYPE' => 'ngi',
            'ON_ENTITY' => 'NGI_FRANCE',
            'USER_ROLE' => 'Regional Staff (ROD)',
        );
        $this->assertTrue($this->user->hasRole(null, null, 'ngi'));
        $this->assertTrue($this->user->hasRole("Staff", "NGI_FRANCE", 'nGi'));
    }

    public function testGetters()
    {
        $this->assertEquals('306G0', $this->user->getId());
        $this->assertEquals("/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Foo Bar", $this->user->getDN());
        $this->assertEquals('Foo Bar', $this->user->getCN());
        $this->assertEquals('foo.bar@cc.in2p3.fr', $this->user->getContactEmail());
    }

    public function testHydratation()
    {
        $input = file_get_contents(dirname(__FILE__) . "/resources/users.xml");
        $hydrator = new EntriesHydrator();
        $hydrator->setRootBinding("\OpsLavoisier\Entries\UserRoleEntries");
        $userData = $hydrator->hydrate($input);

        $this->assertEquals('foo.bar@cc.in2p3.fr', $userData->getContactEmail());
    }

}