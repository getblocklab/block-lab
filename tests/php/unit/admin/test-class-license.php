<?php
/**
 * Tests for class License.
 *
 * @package Block_Lab
 */

use Block_Lab\Admin;
use Brain\Monkey;

/**
 * Tests for class License.
 */
class Test_License extends \WP_UnitTestCase {

	use Testing_Helper;

	/**
	 * Instance of License.
	 *
	 * @var Admin\License
	 */
	public $instance;

	/**
	 * The transient name for the license.
	 *
	 * @var string
	 */
	const LICENSE_TRANSIENT_NAME = 'block_lab_license';

	/**
	 * The option name for the notices.
	 *
	 * @var string
	 */
	const NOTICES_OPTION_NAME = 'block_lab_notices';

	/**
	 * The option name of the Block Lab license key.
	 *
	 * @var string
	 */
	const LICENSE_KEY_OPTION_NAME = 'block_lab_license_key';

	/**
	 * The name of a HTTP filter.
	 *
	 * @var string
	 */
	const HTTP_FILTER_NAME = 'pre_http_request';

	/**
	 * The notice for when the validation request fails.
	 *
	 * @var string
	 */
	const EXPECTED_LICENSE_REQUEST_FAILED_NOTICE = '<div class="notice notice-error"><p>There was a problem activating the license, but it may not be invalid. If the problem persists, please <a href="mailto:hi@getblocklab.com?subject=There was a problem activating my Block Lab Pro license">contact support</a>.</p></div>';

	/**
	 * The notice for when the license is invalid.
	 *
	 * @var string
	 */
	const EXPECTED_LICENSE_INVALID_NOTICE = '<div class="notice notice-error"><p>There was a problem activating your Block Lab license.</p></div>';

	/**
	 * The notice for when the license validation succeeds.
	 *
	 * @var string
	 */
	const EXPECTED_LICENSE_SUCCESS_NOTICE = '<div class="notice notice-success"><p>Your Block Lab license was successfully activated!</p></div>';

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();
		$this->instance = new Admin\License();
		$this->instance->set_plugin( block_lab() );
	}

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		remove_all_filters( self::HTTP_FILTER_NAME );
		delete_option( self::NOTICES_OPTION_NAME );
		delete_option( self::LICENSE_KEY_OPTION_NAME );
		delete_transient( self::LICENSE_TRANSIENT_NAME );
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers \Block_Lab\Admin\License::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_filter( 'pre_update_option_block_lab_license_key', [ $this->instance, 'save_license_key' ] ) );
	}

	/**
	 * Test init.
	 *
	 * @covers \Block_Lab\Admin\License::init()
	 */
	public function test_init() {
		// Before init() is called, these properties should not have values.
		$this->assertEmpty( $this->instance->store_url );
		$this->assertEmpty( $this->instance->product_slug );

		$this->instance->init();

		// Now that init() was called, the properties should have values.
		$this->assertEquals( 'https://getblocklab.com', $this->instance->store_url );
		$this->assertEquals( 'block-lab-pro', $this->instance->product_slug );
	}

	/**
	 * Test save_license_key.
	 *
	 * @covers \Block_Lab\Admin\License::save_license_key()
	 */
	public function test_save_license_key() {
		$mock_invalid_license_key = '0000000';
		$returned_key             = $this->instance->save_license_key( $mock_invalid_license_key );

		// For the request failing, like with a 404, the method should return '', and the notice should be to retry or contact support.
		$this->assertEquals( '', $returned_key );
		$this->assertEquals(
			[ self::EXPECTED_LICENSE_REQUEST_FAILED_NOTICE ],
			get_option( self::NOTICES_OPTION_NAME )
		);
		delete_option( self::NOTICES_OPTION_NAME );

		// Cause the validation request to return that the license is valid.
		add_filter(
			self::HTTP_FILTER_NAME,
			function() {
				return [ 'body' => wp_json_encode( [ 'license' => 'invalid' ] ) ];
			}
		);
		$returned_key = $this->instance->save_license_key( $mock_invalid_license_key );

		// For an invalid license (not simply the request failing), the method should return '', and the notice should be an error.
		$this->assertEquals( '', $returned_key );
		$this->assertEquals(
			[ self::EXPECTED_LICENSE_INVALID_NOTICE ],
			get_option( self::NOTICES_OPTION_NAME )
		);
		delete_option( self::NOTICES_OPTION_NAME );
		remove_all_filters( self::HTTP_FILTER_NAME );

		$expected_license = [
			'license' => 'valid',
			'expires' => gmdate( 'Y-m-d', time() + DAY_IN_SECONDS ),
		];
		add_filter(
			self::HTTP_FILTER_NAME,
			function( $response ) use ( $expected_license ) {
				unset( $response );
				return [ 'body' => wp_json_encode( $expected_license ) ];
			}
		);

		$this->set_license_validity( true );
		$mock_valid_license_key = '9250342';
		$returned_key           = $this->instance->save_license_key( $mock_valid_license_key );
		$this->assertEquals( $mock_valid_license_key, $returned_key );
		$this->assertEquals(
			[ self::EXPECTED_LICENSE_SUCCESS_NOTICE ],
			get_option( self::NOTICES_OPTION_NAME )
		);
	}

	/**
	 * Test is_valid.
	 *
	 * @covers \Block_Lab\Admin\License::is_valid()
	 */
	public function test_is_valid() {
		// The transient is not set at all, so this should be false.
		$this->assertFalse( $this->instance->is_valid() );

		set_transient(
			self::LICENSE_TRANSIENT_NAME,
			[ 'license' => 'valid' ]
		);

		// The license only has part of the required data, it is missing an 'expires' value.
		$this->assertFalse( $this->instance->is_valid() );

		set_transient(
			self::LICENSE_TRANSIENT_NAME,
			[
				'license' => 'valid',
				'expires' => gmdate( 'Y-m-d', time() - DAY_IN_SECONDS ),
			]
		);

		// The license now has an 'expires' value, but it expired a day ago.
		$this->assertFalse( $this->instance->is_valid() );

		set_transient(
			self::LICENSE_TRANSIENT_NAME,
			[
				'license' => 'valid',
				'expires' => gmdate( 'Y-m-d', time() + DAY_IN_SECONDS ),
			]
		);

		// The license won't expire for one more day, so this should return true.
		$this->assertTrue( $this->instance->is_valid() );
	}

	/**
	 * Test get_license.
	 *
	 * @covers \Block_Lab\Admin\License::get_license()
	 */
	public function test_get_license() {
		$this->instance->init();
		$valid_license_transient_value   = [
			'license' => 'valid',
			'expires' => gmdate( 'Y-m-d', time() + DAY_IN_SECONDS ),
		];
		$invalid_license_transient_value = [
			'license' => 'expired',
		];

		// If the transient is set, get_license() should simply return it.
		set_transient( self::LICENSE_TRANSIENT_NAME, $valid_license_transient_value );
		$this->assertEquals( $valid_license_transient_value, $this->instance->get_license() );

		set_transient( self::LICENSE_TRANSIENT_NAME, $invalid_license_transient_value );
		$this->assertEquals( $invalid_license_transient_value, $this->instance->get_license() );

		// If there's no transient or option, this should return false.
		delete_transient( self::LICENSE_TRANSIENT_NAME );
		$this->assertFalse( $this->instance->get_license() );

		$expiration_date  = gmdate( 'Y-m-d', time() + DAY_IN_SECONDS );
		$expected_license = [
			'license' => 'valid',
			'expires' => $expiration_date,
		];

		add_filter(
			self::HTTP_FILTER_NAME,
			function( $response ) use ( $expected_license ) {
				unset( $response );
				return [ 'body' => wp_json_encode( $expected_license ) ];
			}
		);

		delete_transient( self::LICENSE_TRANSIENT_NAME );
		$example_valid_license_key = '5134315';
		add_option( self::LICENSE_KEY_OPTION_NAME, $example_valid_license_key );

		// If the license transient is empty, this should look at the option value and make a request to validate that.
		$this->assertEquals( $expected_license, $this->instance->get_license() );
	}

	/**
	 * Test activate_license.
	 *
	 * @covers \Block_Lab\Admin\License::activate_license()
	 */
	public function test_activate_license() {
		$this->instance->init();
		$license_key = '6234234';
		add_filter(
			self::HTTP_FILTER_NAME,
			function() {
				return new WP_Error();
			}
		);

		$this->instance->activate_license( $license_key );
		// If the POST request returns a wp_error(), this should store 'request_failed' in the transient.
		$this->assertEquals(
			[ 'license' => 'request_failed' ],
			get_transient( self::LICENSE_TRANSIENT_NAME )
		);

		remove_all_filters( self::HTTP_FILTER_NAME );
		$expected_license = [
			'license' => 'valid',
			'expires' => gmdate( 'Y-m-d', time() + DAY_IN_SECONDS ),
		];

		add_filter(
			self::HTTP_FILTER_NAME,
			function() use ( $expected_license ) {
				return [ 'body' => wp_json_encode( $expected_license ) ];
			}
		);
		$this->instance->activate_license( $license_key );

		// Having simulated a successful license validation with the filter above, this should activate the license.
		$this->assertEquals( $expected_license, get_transient( self::LICENSE_TRANSIENT_NAME ) );
	}

	/**
	 * Test license_success_message.
	 *
	 * @covers \Block_Lab\Admin\License::license_success_message()
	 */
	public function test_license_success_message() {
		$this->assertEquals(
			self::EXPECTED_LICENSE_SUCCESS_NOTICE,
			$this->instance->license_success_message()
		);
	}

	/**
	 * Test license_request_failed_message.
	 *
	 * @covers \Block_Lab\Admin\License::license_request_failed_message()
	 */
	public function test_license_request_failed_message() {
		$this->assertEquals(
			self::EXPECTED_LICENSE_REQUEST_FAILED_NOTICE,
			$this->instance->license_request_failed_message()
		);
	}

	/**
	 * Test license_invalid_message.
	 *
	 * @covers \Block_Lab\Admin\License::license_invalid_message()
	 */
	public function test_license_invalid_message() {
		$this->assertEquals(
			self::EXPECTED_LICENSE_INVALID_NOTICE,
			$this->instance->license_invalid_message()
		);
	}
}
