<?php
/**
 * Test_Api
 *
 * @package Block_Lab
 */

use Block_Lab\Admin\Migration\Api;

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
		$this->instance = new Api();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_migrate_post_content' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_migrate_post_type' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_update_subscription_key' ] ) );
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
	 * Test get_update_subscription_key_response.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_update_subscription_key_response()
	 */
	public function test_get_update_subscription_key_response() {
		$this->assertEquals( 'WP_REST_Response', get_class( $this->instance->get_update_subscription_key_response( [] ) ) );
	}
}
