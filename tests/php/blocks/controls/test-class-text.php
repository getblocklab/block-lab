<?php
/**
 * Tests for class Text.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Text.
 */
class Test_Text extends \WP_UnitTestCase {

	use Control_Helper;

	/**
	 * Instance of Text.
	 *
	 * @var Controls\Text
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Text();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Text::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Text', $this->instance->label );
		$this->assertEquals( 'text', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * The parent constructor calls register_settings(), so there's no need to call it again here.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Text::register_settings()
	 */
	public function test_register_settings() {
		$expected_settings = array(
			array(
				'name'     => 'location',
				'label'    => 'Location',
				'type'     => 'location',
				'default'  => 'editor',
				'help'     => '',
				'sanitize' => array( $this->instance, 'sanitize_location' ),
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
			array(
				'name'     => 'default',
				'label'    => 'Default Value',
				'type'     => 'text',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			),
			array(
				'name'     => 'placeholder',
				'label'    => 'Placeholder Text',
				'type'     => 'text',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			),
			array(
				'name'     => 'maxlength',
				'label'    => 'Character Limit',
				'type'     => 'number_non_negative',
				'default'  => '',
				'help'     => '',
				'sanitize' => array( $this->instance, 'sanitize_number' ),
				'validate' => '',
				'value'    => null,
			),
		);

		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
	}
}
