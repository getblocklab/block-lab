<?php
/**
 * Test_Api
 *
 * @package Block_Lab
 */

use Block_Lab\Admin\Migration\Api;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;
use function Brain\Monkey\Functions\expect;

/**
 * Class Test_Api
 *
 * @package Block_Lab
 */
class Test_Api extends WP_UnitTestCase {

	use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	/**
	 * The instance to test.
	 *
	 * @var Api
	 */
	public $instance;

	/**
	 * Sets up each test.
	 *
	 * @inheritDoc
	 */
	public function setUp() {
		parent::setUp();
		setUp();
		$this->instance = new Api();
	}

	/**
	 * Tears down after each test.
	 *
	 * @inheritDoc
	 */
	public function tearDown() {
		tearDown();
		parent::tearDown();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_update_subscription_key' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_install_gcb' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_migrate_post_content' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_migrate_post_type' ] ) );
	}

	/**
	 * Test register_route_update_subscription_key.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::register_route_update_subscription_key()
	 */
	public function test_register_route_update_subscription_key() {
		do_action( 'rest_api_init' );
		$this->instance->register_route_update_subscription_key();
		$routes = rest_get_server()->get_routes();

		$this->assertArrayHasKey( '/block-lab/update-subscription-key', $routes );
	}

	/**
	 * Test get_update_subscription_key_response when no key is passed.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_update_subscription_key_response()
	 */
	public function test_get_update_subscription_key_response_no_key() {
		$response = $this->instance->get_update_subscription_key_response( [] );
		$this->assertEquals( 'no_subscription_key', $response->get_error_code() );
		$this->assertEquals( 'No subscription key present', $response->get_error_message() );
	}

	/**
	 * Test get_update_subscription_key_response when there is a key passed.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_update_subscription_key_response()
	 */
	public function test_get_update_subscription_key_response_key_present() {
		$response = $this->instance->get_update_subscription_key_response( [ 'subscriptionKey' => '123456' ] );
		$this->assertEquals( [ 'success' => true ], $response->get_data() );
	}

	/**
	 * Test get_update_subscription_key_response when the same key was already saved.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_update_subscription_key_response()
	 */
	public function test_get_update_subscription_key_response_key_already_saved() {
		$new_value = '123456';
		update_option( Api::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY, $new_value );
		$response = $this->instance->get_update_subscription_key_response( [ 'subscriptionKey' => $new_value ] );
		$this->assertEquals( [ 'success' => true ], $response->get_data() );
	}

	/**
	 * Test register_route_install_gcb.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::register_route_install_gcb()
	 */
	public function test_register_route_install_gcb() {
		do_action( 'rest_api_init' );
		$this->instance->register_route_install_gcb();
		$routes = rest_get_server()->get_routes();

		$this->assertArrayHasKey( '/block-lab/install-activate-gcb', $routes );
	}

	/**
	 * Test get_install_gcb_response when the plugin is already installed.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_install_gcb_response()
	 */
	public function test_get_install_gcb_response_plugin_already_installed() {
		if ( is_multisite() ) {
			expect( 'is_network_only_plugin' )
				->andReturn( false );
		}

		$full_plugin_file = 'genesis-custom-blocks/genesis-custom-blocks.php';
		expect( 'get_plugins' )
			->andReturn( [ $full_plugin_file => [] ] );
		$response = $this->instance->get_install_gcb_response( [] );

		$this->assertEquals(
			'Plugin file does not exist.',
			$response->get_error_message()
		);
	}

	/**
	 * Test get_install_gcb_response when the plugin is not found.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_install_gcb_response()
	 */
	public function test_get_install_gcb_response_plugin_not_found() {
		expect( 'get_plugins' )
			->andReturn( [] );
		$response = $this->instance->get_install_gcb_response( [] );

		$this->assertEquals(
			'Plugin not found.',
			$response->get_error_message()
		);
	}

	/**
	 * Test register_route_migrate_post_content.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::register_route_migrate_post_content()
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
	 * @covers Block_Lab\Admin\Migration\Api::get_migrate_post_content_response()
	 */
	public function test_get_migrate_post_content_response() {
		$this->assertEquals( 'WP_REST_Response', get_class( $this->instance->get_migrate_post_content_response() ) );
	}

	/**
	 * Test register_route_migrate_post_type.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::register_route_migrate_post_type()
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
	 * @covers Block_Lab\Admin\Migration\Api::get_migrate_post_type_response()
	 */
	public function test_get_migrate_post_type_response() {
		$this->assertEquals( 'WP_REST_Response', get_class( $this->instance->get_migrate_post_type_response() ) );
	}
}
