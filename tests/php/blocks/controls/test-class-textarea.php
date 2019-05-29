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
	 * Instance of the setting.
	 *
	 * @var Controls\Control_setting
	 */
	public $setting;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Textarea();
		$this->setting  = new Controls\Control_Setting();
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
		$this->assertEquals( 'new_lines', $rows_setting->name );
		$this->assertEquals( 'New Lines', $rows_setting->label );
		$this->assertEquals( 'new_line_format', $rows_setting->type );
		$this->assertEquals( 'autop', $rows_setting->default );
		$this->assertEquals( array( $this->instance, 'sanitize_new_line_format' ), $rows_setting->sanitize );
	}

	/**
	 * Test render_settings_new_line_format.
	 *
	 * @covers \Block_Lab\Blocks\Controls\textarea::render_settings_new_line_format()
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_select()
	 */
	public function test_render_settings_new_line_format() {
		$name = 'textarea';
		$id   = 'bl_textarea';

		ob_start();
		$this->instance->render_settings_new_line_format( $this->setting, $name, $id );
		$output = ob_get_clean();
		$this->assertContains( 'autop', $output );
		$this->assertContains( 'autobr', $output );
		$this->assertContains( 'none', $output );
	}

	/**
	 * Test get_new_line_formats.
	 *
	 * @covers \Block_Lab\Blocks\Controls\textarea::get_new_line_formats()
	 */
	public function test_get_new_line_formats() {
		$formats = $this->instance->get_new_line_formats();
		$this->assertArrayHasKey( 'autop', $formats );
		$this->assertArrayHasKey( 'autobr', $formats );
		$this->assertArrayHasKey( 'none', $formats );
	}

	/**
	 * Test test_sanitize_new_line_format.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Textarea::test_sanitize_new_line_format()
	 */
	public function test_sanitize_new_line_format() {
		$this->assertEmpty( $this->instance->sanitize_new_line_format( 'foo' ) );
		$this->assertEquals( 'autop', $this->instance->sanitize_new_line_format( 'autop' ) );
		$this->assertEquals( 'autobr', $this->instance->sanitize_new_line_format( 'autobr' ) );
		$this->assertEquals( 'none', $this->instance->sanitize_new_line_format( 'none' ) );

		// If a non-string value is passed, this should return null.
		$this->assertEquals( null, $this->instance->sanitize_new_line_format( false ) );
	}
}
