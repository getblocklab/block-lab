<?php
/**
 * Tests for class User.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class User.
 */
class Test_User extends \WP_UnitTestCase {

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
		$this->instance = new Controls\User();
		$this->setting  = new Controls\Control_Setting();
	}
	/**
	 * Test __construct.
	 *
	 * @covers __construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'user', $this->instance->name );
		$this->assertEquals( 'User', $this->instance->label );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers User::register_settings()
	 */
	public function test_register_settings() {
		$this->instance->register_settings();

		$first_setting = reset( $this->instance->settings );
		$this->assertEquals( 'help', $first_setting->name );
		$this->assertEquals( 'Help Text', $first_setting->label );
		$this->assertEquals( 'text', $first_setting->type );
		$this->assertEquals( '', $first_setting->default );
		$this->assertEquals( 'sanitize_text_field', $first_setting->sanitize );

		$user_setting = end( $this->instance->settings );
		$this->assertEquals( 'placeholder', $user_setting->name );
		$this->assertEquals( 'Placeholder Text', $user_setting->label );
		$this->assertEquals( 'text', $user_setting->type );
		$this->assertEquals( '', $user_setting->default );
		$this->assertEquals( 'sanitize_text_field', $user_setting->sanitize );
	}

	/**
	 * Test output.
	 *
	 * @covers output()
	 */
	public function test_output() {
		$invalid_login    = 'notvalie';
		$valid_login      = 'Jonas Doe';
		$expected_wp_user = $this->factory()->user->create_and_get( array( 'user_login' => $valid_login ) );

		$this->assertEquals( false, $this->instance->output( $invalid_login, false ) );
		$this->assertEquals( $expected_wp_user, $this->instance->output( $valid_login, false ) );
		$this->assertEquals( '', $this->instance->output( $invalid_login, true ) );
		$this->assertEquals( $expected_wp_user->get( 'display_name' ), $this->instance->output( $valid_login, true ) );
	}
}
