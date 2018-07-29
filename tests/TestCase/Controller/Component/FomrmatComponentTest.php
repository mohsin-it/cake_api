<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\FomrmatComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\FomrmatComponent Test Case
 */
class FomrmatComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\Component\FomrmatComponent
     */
    public $Fomrmat;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Fomrmat = new FomrmatComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Fomrmat);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
