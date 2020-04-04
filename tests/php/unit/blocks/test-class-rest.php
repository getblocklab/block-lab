<?php
/**
 * Tests for class Rest.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Rest;

/**
 * Tests for class Rest.
 */
class Test_Rest extends \WP_UnitTestCase {

	/**
	 * The instance to test.
	 *
	 * @var Rest
	 */
	public $instance;

	/**
	 * The name of the mock block.
	 *
	 * @var string
	 */
	public $mock_block_name = 'block-lab/hero';

	/**
	 * The REST API route for blocks.
	 *
	 * @var string
	 */
	public $rest_api_route = '/wp/v2/block-renderer/';

	/**
	 * A mock REST API handler.
	 *
	 * @var array
	 */
	public $mock_handler = [
		0 => [
			'methods'  => [ 'GET' ],
			'callback' => [ 'example_callback' ],
		],
	];

	/**
	 * Set up each test.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Rest();
		$this->register_mock_block();
	}

	/**
	 * Tears down after each test.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		unregister_block_type( $this->mock_block_name );
		parent::tearDown();
	}

	/**
	 * Registers the mock block.
	 *
	 * Mainly taken from REST_Block_Renderer_Controller_Test::register_test_block().
	 */
	public function register_mock_block() {
		register_block_type(
			$this->mock_block_name,
			[
				'attributes'      => [
					'example_string' => [
						'type'    => 'string',
						'default' => 'some_default',
					],
					'example_int'    => [
						'type' => 'integer',
					],
				],
				'render_callback' => function( $attributes ) {
					return wp_json_encode( $attributes );
				},
			]
		);
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers \Block_Lab\Blocks\Rest::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$filtered_endpoints = apply_filters( 'rest_endpoints', rest_get_server()->get_routes() );
		$block_route        = $this->rest_api_route . '(?P<name>' . $this->mock_block_name . ')';

		$this->assertEquals( 10, has_action( 'rest_endpoints', [ $this->instance, 'filter_block_endpoints' ] ) );
		$this->assertEquals( [ 'GET', 'POST' ], $filtered_endpoints[ $block_route ][0]['methods'] );
		$this->assertEquals( [ $this->instance, 'get_item' ], $filtered_endpoints[ $block_route ][0]['callback'] );
	}

	/**
	 * Test filter_block_endpoints, when there is a non-Block Lab block.
	 *
	 * @covers \Block_Lab\Blocks\Rest::filter_block_endpoints()
	 */
	public function test_filter_block_endpoints_non_block_lab_block() {
		$endpoints = [
			$this->rest_api_route . '/(?P<name>baz-plugin/example-block-name)'       => $this->mock_handler,
			$this->rest_api_route . '/(?P<name>another-plugin/here-is-a-block-name)' => [],
		];

		$this->assertEquals(
			$endpoints,
			$this->instance->filter_block_endpoints( $endpoints )
		);
	}

	/**
	 * Test filter_block_endpoints, when there is a Block Lab block.
	 *
	 * @covers \Block_Lab\Blocks\Rest::filter_block_endpoints()
	 */
	public function test_filter_block_endpoints_block_lab_block() {
		$this->assertEquals(
			[
				$this->rest_api_route . '(?P<name>block-lab/main-hero)' => [
					0 => [
						'methods'  => [ 'GET', 'POST' ],
						'callback' => [ $this->instance, 'get_item' ],
					],
				],
			],
			$this->instance->filter_block_endpoints(
				[ $this->rest_api_route . '(?P<name>block-lab/main-hero)' => $this->mock_handler ]
			)
		);
	}

	/**
	 * Gets the test data for test_get_item_post_request().
	 *
	 * @return array The test data.
	 */
	public function get_data_test_get_item() {
		return [
			'get_request'  => [ 'GET' ],
			'post_request' => [ 'POST' ],
		];
	}

	/**
	 * Test get_item.
	 *
	 * @dataProvider get_data_test_get_item
	 * @covers \Block_Lab\Blocks\Rest::get_item()
	 *
	 * @param string $request_type The type of request, like 'GET'.
	 */
	public function test_get_item( $request_type ) {
		wp_set_current_user( $this->factory()->user->create( [ 'role' => 'editor' ] ) );

		$string_attribute = 'Lorem ipsum dolor';
		$int_attribute    = 200;
		$attributes       = [
			'example_string' => $string_attribute,
			'example_int'    => $int_attribute,
		];

		$request = new WP_REST_Request( $request_type, $this->rest_api_route . $this->mock_block_name );
		$request->set_param( 'context', 'edit' );

		if ( 'GET' === $request_type ) {
			$request->set_param( 'attributes', $attributes );
		} elseif ( 'POST' === $request_type ) {
			$request->set_body( wp_json_encode( $attributes ) );
		}

		$response = rest_get_server()->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$actual = $response->get_data()['rendered'];
		$this->assertContains( $string_attribute, $actual );
		$this->assertContains( strval( $int_attribute ), $actual );
	}
}
