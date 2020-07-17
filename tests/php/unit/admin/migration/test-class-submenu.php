<?php
/**
 * Test_Submenu
 *
 * @package Block_Lab
 */

use Block_Lab\Admin\Migration\Submenu;
use Block_Lab\Admin\License;
use Brain\Monkey;
use function Brain\Monkey\Functions\expect;

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
	 * @covers Block_Lab\Admin\Migration\Submenu::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 9, has_action( 'admin_menu', [ $this->instance, 'add_submenu_page' ] ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $this->instance, 'enqueue_scripts' ] ) );
		$this->assertEquals( 10, has_action( 'admin_bar_init', [ $this->instance, 'maybe_activate_plugin' ] ) );
	}

	/**
	 * Test add_submenu_page.
	 *
	 * @covers Block_Lab\Admin\Migration\Submenu::add_submenu_page()
	 */
	public function test_add_submenu_pages() {
		$this->set_admin_user();
		expect( 'add_submenu_page' )
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
	 * @covers Block_Lab\Admin\Migration\Submenu::enqueue_scripts()
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
	 * @covers Block_Lab\Admin\Migration\Submenu::enqueue_scripts()
	 */
	public function test_enqueue_scripts_wrong_page() {
		$this->set_admin_user();
		expect( 'filter_input' )
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
	 * @covers Block_Lab\Admin\Migration\Submenu::enqueue_scripts()
	 */
	public function test_enqueue_scripts_right_page() {
		$this->set_admin_user();
		expect( 'filter_input' )
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
	 * @covers Block_Lab\Admin\Migration\Submenu::user_can_view_migration_page()
	 */
	public function test_user_can_view_migration_page_non_admin() {
		$this->assertFalse( $this->instance->user_can_view_migration_page() );
	}

	/**
	 * Test user_can_view_migration_page with an admin user.
	 *
	 * @covers Block_Lab\Admin\Migration\Submenu::user_can_view_migration_page()
	 */
	public function test_user_can_view_migration_page_admin() {
		$this->set_admin_user();
		$this->assertTrue( $this->instance->user_can_view_migration_page() );
	}

	/**
	 * Test render_page.
	 *
	 * @covers Block_Lab\Admin\Migration\Submenu::render_page()
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
	 * @covers Block_Lab\Admin\Migration\Submenu::maybe_activate_plugin()
	 */
	public function test_maybe_activate_plugin_no_query_var() {
		$error = $this->get_plugin_activation_error();
		$this->assertEmpty( $error );
	}

	/**
	 * Test maybe_activate_plugin with the correct query var.
	 *
	 * @covers Block_Lab\Admin\Migration\Submenu::maybe_activate_plugin()
	 */
	public function test_maybe_activate_plugin_correct_query_var() {
		$_GET['bl_deactivate_and_activate'] = true;

		$error = $this->get_plugin_activation_error();
		$this->assertEquals( 'Sorry, you are not allowed to deactivate this plugin.', $error->getMessage() );
	}

	/**
	 * Test maybe_activate_plugin with the correct user.
	 *
	 * @covers Block_Lab\Admin\Migration\Submenu::maybe_activate_plugin()
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
	 * @covers Block_Lab\Admin\Migration\Submenu::maybe_activate_plugin()
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
		expect( 'wp_safe_redirect' )
			->once();

		$this->get_plugin_activation_error();
	}

	/**
	 * Gets the test data for test_get_coupon_code.
	 *
	 * @return array The test data.
	 */
	public function get_data_discount_code() {
		return [
			'empty_string'   => [ '', false ],
			'false_license'  => [ false, false ],
			'string_license' => [ '98765432123456789', '1fe2038a' ],
		];
	}

	/**
	 * Test get_coupon_code.
	 *
	 * @dataProvider get_data_discount_code
	 * @covers Block_Lab\Admin\Migration\Submenu::get_coupon_code()
	 *
	 * @param string      $license_key The Block Lab license key.
	 * @param string|bool $expected    The expected return value.
	 */
	public function test_get_coupon_code( $license_key, $expected ) {
		add_option( License::LICENSE_KEY_OPTION_NAME, $license_key );
		$this->assertEquals( $expected, $this->instance->get_coupon_code() );
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
