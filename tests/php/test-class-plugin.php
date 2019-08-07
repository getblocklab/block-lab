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

	use Testing_Helper;

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
		$this->instance->plugin_loaded();
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
	 * This is essentially the same test as in Test_Utils.
	 * But this tests that the __call() magic method in Plugin works.
	 * This method, is_pro(), is called in the Plugin class.
	 * So this ensures that the magic method refers the call to the Utils class.
	 *
	 * @covers \Block_Lab\Blocks\Utils::is_pro()
	 */
	public function test_is_pro() {
		$this->instance->admin = new Block_Lab\Admin\Admin();
		$this->instance->admin->init();
		$this->set_license_validity( true );
		$this->assertTrue( $this->instance->is_pro() );

		$this->set_license_validity( false );
		$this->assertFalse( $this->instance->is_pro() );
	}

	/**
	 * Test get_template_locations.
	 *
	 * This is also essentially the same test as in Test_Utils.
	 * But this also tests that the __call() magic method in Plugin works.
	 *
	 * @covers \Block_Lab\Blocks\Utils::get_template_locations()
	 */
	public function test_get_template_locations() {
		$name = 'foo-baz';
		$this->assertEquals(
			array(
				"blocks/foo-baz/block.php",
				"blocks/block-foo-baz.php",
				"blocks/block.php",
			),
			$this->instance->get_template_locations( $name )
		);
	}
}
