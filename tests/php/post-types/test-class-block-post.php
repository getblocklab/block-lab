<?php
/**
 * Tests for class Block_Post.
 *
 * @package Block_Lab
 */

namespace Block_Lab\Post_Types;

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
		$this->instance = new Block_Post();
	}

	/**
	 * Test register_hooks().
	 *
	 * @covers Block_Post::register_hooks().
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
		$this->assertEquals( 10, has_action( 'admin_init',  array( $this->instance, 'register_controls' )  ) );

		$this->assertEquals( 10, has_action( 'disable_months_dropdown', '__return_true' ) );
		$this->assertEquals( 10, has_action( 'page_row_actions',  array( $this->instance, 'page_row_actions' )  ) );
		$this->assertEquals( 10, has_action( 'bulk_actions-edit-' . $this->instance->slug,  array( $this->instance, 'bulk_actions' )  ) );
		$this->assertEquals( 10, has_action( 'handle_bulk_actions-edit-' . $this->instance->slug,  array( $this->instance, 'bulk_export' )  ) );
		$this->assertEquals( 10, has_action( 'manage_edit-' . $this->instance->slug . '_columns',  array( $this->instance, 'list_table_columns' )  ) );
		$this->assertEquals( 10, has_action( 'manage_' . $this->instance->slug . '_posts_custom_column',  array( $this->instance, 'list_table_content' )  ) );

		$this->assertEquals( 10, has_action( 'wp_ajax_fetch_field_settings',  array( $this->instance, 'ajax_field_settings' )  ) );
	}

	/**
	 * Test register_controls().
	 *
	 * @covers Block_Post::register_controls.
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

		// The pro license is active, so the 'user' control should be registered.
		$this->assertEquals( 'Block_Lab\Blocks\Controls\User', get_class( $this->instance->controls['user' ] ) );
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
