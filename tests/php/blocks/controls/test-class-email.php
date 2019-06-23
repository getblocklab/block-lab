<?php
/**
 * Tests for class Email.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Email.
 */
class Test_Email extends \WP_UnitTestCase {

	use Control_Helper;

	/**
	 * Instance of Email.
	 *
	 * @var Controls\Email
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Email();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Email::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Email', $this->instance->label );
		$this->assertEquals( 'email', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * The parent constructor calls register_settings(), so there's no need to call it again here.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Email::register_settings()
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
				'type'     => 'email',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_email',
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
		);

		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
	}
}
