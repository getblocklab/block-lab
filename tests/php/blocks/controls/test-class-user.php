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
	 * Instance of User.
	 *
	 * @var Controls\User
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\User();
	}

	/**
	 * Test __construct().
	 *
	 * @covers __construct.
	 */
	public function test_construct() {
		$this->assertEquals( 'User', $this->instance->label );
		$this->assertEquals( 'user', $this->instance->name );
	}

	/**
	 * Test register_settings().
	 *
	 * @covers User::register_settings().
	 */
	public function test_register_settings() {
		$this->instance->register_settings();
		foreach ( $this->instance->settings as $setting ) {
			$this->assertEquals( 'Block_Lab\Blocks\Controls\Control_Setting', get_class( $setting ) );
		}

		$help_setting = reset( $this->instance->settings );
		$this->assertEquals( 'help', $help_setting->name );
		$this->assertEquals( 'Help Text', $help_setting->label );
		$this->assertEquals( 'text', $help_setting->type );
		$this->assertEquals( '', $help_setting->default );
		$this->assertEquals( 'sanitize_text_field', $help_setting->sanitize );

		$placeholder_setting = end( $this->instance->settings );
		$this->assertEquals( 'placeholder', $placeholder_setting->name );
		$this->assertEquals( 'Placeholder Text', $placeholder_setting->label );
		$this->assertEquals( 'text', $placeholder_setting->type );
		$this->assertEquals( '', $placeholder_setting->default );
		$this->assertEquals( 'sanitize_text_field', $placeholder_setting->sanitize );
	}
}
