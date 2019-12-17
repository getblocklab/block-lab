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

	use Testing_Helper;

	/**
	 * Instance of Textarea.
	 *
	 * @var Controls\Textarea
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
	 * The parent constructor calls register_settings(), so there's no need to call it again here.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Textarea::register_settings()
	 */
	public function test_register_settings() {
		$expected_settings = [
			[
				'name'     => 'location',
				'label'    => 'Field Location',
				'type'     => 'location',
				'default'  => 'editor',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_location' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'width',
				'label'    => 'Field Width',
				'type'     => 'width',
				'default'  => '100',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'help',
				'label'    => 'Help Text',
				'type'     => 'text',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'default',
				'label'    => 'Default Value',
				'type'     => 'textarea',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_textarea_field',
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'placeholder',
				'label'    => 'Placeholder Text',
				'type'     => 'text',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'maxlength',
				'label'    => 'Character Limit',
				'type'     => 'number_non_negative',
				'default'  => '',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_number' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'number_rows',
				'label'    => 'Number of Rows',
				'type'     => 'number_non_negative',
				'default'  => 4,
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_number' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'new_lines',
				'label'    => 'New Lines',
				'type'     => 'new_line_format',
				'default'  => 'autop',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_new_line_format' ],
				'validate' => '',
				'value'    => null,
			],
		];

		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
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
	 * @covers \Block_Lab\Blocks\Controls\Textarea::sanitize_new_line_format()
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
