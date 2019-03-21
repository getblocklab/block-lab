<?php
/**
 * Tests for class Block_Post.
 *
 * @package Block_Lab
 */

use Block_Lab\Post_Types;
use Block_Lab\Blocks\Controls;

/**
 * Tests for class Block_Post.
 */
class Test_Block_Post extends \WP_UnitTestCase {

	/**
	 * Instance of Block_Post.
	 *
	 * @var Block_Post
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Post_Types\Block_Post();
		$this->instance->register_controls();
		$this->instance->controls['user'] = new Controls\User();
	}

	/**
	 * Test register_hooks().
	 *
	 * @covers \Block_Lab\Post_Types\Block_Post::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();

		$this->assertEquals( 10, has_action( 'init',  array( $this->instance, 'register_post_type' )  ) );
		$this->assertEquals( 10, has_action( 'admin_init',  array( $this->instance, 'add_caps' )  ) );
		$this->assertEquals( 10, has_action( 'admin_init',  array( $this->instance, 'row_export' )  ) );
		$this->assertEquals( 10, has_action( 'add_meta_boxes',  array( $this->instance, 'add_meta_boxes' )  ) );
		$this->assertEquals( 10, has_action( 'add_meta_boxes',  array( $this->instance, 'remove_meta_boxes' )  ) );
		$this->assertEquals( 10, has_action( 'post_submitbox_start',  array( $this->instance, 'save_draft_button' )  ) );
		$this->assertEquals( 10, has_action( 'enter_title_here',  array( $this->instance, 'post_title_placeholder' )  ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts',  array( $this->instance, 'enqueue_scripts' )  ) );
		$this->assertEquals( 10, has_action( 'wp_insert_post_data',  array( $this->instance, 'save_block' )  ) );
		$this->assertEquals( 10, has_action( 'init',  array( $this->instance, 'register_controls' )  ) );
		$this->assertEquals( 10, has_action( 'block_lab_field_value', array( $this->instance, 'get_field_value' ) ) );

		$this->assertEquals( 10, has_action( 'disable_months_dropdown', '__return_true' ) );
		$this->assertEquals( 10, has_action( 'page_row_actions',  array( $this->instance, 'page_row_actions' )  ) );
		$this->assertEquals( 10, has_action( 'bulk_actions-edit-' . $this->instance->slug,  array( $this->instance, 'bulk_actions' )  ) );
		$this->assertEquals( 10, has_action( 'handle_bulk_actions-edit-' . $this->instance->slug,  array( $this->instance, 'bulk_export' )  ) );
		$this->assertEquals( 10, has_action( 'manage_edit-' . $this->instance->slug . '_columns',  array( $this->instance, 'list_table_columns' )  ) );
		$this->assertEquals( 10, has_action( 'manage_' . $this->instance->slug . '_posts_custom_column',  array( $this->instance, 'list_table_content' )  ) );

		$this->assertEquals( 10, has_action( 'wp_ajax_fetch_field_settings',  array( $this->instance, 'ajax_field_settings' )  ) );
	}

	/**
	 * Test register_controls.
	 *
	 * @covers \Block_Lab\Post_Types\Block_Post::register_controls()
	 */
	public function test_register_controls() {
		$this->instance->register_controls();
		foreach ( $this->instance->controls as $control_type => $instance ) {
			$this->assertContains( 'Block_Lab\Blocks\Controls\\', get_class( $instance ) );
		}

		// Because the pro license isn't active, the 'user' control should not display.
		$this->assertFalse( isset( $this->instance->controls['user' ] ) );

		$this->set_valid_license();
		block_lab()->admin->init();
		$this->instance->register_controls();

		// The pro license is active, so the 'user' and 'post' controls should be registered.
		$this->assertEquals( 'Block_Lab\Blocks\Controls\User', get_class( $this->instance->controls['user' ] ) );
		$this->assertEquals( 'Block_Lab\Blocks\Controls\Post', get_class( $this->instance->controls['post' ] ) );
	}

	/**
	 * Test get_field_value
	 *
	 * @covers \Block_Lab\Post_Types\Block_Post::get_field_value()
	 */
	public function test_get_field_value() {
		$invalid_login    = 'asdfg';
		$valid_login      = 'John Doe';
		$expected_wp_user = $this->factory()->user->create_and_get( array( 'user_login' => $valid_login ) );
		$control          = 'user';

		// The 'user' control.
		$this->assertEquals( false, $this->instance->get_field_value( $invalid_login, $control, false ) );
		$this->assertEquals( $expected_wp_user, $this->instance->get_field_value( $valid_login, $control, false ) );
		$this->assertEquals( '', $this->instance->get_field_value( $invalid_login, $control, true ) );
		$this->assertEquals( $expected_wp_user->get( 'display_name' ), $this->instance->get_field_value( $valid_login, $control, true ) );

		// Any value for the 2nd argument other than 'user' should return the passed $value unchanged.
		$this->assertEquals( $invalid_login, $this->instance->get_field_value( $invalid_login, 'different-control', false ) );
		$this->assertEquals( $valid_login, $this->instance->get_field_value( $valid_login, 'random-control', false ) );
		$this->assertEquals( $invalid_login, $this->instance->get_field_value( $invalid_login, 'some-other-control', true ) );

		$string_value  = 'Example string';
		$array_value   = array( 'first value', 'second value' );
		$boolean_value = true;
		$this->assertEquals( $string_value, $this->instance->get_field_value( $string_value, 'non-user-control', true ) );
		$this->assertEquals( $array_value, $this->instance->get_field_value( $array_value, 'some-control', false ) );
		$this->assertEquals( $boolean_value, $this->instance->get_field_value( $boolean_value, 'not-a-user-control', true ) );
	}

	/**
	 * Sets a valid license.
	 */
	public function set_valid_license() {
		set_transient(
			'block_lab_license',
			array(
				'license' => 'valid',
				'expires' => date( '+1 month' ),
			)
		);
	}
}
