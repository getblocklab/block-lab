<?php
/**
 * Tests for class Admin.
 *
 * @package Block_Lab
 */

use Block_Lab\Admin;

/**
 * Tests for class Admin.
 */
class Test_Admin extends \WP_UnitTestCase {

	use Control_Helper;

	/**
	 * Instance of Admin.
	 *
	 * @var Controls\Admin
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Admin\Admin();
	}

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		unset( $GLOBALS['current_screen'] );
		parent::tearDown();
	}

	/**
	 * Test init.
	 *
	 * @covers \Block_Lab\Admin\Admin::init()
	 */
	public function test_init() {
		$this->set_license_validity( false );
		$this->instance->init();
		$settings_class = 'Block_Lab\Admin\Settings';
		$license_class  = 'Block_Lab\Admin\License';
		$this->assertEquals( $settings_class, get_class( $this->instance->settings ) );
		$this->assertEquals( $license_class, get_class( $this->instance->license ) );

		$block_lab_reflection = new ReflectionObject( block_lab() );
		$components           = $block_lab_reflection->getProperty( 'components' );
		$components->setAccessible( true );
		$components_value = $components->getValue( block_lab() );

		// The settings should have been added to the plugin components.
		$this->assertEquals( $this->instance->settings->slug, $components_value[ $settings_class ]->slug );
		$this->assertArrayHasKey( $settings_class, $components_value );
		$this->assertArrayHasKey( $license_class, $components_value );

		// Because the Pro license isn't active, there should be an Upgrade class instantiated.
		$upgrade_class = 'Block_Lab\Admin\Upgrade';
		$this->assertEquals( $upgrade_class, get_class( $this->instance->upgrade ) );
		$this->assertArrayHasKey( $upgrade_class, $components_value );
		$this->assertFalse( $this->did_settings_redirect_occur() );

		// With an active Pro license, this should redirect from the Pro page to the settings page.
		$this->set_license_validity( true );
		set_current_screen( 'edit.php' );
		$_GET['page'] = 'block-lab-pro';
		$this->assertTrue( $this->did_settings_redirect_occur() );
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers Block_Lab\Admin\Admin::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $this->instance, 'enqueue_scripts' ) ) );
	}

	/**
	 * Test enqueue_scripts.
	 *
	 * @covers \Block_Lab\Admin\Admin::enqueue_scripts()
	 */
	public function test_enqueue_scripts() {
		block_lab()->register_component( $this->instance );
		$this->instance->set_plugin( block_lab() );
		$this->instance->enqueue_scripts();
		$styles     = wp_styles();
		$handle     = 'block-lab';
		$stylesheet = $styles->registered[ $handle ];

		$this->assertEquals( $handle, $stylesheet->handle );
		$this->assertContains( 'css/admin.css', $stylesheet->src );
		$this->assertEquals( array(), $stylesheet->deps );
		$this->assertEquals( array(), $stylesheet->extra );
		$this->assertTrue( in_array( $handle, $styles->queue, true ) );
	}

	/**
	 * Test maybe_settings_redirect.
	 *
	 * @covers Block_Lab\Admin\Admin::maybe_settings_redirect()
	 */
	public function test_maybe_settings_redirect() {
		// None of the conditional is satisfied, so this should not redirect.
		$this->assertFalse( $this->did_settings_redirect_occur() );

		// Only the first part of the conditional is satisfied, so this should still not redirect.
		set_current_screen( 'edit.php' );
		$this->assertFalse( $this->did_settings_redirect_occur() );

		// The 'page' is set, but incorrect.
		$_GET['page'] = 'incorrect-page';
		$this->assertFalse( $this->did_settings_redirect_occur() );

		// Now that the 'page' is correct, the entire conditional should be true, and this should redirect.
		$_GET['page'] = 'block-lab-pro';
		$this->assertTrue( $this->did_settings_redirect_occur() );

		// Mainly copied from Weston Ruter in the AMP Plugin for WordPress.
		add_filter(
			'wp_redirect',
			function( $url, $status ) {
				throw new Exception( $url, $status );
			},
			10,
			2
		);

		try {
			$this->instance->maybe_settings_redirect();
		} catch ( Exception $e ) {
			$exception = $e;
		}

		// Assert that the response was a redirect (302), and that it redirected to the right URL.
		$this->assertEquals( 302, $exception->getCode() );
		$this->assertContains(
			add_query_arg(
				array(
					'post_type' => 'block_lab',
					'page'      => 'block-lab-settings',
					'tab'       => 'license',
				),
				admin_url( 'edit.php' )
			),
			$exception->getMessage()
		);
	}

	/**
	 * Invokes maybe_settings_redirect(), and gets whether the redirect occurred.
	 *
	 * @return boolean Whether it caused a redirect.
	 */
	public function did_settings_redirect_occur() {
		try {
			$this->instance->maybe_settings_redirect();
		} catch ( Exception $e ) {
			$exception = $e;
		}

		return isset( $exception );
	}
}
