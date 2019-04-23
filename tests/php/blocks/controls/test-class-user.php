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
	 * Instance of the extending class User.
	 *
	 * @var Controls\User
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
	 * @covers \Block_Lab\Blocks\Controls\User::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'user', $this->instance->name );
		$this->assertEquals( 'User', $this->instance->label );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\User::register_settings()
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
	 * Test validate.
	 *
	 * @covers \Block_Lab\Blocks\Controls\User::validate()
	 */
	public function test_validate() {
		$expected_wp_user = $this->factory()->user->create_and_get();
		$valid_user_id    = $expected_wp_user->ID;
		$invalid_user_id  = 11111111;

		$this->assertEquals( false, $this->instance->validate( array( 'id' => $invalid_user_id ), false ) );
		$this->assertEquals( $expected_wp_user, $this->instance->validate( array( 'id' => $valid_user_id ), false ) );
		$this->assertEquals( '', $this->instance->validate( array( 'id' => $invalid_user_id ), true ) );
		$this->assertEquals( $expected_wp_user->get( 'display_name' ), $this->instance->validate( array( 'id' => $valid_user_id ), true ) );
	}
}
