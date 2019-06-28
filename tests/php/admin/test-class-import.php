<?php
/**
 * Tests for class Import.
 *
 * @package Block_Lab
 */

use Block_Lab\Admin;
use Brain\Monkey;

/**
 * Tests for class Import.
 */
class Test_Import extends \WP_UnitTestCase {

	use Testing_Helper;

	/**
	 * Instance of Import.
	 *
	 * @var Admin\Import
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();
		$this->instance = new Admin\Import();
		$this->instance->set_plugin( block_lab() );
	}

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers \Block_Lab\Admin\Import::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_filter( 'admin_init', array( $this->instance, 'register_importer' ) ) );
	}

	/**
	 * Test register_importer.
	 *
	 * @covers \Block_Lab\Admin\Import::register_importer()
	 */
	public function test_register_importer() {
		global $wp_importers;

		$this->instance->register_importer();
		$this->assertEquals(
			array(
				'Block Lab',
				'Import custom blocks created with Block Lab.',
				array( $this->instance, 'render_page' )
			),
			$wp_importers[ $this->instance->slug ]
		);
	}
}
