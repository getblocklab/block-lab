<?php
/**
 * Test_Api
 *
 * @package Block_Lab
 */

use Block_Lab\Admin\Migration\Api;
use Block_Lab\Admin\Migration\Subscription_Api;
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
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_install_gcb' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_migrate_post_content' ] ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->instance, 'register_route_migrate_post_type' ] ) );
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
	 * Test get_download_link when it should be for GCB Pro.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_download_link()
	 */
	public function test_get_download_link_gcb_pro() {
		$download_link = 'https://example.com/bar';
		set_transient( Subscription_Api::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK, $download_link );
		$this->assertEquals( $download_link, $this->instance->get_download_link() );
	}

	/**
	 * Test get_download_link when it should be for GCB free.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_download_link()
	 */
	public function test_get_download_link_gcb_free() {
		$download_link      = 'https://example.com/baz';
		$api                = new stdClass();
		$api->download_link = $download_link;

		add_filter(
			'plugins_api_result',
			static function() use ( $api ) {
				return $api;
			}
		);

		$this->assertEquals( $download_link, $this->instance->get_download_link() );
	}

	/**
	 * Test get_download_link when there is no download_link.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_download_link()
	 */
	public function test_get_download_link_no_download_link() {
		add_filter(
			'plugins_api_result',
			static function() {
				return new stdClass();
			}
		);

		$actual = $this->instance->get_download_link();
		$this->assertEquals(
			'no_download_link',
			$actual->get_error_code()
		);
		$this->assertEquals(
			'There was no download_link in the API',
			$actual->get_error_message()
		);
	}

	/**
	 * Test get_download_link when it returns an error.
	 *
	 * @covers Block_Lab\Admin\Migration\Api::get_download_link()
	 */
	public function test_get_download_link_error() {
		$error_code = 'example_error';
		$error      = new WP_Error( $error_code );

		add_filter(
			'plugins_api_result',
			static function() use ( $error ) {
				return $error;
			}
		);

		$actual = $this->instance->get_download_link();
		$this->assertEquals( $error_code, $actual->get_error_code() );
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
