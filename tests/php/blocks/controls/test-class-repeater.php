<?php
/**
 * Tests for class Repeater.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Repeater.
 */
class Test_Repeater extends \WP_UnitTestCase {

	/**
	 * Instance of Repeater.
	 *
	 * @var Controls\Repeater
	 */
	public $instance;

	/**
	 * Instance of the setting.
	 *
	 * @var Controls\Control_Setting
	 */
	public $setting;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Repeater();
		$this->setting  = new Controls\Control_Setting();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Repeater::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Repeater', $this->instance->label );
		$this->assertEquals( 'repeater', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Repeater::register_settings()
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
	}
}
