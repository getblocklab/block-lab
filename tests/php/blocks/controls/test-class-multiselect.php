<?php
/**
 * Tests for class Multiselect.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Multiselect.
 */
class Test_Multiselect extends \WP_UnitTestCase {

	use Control_Helper;

	/**
	 * Instance of Multiselect.
	 *
	 * @var Controls\Multiselect
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Multiselect();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Multiselect::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Multi-Select', $this->instance->label );
		$this->assertEquals( 'multiselect', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * The parent constructor calls register_settings(), so there's no need to call it again here.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Multiselect::register_settings()
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
				'name'     => 'options',
				'label'    => 'Choices',
				'type'     => 'textarea_array',
				'default'  => '',
				'help'     => 'Enter each choice on a new line. To specify the value and label separately, use this format:<br />foo : Foo<br />bar : Bar',
				'sanitize' => array( $this->instance, 'sanitize_textarea_assoc_array' ),
				'validate' => '',
				'value'    => null,
			),
			array(
				'name'     => 'default',
				'label'    => 'Default Value',
				'type'     => 'textarea_array',
				'default'  => '',
				'help'     => 'Enter each default value on a new line.',
				'sanitize' => array( $this->instance, 'sanitize_textarea_array' ),
				'validate' => array( $this->instance, 'validate_options' ),
				'value'    => null,
			),
		);
		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
	}
}
