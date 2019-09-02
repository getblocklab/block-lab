<?php
/**
 * Tests for class Control_Abstract.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Control_Abstract.
 */
class Test_Control_Abstract extends \WP_UnitTestCase {

	/**
	 * A mock name of the control.
	 *
	 * @var string
	 */
	const NAME = 'block-fields-settings[5c6c6bcf03d2c][default]';

	/**
	 * A mock ID of the control.
	 *
	 * @var string
	 */
	const ID = 'block-fields-edit-settings-number-default_5c6c6bcf03d2c';

	/**
	 * Instance of the extending class Number.
	 *
	 * @var Controls\Number
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
		$this->instance = new Controls\Number();
		$this->setting  = new Controls\Control_Setting();
	}

	/**
	 * Test create_settings_config.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::create_settings_config()
	 */
	public function test_create_settings_config() {
		$this->assertArraySubset(
			array(
				'location'    => array(
					'name'     => 'location',
					'label'    => __( 'Field Location', 'block-lab' ),
					'type'     => 'location',
					'default'  => 'editor',
					'sanitize' => array( $this->instance, 'sanitize_location' ),
				),
				'width'       => array(
					'name'     => 'width',
					'label'    => __( 'Field Width', 'block-lab' ),
					'type'     => 'width',
					'default'  => '100',
					'sanitize' => 'sanitize_text_field',
				),
				'help'        => array(
					'name'     => 'help',
					'label'    => __( 'Help Text', 'block-lab' ),
					'type'     => 'text',
					'default'  => '',
					'sanitize' => 'sanitize_text_field',
				),
				'default'     => array(
					'name'     => 'default',
					'label'    => __( 'Default Value', 'block-lab' ),
					'type'     => 'text',
					'default'  => '',
					'sanitize' => 'sanitize_text_field',
				),
				'placeholder' => array(
					'name'     => 'placeholder',
					'label'    => __( 'Placeholder Text', 'block-lab' ),
					'type'     => 'text',
					'default'  => '',
					'sanitize' => 'sanitize_text_field',
				),
			),
			$this->instance->settings_config
		);

		$this->assertArraySubset(
			array(
				'editor'    => __( 'Editor', 'block-lab' ),
				'inspector' => __( 'Inspector', 'block-lab' ),
			),
			$this->instance->locations
		);
	}

	/**
	 * Test render_settings_number.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_settings_number()
	 */
	public function test_render_settings_number() {
		ob_start();
		$this->instance->render_settings_number( $this->setting, self::NAME, self::ID );
		$output = ob_get_clean();

		// This should not have a min="0" attribute.
		$this->assertNotContains( 'min="0"', $output );
		$this->assertContains( self::NAME, $output );
		$this->assertContains( self::ID, $output );
	}

	/**
	 * Test render_settings_number_non_negative.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_settings_number_non_negative()
	 */
	public function test_render_settings_number_non_negative() {
		ob_start();
		$this->instance->render_settings_number_non_negative( $this->setting, self::NAME, self::ID );
		$output = ob_get_clean();

		// This should have a min="0" attribute.
		$this->assertContains( 'min="0"', $output );
		$this->assertContains( self::NAME, $output );
		$this->assertContains( self::ID, $output );
	}

	/**
	 * Test render_number.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_number()
	 */
	public function test_render_number() {
		$min_attribute = 'min="0"';
		ob_start();
		$this->instance->render_number( $this->setting, self::NAME, self::ID );
		$output = ob_get_clean();

		// This should not have a min="0" attribute, as there is no 4th argument.
		$this->assertNotContains( $min_attribute, $output );

		ob_start();
		$this->instance->render_number( $this->setting, self::NAME, self::ID, true );
		$output = ob_get_clean();

		// This should  have a min="0" attribute, as the 4th argument is true.
		$this->assertContains( $min_attribute, $output );
	}

	/**
	 * Test render_select.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_select()
	 */
	public function test_render_select() {
		$options = array(
			'foo' => 'One',
			'bar' => 'Two',
			'baz' => 'Three',
		);
		ob_start();
		$this->instance->render_select( $this->setting, self::NAME, self::ID, $options );
		$output = ob_get_clean();

		$this->assertContains( 'value="foo"', $output );
		$this->assertContains( 'value="bar"', $output );
		$this->assertContains( 'value="baz"', $output );
		$this->assertContains( 'One', $output );
		$this->assertContains( 'Two', $output );
		$this->assertContains( 'Three', $output );
	}

	/**
	 * Test render_settings_location.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_settings_location()
	 */
	public function test_render_settings_location() {
		ob_start();
		$this->instance->render_settings_location( $this->setting, self::NAME, self::ID );
		$output = ob_get_clean();

		$this->assertContains( 'value="editor"', $output );
		$this->assertContains( 'value="inspector"', $output );
		$this->assertContains( 'Editor', $output );
		$this->assertContains( 'Inspector', $output );
	}

	/**
	 * Test sanitize_location.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::sanitize_location()
	 */
	public function test_sanitize_location() {
		$wrong_locations = array( 'incorrect', 'classic-editor', 'foo-baz', false, null );
		foreach ( $wrong_locations as $wrong_location ) {
			$this->assertEquals( null, $this->instance->sanitize_location( $wrong_location ) );
		}

		$correct_locations = array( 'editor', 'inspector' );
		foreach ( $correct_locations as $correct_location ) {
			$this->assertEquals( $correct_location, $this->instance->sanitize_location( $correct_location ) );
		}
	}

	/**
	 * Test render_settings_width.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_settings_width()
	 */
	public function test_render_settings_width() {
		ob_start();
		$this->instance->render_settings_width( $this->setting, self::NAME, self::ID );
		$output = ob_get_clean();

		$this->assertContains( 'button-group', $output );

		$widths = array(
			'25'  => '25%',
			'50'  => '50%',
			'75'  => '75%',
			'100' => '100%',
		);

		foreach ( $widths as $value => $label ) {
			$this->assertContains( strval( $value ), $output );
			$this->assertContains( strval( $label ), $output );
		}
	}

	/**
	 * Test render_button_group.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_button_group()
	 */
	public function test_render_button_group() {
		$buttons = array(
			'foo' => 'Bar',
			'baz' => 'Qux',
		);
		ob_start();
		$this->instance->render_button_group( $this->setting, self::NAME, self::ID, $buttons );
		$output = ob_get_clean();

		$this->assertContains( 'button-group', $output );

		foreach ( $buttons as $value => $label ) {
			$this->assertContains( $value, $output );
			$this->assertContains( $label, $output );
		}
	}
}
