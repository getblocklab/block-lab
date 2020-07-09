<?php
/**
 * Test_Submenu
 *
 * @package Block_Lab
 */

use Block_Lab\Admin\Migration\Submenu;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Class Test_Submenu
 *
 * @package Block_Lab
 */
class Test_Submenu extends WP_UnitTestCase {

	use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	/**
	 * The instance to test.
	 *
	 * @var Submenu
	 */
	public $instance;

	/**
	 * Sets up each test.
	 *
	 * @inheritDoc
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();
		$this->instance = new Submenu();
	}

	/**
	 * Tears down after each test.
	 *
	 * @inheritDoc
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 9, has_action( 'admin_menu', [ $this->instance, 'add_submenu_page' ] ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $this->instance, 'enqueue_scripts' ] ) );
		$this->assertEquals( 10, has_action( 'admin_bar_init', [ $this->instance, 'maybe_activate_plugin' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_migrate_post_content' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_migrate_post_type' ] ) );
	}

	/**
	 * Test add_submenu_page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::add_submenu_page()
	 */
	public function test_add_submenu_pages() {
		$this->set_admin_user();
		Functions\expect( 'add_submenu_page' )
			->once()
			->with(
				'edit.php?post_type=block_lab',
				'Migrate to Genesis Custom Blocks',
				'Migrate',
				'manage_options',
				'block-lab-migration',
				[ $this->instance, 'render_page' ]
			);

		$this->instance->add_submenu_page();
	}

	/**
	 * Test enqueue_scripts when not on a page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::enqueue_scripts()
	 */
	public function test_enqueue_scripts_not_on_page() {
		$this->set_admin_user();
		$this->instance->enqueue_scripts();
		$this->assertFalse( wp_style_is( 'block-lab-migration' ) );
		$this->assertFalse( wp_script_is( 'block-lab-migration' ) );
	}

	/**
	 * Test enqueue_scripts when on the wrong page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::enqueue_scripts()
	 */
	public function test_enqueue_scripts_wrong_page() {
		$this->set_admin_user();
		Monkey\Functions\expect( 'filter_input' )
			->once()
			->with(
				INPUT_GET,
				'page',
				FILTER_SANITIZE_STRING
			)
			->andReturn( 'wrong-page' );

		$this->instance->enqueue_scripts();
		$this->assertFalse( wp_style_is( 'block-lab-migration' ) );
		$this->assertFalse( wp_script_is( 'block-lab-migration' ) );
	}

	/**
	 * Test enqueue_scripts on the right page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::enqueue_scripts()
	 */
	public function test_enqueue_scripts_right_page() {
		$this->set_admin_user();
		Monkey\Functions\expect( 'filter_input' )
			->once()
			->with(
				INPUT_GET,
				'page',
				FILTER_SANITIZE_STRING
			)
			->andReturn( 'block-lab-migration' );

		$this->instance->enqueue_scripts();
		$this->assertTrue( wp_style_is( 'block-lab-migration' ) );
		$this->assertTrue( wp_script_is( 'block-lab-migration' ) );
	}

	/**
	 * Test user_can_view_migration_page with a non-admin user.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::user_can_view_migration_page()
	 */
	public function test_user_can_view_migration_page_non_admin() {
		$this->assertFalse( $this->instance->user_can_view_migration_page() );
	}

	/**
	 * Test user_can_view_migration_page with an admin user.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::user_can_view_migration_page()
	 */
	public function test_user_can_view_migration_page_admin() {
		$this->set_admin_user();
		$this->assertTrue( $this->instance->user_can_view_migration_page() );
	}

	/**
	 * Test render_page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::render_page()
	 */
	public function test_render_page() {
		ob_start();
		$this->instance->render_page();

		$this->assertContains(
			'<div class="bl-migration__content"></div>',
			ob_get_clean()
		);
	}

	/**
	 * Test maybe_activate_plugin with no query var.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::maybe_activate_plugin()
	 */
	public function test_maybe_activate_plugin_no_query_var() {
		$error = $this->get_plugin_activation_error();
		$this->assertFalse( isset( $error ) );
	}

	/**
	 * Test maybe_activate_plugin with the correct query var.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::maybe_activate_plugin()
	 */
	public function test_maybe_activate_plugin_correct_query_var() {
		$_GET['bl_deactivate_and_activate'] = true;

		$error = $this->get_plugin_activation_error();
		$this->assertEquals( 'Sorry, you are not allowed to deactivate this plugin.', $error->getMessage() );
	}

	/**
	 * Test maybe_activate_plugin with the correct user.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::maybe_activate_plugin()
	 */
	public function test_maybe_activate_plugin_correct_user() {
		$_GET['bl_deactivate_and_activate'] = true;
		$user_id                            = $this->factory()->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );
		if ( is_multisite() ) {
			grant_super_admin( $user_id );
		}

		// The nonce is not present, so there should be an error.
		$error = $this->get_plugin_activation_error();
		$this->assertEquals( 'The link you followed has expired.', $error->getMessage() );
	}

	/**
	 * Test maybe_activate_plugin with the nonce present.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::maybe_activate_plugin()
	 */
	public function test_maybe_activate_plugin_nonce_present() {
		$_GET['bl_deactivate_and_activate'] = true;
		$user_id                            = $this->factory()->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );
		$_REQUEST = [ '_wpnonce' => wp_create_nonce( 'deactivate_bl_and_activate_new' ) ];
		if ( is_multisite() ) {
			grant_super_admin( $user_id );
		}

		// Now that the nonce is correct, this should redirect to the URL to activate the plugin.
		Monkey\Functions\expect( 'wp_safe_redirect' )
			->once();

		$this->get_plugin_activation_error();
	}

	/**
	 * Test register_route_migrate_post_content.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::register_route_migrate_post_content()
	 */
	public function test_register_route_migrate_post_content() {
		do_action( 'rest_api_init' );
		$this->instance->register_route_migrate_post_content();
		$routes = rest_get_server()->get_routes();

		$this->assertArrayHasKey( '/block-lab/migrate-post-content', $routes );
	}

	/**
	 * Test get_migrate_post_content_response.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::get_migrate_post_content_response()
	 */
	public function test_get_migrate_post_content_response() {
		$this->assertEquals( 'WP_REST_Response', get_class( $this->instance->get_migrate_post_content_response() ) );
	}

	/**
	 * Test register_route_migrate_post_type.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::register_route_migrate_post_type()
	 */
	public function test_register_route_migrate_post_type() {
		do_action( 'rest_api_init' );
		$this->instance->register_route_migrate_post_type();
		$routes = rest_get_server()->get_routes();

		$this->assertArrayHasKey( '/block-lab/migrate-post-type', $routes );
	}

	/**
	 * Test get_migrate_post_type_response.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::get_migrate_post_type_response()
	 */
	public function test_get_migrate_post_type_response() {
		$this->assertEquals( 'WP_REST_Response', get_class( $this->instance->get_migrate_post_type_response() ) );
	}

	/**
	 * Gets the error from activating the plugin, if any.
	 *
	 * @return Exception|null The error if there was one, or null.
	 */
	public function get_plugin_activation_error() {
		try {
			$this->instance->maybe_activate_plugin();
		} catch ( Exception $e ) {
			$error = $e;
		}

		return isset( $error ) ? $error : null;
	}

	/**
	 * Sets the current user as an administrator, and a super admin in multisite.
	 */
	public function set_admin_user() {
		$user_id = $this->factory()->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );
		if ( is_multisite() ) {
			grant_super_admin( $user_id );
		}
	}
}
