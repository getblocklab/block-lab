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

		$rows_setting = reset( $this->instance->settings );
		$this->assertEquals( 'help', $rows_setting->name );
		$this->assertEquals( 'Help Text', $rows_setting->label );
		$this->assertEquals( 'text', $rows_setting->type );
		$this->assertEquals( '', $rows_setting->default );
		$this->assertEquals( 'sanitize_text_field', $rows_setting->sanitize );
	}
}
