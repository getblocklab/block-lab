<?php
/**
 * Tests for class Plugin.
 *
 * @package Block_Lab
 */

/**
 * Tests for class Plugin.
 */
class Test_Plugin extends \WP_UnitTestCase {

	use Control_Helper;

	/**
	 * Instance of Plugin.
	 *
	 * @var Plugin
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Block_Lab\Plugin();
	}

	/**
	 * Test plugin_loaded.
	 *
	 * @covers \Block_Lab\Plugin::plugin_loaded()
	 */
	public function test_plugin_loaded() {
		$this->instance->plugin_loaded();
		$this->assertEquals( 'Block_Lab\Admin\Admin', get_class( $this->instance->admin ) );
	}

	/**
	 * Test is_pro.
	 *
	 * @covers \Block_Lab\Plugin::is_pro()
	 */
	public function test_is_pro() {
		$this->instance->admin = new Block_Lab\Admin\Admin();
		$this->instance->admin->init();
		$this->set_license_validity( true );
		$this->assertTrue( $this->instance->is_pro() );

		$this->set_license_validity( false );
		$this->assertFalse( $this->instance->is_pro() );
	}
}
