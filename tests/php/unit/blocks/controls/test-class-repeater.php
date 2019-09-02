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

	use Testing_Helper;

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
		$this->assertEquals( 'object', $this->instance->type );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Repeater::register_settings()
	 */
	public function test_register_settings() {
		$expected_settings = array(
			array(
				'name'     => 'columns',
				'label'    => __( 'Columns', 'block-lab' ),
				'type'     => 'columns',
				'default'  => '100',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			),
			array(
				'name'     => 'help',
				'label'    => 'Help Text',
				'type'     => 'text',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			),
		);

		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
	}

	/**
	 * Test render_settings_columns.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Repeater::render_settings_columns()
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_button_group()
	 */
	public function test_render_settings_columns() {
		$name = 'repeater';
		$id   = 'bl_repeater';

		ob_start();
		$this->instance->render_settings_columns( $this->setting, $name, $id );
		$output = ob_get_clean();

		$this->assertContains( 'button-group', $output );

		$columns = array(
			'100' => '1',
			'50'  => '2',
			'33'  => '3',
			'25'  => '4',
		);

		foreach ( $columns as $value => $label ) {
			$this->assertContains( strval( $value ), $output );
			$this->assertContains( strval( $label ), $output );
		}
	}
}
