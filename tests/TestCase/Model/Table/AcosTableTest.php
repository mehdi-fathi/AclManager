<?php
namespace AclManager\Test\TestCase\Model\Table;

use AclManager\Model\Table\AcosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * AclManager\Model\Table\AcosTable Test Case
 */
class AcosTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.acl_manager.acos',
        'plugin.acl_manager.roles',
        'plugin.acl_manager.acos_roles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Acos') ? [] : ['className' => 'AclManager\Model\Table\AcosTable'];
        $this->Acos = TableRegistry::get('Acos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Acos);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
