<?php
/**
 * Tests for class Checkbox.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Checkbox.
 */
class Test_Checkbox extends \WP_UnitTestCase {

	use Testing_Helper;

	/**
	 * Instance of Checkbox.
	 *
	 * @var Controls\Checkbox
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Checkbox();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Checkbox::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Checkbox', $this->instance->label );
		$this->assertEquals( 'checkbox', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * The parent constructor calls register_settings(), so there's no need to call it again here.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Checkbox::register_settings()
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
				'type'     => 'checkbox',
				'default'  => '0',
				'help'     => '',
				'sanitize' => array( $this->instance, 'sanitize_checkbox' ),
				'validate' => '',
				'value'    => null,
			),
		);

		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
	}
}
