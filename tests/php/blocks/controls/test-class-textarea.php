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
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Textarea::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Textarea', $this->instance->label );
		$this->assertEquals( 'textarea', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Textarea::register_settings()
	 */
	public function test_register_settings() {
		$this->instance->register_settings();
		foreach ( $this->instance->settings as $setting ) {
			$this->assertEquals( 'Block_Lab\Blocks\Controls\Control_Setting', get_class( $setting ) );
		}

		$rows_setting = reset( $this->instance->settings );
		$this->assertEquals( 'help', $rows_setting->name );
		$this->assertEquals( 'Help Text', $rows_setting->label );
		$this->assertEquals( 'text', $rows_setting->type );
		$this->assertEquals( '', $rows_setting->default );
		$this->assertEquals( 'sanitize_text_field', $rows_setting->sanitize );

		$rows_setting = end( $this->instance->settings );
		$this->assertEquals( 'should_autop', $rows_setting->name );
		$this->assertEquals( 'Convert newlines to p tags', $rows_setting->label );
		$this->assertEquals( 'checkbox', $rows_setting->type );
		$this->assertEquals( 0, $rows_setting->default );
		$this->assertEquals( array( $this->instance, 'sanitize_checkbox' ), $rows_setting->sanitize );
	}
}
