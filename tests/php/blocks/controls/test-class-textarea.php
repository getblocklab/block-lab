<?php
/**
 * Tests for class Textarea.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Textarea.
 */
class Test_Textarea extends \WP_UnitTestCase {

	/**
	 * Instance of Textarea.
	 *
	 * @var Controls\Textarea
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Textarea();
	}

	/**
	 * Test __construct().
	 *
	 * @covers __construct.
	 */
	public function test_construct() {
		$this->assertEquals( 'Textarea', $this->instance->label );
		$this->assertEquals( 'textarea', $this->instance->name );
	}

	/**
	 * Test register_settings().
	 *
	 * @covers Textarea::register_settings().
	 */
	public function test_register_settings() {
		$this->instance->register_settings();
		foreach ( $this->instance->settings as $setting ) {
			$this->assertEquals( 'Block_Lab\Blocks\Controls\Control_Setting', get_class( $setting ) );
		}

		$rows_setting = end( $this->instance->settings );
		$this->assertEquals( 'number_rows', $rows_setting->name );
		$this->assertEquals( 'Number of Rows', $rows_setting->label );
		$this->assertEquals( 'number_non_negative', $rows_setting->type );
		$this->assertEquals( 4, $rows_setting->default );
		$this->assertEquals( array( $this->instance, 'sanitize_number' ), $rows_setting->sanitize );
	}
}
