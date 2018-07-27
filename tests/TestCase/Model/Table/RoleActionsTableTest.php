<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RoleActionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RoleActionsTable Test Case
 */
class RoleActionsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\RoleActionsTable
     */
    public $RoleActions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.role_actions',
        'app.roles',
        'app.actions'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('RoleActions') ? [] : ['className' => RoleActionsTable::class];
        $this->RoleActions = TableRegistry::get('RoleActions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RoleActions);

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
