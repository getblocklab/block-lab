<?php
/**
 * Tests for class Settings.
 *
 * @package Block_Lab
 */

use Block_Lab\Admin;
use Brain\Monkey;

/**
 * Tests for class Settings.
 */
class Test_Settings extends \WP_UnitTestCase {

	/**
	 * Instance of Settings.
	 *
	 * @var Admin\Settings
	 */
	public $instance;

	/**
	 * The option name for the notices.
	 *
	 * @var string
	 */
	const NOTICES_OPTION_NAME = 'block_lab_notices';

	/**
	 * The slug of the parent of the submenu.
	 *
	 * @var string
	 */
	const SUBMENU_PARENT_SLUG = 'edit.php?post_type=block_lab';

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();
		$this->instance = new Admin\Settings();
		$this->instance->set_plugin( block_lab() );

	}

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		global $submenu;

		unset( $submenu[ self::SUBMENU_PARENT_SLUG ] );
		delete_option( self::NOTICES_OPTION_NAME );
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers \Block_Lab\Admin\Settings::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_action( 'admin_menu', [ $this->instance, 'add_submenu_pages' ] ) );
		$this->assertEquals( 10, has_action( 'admin_init', [ $this->instance, 'register_settings' ] ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $this->instance, 'enqueue_scripts' ] ) );
		$this->assertEquals( 10, has_action( 'admin_notices', [ $this->instance, 'show_notices' ] ) );
	}

	/**
	 * Test enqueue_scripts.
	 *
	 * @covers \Block_Lab\Admin\Settings::enqueue_scripts()
	 */
	public function test_enqueue_scripts() {
		$this->instance->enqueue_scripts();
		$styles = wp_styles();

		// Because filter_input() should return nothing, the conditional should be false, and this shouldn't enqueue the script.
		$this->assertFalse( in_array( $this->instance->slug, $styles->queue, true ) );
		$this->assertFalse( in_array( $this->instance->slug, $styles->registered, true ) );

		Monkey\Functions\expect( 'filter_input' )
			->once()
			->with(
				INPUT_GET,
				'page',
				FILTER_SANITIZE_STRING
			)
			->andReturn( 'incorrect-page' );

		$this->instance->enqueue_scripts();
		$styles = wp_styles();

		// Because filter_input() returns the wrong page, the conditional should again be false and this shouldn't enqueue the script.
		$this->assertFalse( in_array( $this->instance->slug, $styles->queue, true ) );
		$this->assertFalse( in_array( $this->instance->slug, $styles->registered, true ) );

		Monkey\Functions\expect( 'filter_input' )
			->once()
			->with(
				INPUT_GET,
				'page',
				FILTER_SANITIZE_STRING
			)
			->andReturn( $this->instance->slug );

		$this->instance->enqueue_scripts();
		$styles = wp_styles();
		$style  = $styles->registered[ $this->instance->slug ];

		// Now that filter_input() returns the correct page, the conditional should be true, and this should enqueue the script.
		$this->assertTrue( in_array( $this->instance->slug, $styles->queue, true ) );
		$this->assertEquals( $this->instance->slug, $style->handle );
		$this->assertContains( 'block-lab/css/admin.settings.css', $style->src );
		$this->assertEquals( [], $style->deps );
		$this->assertEquals( [], $style->extra );
	}

	/**
	 * Test add_submenu_pages.
	 *
	 * @covers \Block_Lab\Admin\Settings::add_submenu_pages()
	 */
	public function test_add_submenu_pages() {
		global $submenu;

		$expected_submenu_settings = [
			'Settings',
			'manage_options',
			$this->instance->slug,
			'Block Lab Settings',
		];

		wp_set_current_user( $this->factory()->user->create( [ 'role' => 'author' ] ) );
		$this->instance->add_submenu_pages();

		// Because the current user doesn't have 'manage_options' permissions, this shouldn't add the submenu.
		$this->assertFalse( isset( $submenu ) && array_key_exists( self::SUBMENU_PARENT_SLUG, $submenu ) );

		wp_set_current_user( $this->factory()->user->create( [ 'role' => 'administrator' ] ) );
		$this->instance->add_submenu_pages();

		// Now that the user has 'manage_options' permissions, this should add the submenu.
		$this->assertEquals( [ $expected_submenu_settings ], $submenu[ self::SUBMENU_PARENT_SLUG ] );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Admin\Settings::register_settings()
	 */
	public function test_register_settings() {
		global $wp_registered_settings;

		$this->instance->register_settings();
		$expected_option_group = 'block-lab-license-key';
		$expected_option_name  = 'block_lab_license_key';
		$this->assertEquals(
			[
				'description'       => '',
				'group'             => $expected_option_group,
				'sanitize_callback' => null,
				'show_in_rest'      => false,
				'type'              => 'string',
			],
			$wp_registered_settings[ $expected_option_name ]
		);
	}

	/**
	 * Test render_page.
	 *
	 * @covers \Block_Lab\Admin\Settings::render_page()
	 */
	public function test_render_page() {
		ob_start();
		$this->instance->render_page();
		$output = ob_get_clean();

		$this->assertContains( '<div class="wrap block-lab-settings">', $output );
		$this->assertContains( '<a href="?tab=license" title="License" class="nav-tab nav-tab-active dashicons-before dashicons-nametag">', $output );
	}

	/**
	 * Test render_page_header.
	 *
	 * @covers \Block_Lab\Admin\Settings::render_page_header()
	 */
	public function test_render_page_header() {
		ob_start();
		$this->instance->render_page();
		$output = ob_get_clean();

		$this->assertContains( '<h2 class="nav-tab-wrapper">', $output );
		$this->assertContains( '<a href="https://getblocklab.com/docs/" target="_blank" class="nav-tab dashicons-before dashicons-info">', $output );
	}

	/**
	 * Test prepare_notice.
	 *
	 * @covers \Block_Lab\Admin\Settings::prepare_notice()
	 */
	public function test_prepare_notice() {
		$notice = 'There was a problem activating your Block Lab license.';
		$this->instance->prepare_notice( $notice );

		$this->assertEquals( [ $notice ], get_option( self::NOTICES_OPTION_NAME ) );

		$existing_notices = [
			'first notice',
			'second notice',
		];

		update_option( self::NOTICES_OPTION_NAME, $existing_notices );
		$this->instance->prepare_notice( $notice );
		$this->assertEquals(
			array_merge(
				$existing_notices,
				[ $notice ]
			),
			get_option( self::NOTICES_OPTION_NAME )
		);
	}

	/**
	 * Test show_notices.
	 *
	 * @covers \Block_Lab\Admin\Settings::show_notices()
	 */
	public function test_show_notices() {
		ob_start();
		$this->instance->show_notices();

		// Because there are no notices stored in the option, this should not output anything.
		$this->assertEmpty( ob_get_clean() );

		$non_array_notice = 'This is a notice value';
		update_option( self::NOTICES_OPTION_NAME, $non_array_notice );
		ob_start();
		$this->instance->show_notices();
		$output = ob_get_clean();

		// Because the notice was not in an array, this should not output anything.
		$this->assertEmpty( $output );

		// The option should not have been deleted, as this should have exited from the function.
		$this->assertEquals( $non_array_notice, get_option( self::NOTICES_OPTION_NAME ) );

		$expected_notices = [
			'Here is a notice',
			'This is also a notice',
		];
		update_option( self::NOTICES_OPTION_NAME, $expected_notices );
		ob_start();
		$this->instance->show_notices();
		$output = ob_get_clean();

		// The notices are correctly stored in an array, so this method should output them.
		foreach ( $expected_notices as $expected_notice ) {
			$this->assertContains( $expected_notice, $output );
		}

		// The option should have been deleted.
		$this->assertEmpty( get_option( self::NOTICES_OPTION_NAME ) );
	}
}
