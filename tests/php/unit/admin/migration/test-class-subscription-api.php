<?php
/**
 * Test_Subscription_Api
 *
 * @package Block_Lab
 */

use Block_Lab\Admin\Migration\Subscription_Api;

/**
 * Class Test_Subscription_Api
 *
 * @package Block_Lab
 */
class Test_Subscription_Api extends WP_UnitTestCase {

	use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	/**
	 * A mock expected download link returned from the endpoint.
	 *
	 * @var string
	 */
	const EXPECTED_DOWNLOAD_LINK = 'https://example.com/baz';

	/**
	 * The instance to test.
	 *
	 * @var Subscription_Api
	 */
	public $instance;

	/**
	 * Sets up each test.
	 *
	 * @inheritDoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Subscription_Api();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers Block_Lab\Admin\Migration\Subscription_Api::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_update_subscription_key' ] ) );
	}

	/**
	 * Test register_route_update_subscription_key.
	 *
	 * @covers Block_Lab\Admin\Migration\Subscription_Api::register_route_update_subscription_key()
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
	 * @covers Block_Lab\Admin\Migration\Subscription_Api::get_update_subscription_key_response()
	 */
	public function test_get_update_subscription_key_response_no_key() {
		update_option( Subscription_Api::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY, '98765' );
		$request = new WP_REST_Request( 'POST' );
		$request->set_param( 'subscriptionKey', '' );
		$response = $this->instance->get_update_subscription_key_response( $request );

		$this->assertEquals( 'empty_subscription_key', $response->get_error_code() );
		$this->assertEquals( 'Empty subscription key', $response->get_error_message() );
		$this->assertEmpty( get_transient( Subscription_Api::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK ) );
		$this->assertEmpty( get_option( Subscription_Api::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY ) );
	}

	/**
	 * Test get_update_subscription_key_response when there is a key passed.
	 *
	 * @covers Block_Lab\Admin\Migration\Subscription_Api::get_update_subscription_key_response()
	 */
	public function test_get_update_subscription_key_response_key_present() {
		$this->simulate_valid_sub_key_response();

		$request = new WP_REST_Request( 'POST' );
		$request->set_param( 'subscriptionKey', '123456' );
		$response = $this->instance->get_update_subscription_key_response( $request );

		$this->assertEquals(
			[ 'success' => true ],
			$response->get_data()
		);
		$this->assertEquals(
			self::EXPECTED_DOWNLOAD_LINK,
			get_transient( Subscription_Api::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK )
		);
	}

	/**
	 * Test get_update_subscription_key_response when the same key was already saved.
	 *
	 * @covers Block_Lab\Admin\Migration\Subscription_Api::get_update_subscription_key_response()
	 */
	public function test_get_update_subscription_key_response_key_already_saved() {
		$this->simulate_valid_sub_key_response();

		$old_value = '123456';
		update_option( Subscription_Api::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY, $old_value );

		$request = new WP_REST_Request( 'POST' );
		$request->set_param( 'subscriptionKey', $old_value );
		$response = $this->instance->get_update_subscription_key_response( $request );

		$this->assertEquals(
			[ 'success' => true ],
			$response->get_data()
		);
		$this->assertEquals(
			self::EXPECTED_DOWNLOAD_LINK,
			get_transient( Subscription_Api::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK )
		);
	}

	/**
	 * Simulates a valid subscription key.
	 */
	public function simulate_valid_sub_key_response() {
		$product_info                = new stdClass();
		$product_info->download_link = self::EXPECTED_DOWNLOAD_LINK;

		add_filter(
			'http_response',
			static function () use ( $product_info ) {
				return [
					'response' => [
						'code' => 200,
					],
					'body'     => wp_json_encode( $product_info ),
				];
			}
		);
	}
}
