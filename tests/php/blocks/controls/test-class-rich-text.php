<?php
/**
 * Tests for class Rich_Text.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Rich_Text.
 */
class Test_Rich_Text extends \WP_UnitTestCase {

	/**
	 * Instance of Rich_Text.
	 *
	 * @var Controls\Rich_Text
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
		$this->instance = new Controls\Rich_Text();
		$this->setting  = new Controls\Control_Setting();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Rich_Text::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Rich Text', $this->instance->label );
		$this->assertEquals( 'rich_text', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Rich_Text::register_settings()
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
		$this->assertEquals( 'placeholder', $rows_setting->name );
		$this->assertEquals( 'Placeholder Text', $rows_setting->label );
		$this->assertEquals( 'text', $rows_setting->type );
		$this->assertEquals( '', $rows_setting->default );
		$this->assertEquals( 'sanitize_text_field', $rows_setting->sanitize );
	}
}
