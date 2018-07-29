<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BreedsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BreedsTable Test Case
 */
class BreedsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\BreedsTable
     */
    public $Breeds;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.breeds',
        'app.pets',
        'app.profiles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Breeds') ? [] : ['className' => BreedsTable::class];
        $this->Breeds = TableRegistry::get('Breeds', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Breeds);

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
