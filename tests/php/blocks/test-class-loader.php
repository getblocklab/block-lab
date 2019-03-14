<?php
/**
 * Tests for class Loader.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Tests for class Loader.
 */
class Test_Loader extends \WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Blocks\Loader();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers Plugin::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_action( 'enqueue_block_editor_assets', array( $this->instance, 'editor_assets' ) ) );
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( $this->instance, 'dynamic_block_loader' ) ) );
		$this->assertEquals( 10, has_action( 'block_lab_output_value', array( $this->instance, 'get_output_value' ) ) );
	}

	/**
	 * Test get_output_value.
	 *
	 * @covers get_output_value()
	 */
	public function test_get_output_value() {
		$invalid_login    = 'asdfg';
		$valid_login      = 'John Doe';
		$expected_wp_user = $this->factory()->user->create_and_get( array( 'user_login' => $valid_login ) );
		$control          = 'user';

		// The 'user' control.
		$this->assertEquals( false, $this->instance->get_output_value( $invalid_login, $control, false ) );
		$this->assertEquals( $expected_wp_user, $this->instance->get_output_value( $valid_login, $control, false ) );
		$this->assertEquals( '', $this->instance->get_output_value( $invalid_login, $control, true ) );
		$this->assertEquals( $expected_wp_user->get( 'display_name' ), $this->instance->get_output_value( $valid_login, $control, true ) );

		// Any value for the 2nd argument other than 'user' should return the passed $value unchanged.
		$this->assertEquals( $invalid_login, $this->instance->get_output_value( $invalid_login, 'different-control', false ) );
		$this->assertEquals( $valid_login, $this->instance->get_output_value( $valid_login, 'random-control', false ) );
		$this->assertEquals( $invalid_login, $this->instance->get_output_value( $invalid_login, 'some-other-control', true ) );

		$string_value  = 'Example string';
		$array_value   = array( 'first value', 'second value' );
		$boolean_value = true;
		$this->assertEquals( $string_value, $this->instance->get_output_value( $string_value, 'non-user-control', true ) );
		$this->assertEquals( $array_value, $this->instance->get_output_value( $array_value, 'some-control', false ) );
		$this->assertEquals( $boolean_value, $this->instance->get_output_value( $boolean_value, 'not-a-user-control', true ) );
	}
}
