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
	 * The option name for the notices.
	 *
	 * @var string
	 */
	const NOTICES_OPTION_NAME = 'block_lab_notices';

	/**
	 * The transient name for the license.
	 *
	 * @var string
	 */
	const LICENSE_TRANSIENT_NAME = 'block_lab_license';

	/**
	 * The option name of the Block Lab license key.
	 *
	 * @var string
	 */
	const LICENSE_KEY_OPTION_NAME = 'block_lab_license_key';

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
		remove_all_filters( 'http_response' );
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
		$this->assertEquals( 10, has_filter( 'pre_update_option_block_lab_license_key', array( $this->instance, 'save_license_key' ) ) );
	}

	/**
	 * Test init.
	 *
	 * @covers \Block_Lab\Admin\License::init()
	 */
	public function test_init() {
		$this->store_url    = 'https://getblocklab.com';
		$this->product_slug = 'block-lab-pro';

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
		$this->set_license_validity( false );
		$mock_invalid_license_key = '0000000';
		$returned_key             = $this->instance->save_license_key( $mock_invalid_license_key );

		// For an invalid license, the method should return '', and the notice should be an error.
		$this->assertEquals( '', $returned_key );
		$this->assertEquals(
			array( '<div class="notice notice-error"><p>There was a problem activating your Block Lab license.</p></div>' ),
			get_option( self::NOTICES_OPTION_NAME )
		);
		delete_option( self::NOTICES_OPTION_NAME );

		$this->set_license_validity( true );
		$mock_valid_license_key = '9250342';
		$returned_key           = $this->instance->save_license_key( $mock_valid_license_key );
		$this->assertEquals( $mock_valid_license_key, $returned_key );
		$this->assertEquals(
			array( '<div class="notice notice-success"><p>Your Block Lab license was successfully activated!</p></div>' ),
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
			array( 'license' => 'valid' )
		);

		// The license only has part of the required data, it is missing an 'expires' value.
		$this->assertFalse( $this->instance->is_valid() );

		set_transient(
			self::LICENSE_TRANSIENT_NAME,
			array(
				'license' => 'valid',
				'expires' => date( 'Y-m-d', time() - DAY_IN_SECONDS ),
			)
		);

		// The license now has an 'expires' value, but it expired a day ago.
		$this->assertFalse( $this->instance->is_valid() );

		set_transient(
			self::LICENSE_TRANSIENT_NAME,
			array(
				'license' => 'valid',
				'expires' => date( 'Y-m-d', time() + DAY_IN_SECONDS ),
			)
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
		$valid_license_transient_value   = array(
			'license' => 'valid',
			'expires' => date( 'Y-m-d', time() + DAY_IN_SECONDS )
		);
		$invalid_license_transient_value = array(
			'license' => 'expired',
		);

		// If the transient is set, get_license() should simply return it.
		set_transient( self::LICENSE_TRANSIENT_NAME, $valid_license_transient_value );
		$this->assertEquals( $valid_license_transient_value, $this->instance->get_license() );

		set_transient( self::LICENSE_TRANSIENT_NAME, $invalid_license_transient_value );
		$this->assertEquals( $invalid_license_transient_value, $this->instance->get_license() );

		// If there's no transient or option, this should return false.
		delete_transient( self::LICENSE_TRANSIENT_NAME );
		$this->assertFalse( $this->instance->get_license() );

		$expiration_date  = date( 'Y-m-d', time() + DAY_IN_SECONDS );
		$expected_license = array(
			'license' => 'valid',
			'expires' => $expiration_date,
		);

		add_filter( 'http_response', function( $response ) use ( $expected_license ) {
			$response['body'] = wp_json_encode( $expected_license );
			return $response;
		} );

		delete_transient( self::LICENSE_TRANSIENT_NAME );
		$example_valid_license_key = '5134315';
		add_option( self::LICENSE_KEY_OPTION_NAME, $example_valid_license_key );
		$actual_license = $this->instance->get_license();

		// If the license transient is empty, this should look at the option value and make a request to validate that.
		$this->assertEquals( $expected_license, $actual_license );
	}

	/**
	 * Test activate_license.
	 *
	 * @covers \Block_Lab\Admin\License::activate_license()
	 */
	public function test_activate_license() {
		$this->instance->init();
		$license_key      = '6234234';
		$http_filter_name = 'http_response';
		add_filter( $http_filter_name, function( $response ) {
			unset( $response );
			return new WP_Error();
		} );

		// If the POST request returns a wp_error(), this should return false.
		$this->assertFalse( $this->instance->activate_license( $license_key ) );

		remove_all_filters( $http_filter_name );

		$expected_license = array(
			'license' => 'valid',
			'expires' => date( 'Y-m-d', time() + DAY_IN_SECONDS ),
		);
		add_filter( $http_filter_name, function( $response ) use ( $expected_license ) {
			$response['body'] = wp_json_encode( $expected_license );
			return $response;
		} );

		// Having simulated a successful license validation with the filter above, this should activate the license.
		$this->instance->activate_license( $license_key );
		$this->assertEquals( $expected_license, get_transient( self::LICENSE_TRANSIENT_NAME ) );
	}

	/**
	 * Test license_success_message.
	 *
	 * @covers \Block_Lab\Admin\License::license_success_message()
	 */
	public function test_license_success_message() {
		$this->assertEquals(
			'<div class="notice notice-success"><p>Your Block Lab license was successfully activated!</p></div>',
			$this->instance->license_success_message()
		);
	}

	/**
	 * Test license_error_message.
	 *
	 * @covers \Block_Lab\Admin\License::license_error_message()
	 */
	public function test_license_error_message() {
		$this->assertEquals(
			'<div class="notice notice-error"><p>There was a problem activating your Block Lab license.</p></div>',
			$this->instance->license_error_message()
		);
	}
}
