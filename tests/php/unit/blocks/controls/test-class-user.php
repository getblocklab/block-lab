<?php
/**
 * Tests for class User.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class User.
 */
class Test_User extends \WP_UnitTestCase {

	use Testing_Helper;

	/**
	 * Instance of the extending class User.
	 *
	 * @var Controls\User
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\User();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\User::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'user', $this->instance->name );
		$this->assertEquals( 'User', $this->instance->label );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\User::register_settings()
	 */
	public function test_register_settings() {
		$expected_settings = [
			[
				'name'     => 'location',
				'label'    => 'Field Location',
				'type'     => 'location',
				'default'  => 'editor',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_location' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'width',
				'label'    => 'Field Width',
				'type'     => 'width',
				'default'  => '100',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'help',
				'label'    => 'Help Text',
				'type'     => 'text',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			],
		];

		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
	}

	/**
	 * Test validate.
	 *
	 * @covers \Block_Lab\Blocks\Controls\User::validate()
	 */
	public function test_validate() {
		$expected_wp_user = $this->factory()->user->create_and_get();
		$valid_user_id    = $expected_wp_user->ID;
		$invalid_user_id  = 11111111;

		$this->assertFalse( $this->instance->validate( [ 'id' => $invalid_user_id ], false ) );
		$this->assertEquals( $expected_wp_user, $this->instance->validate( [ 'id' => $valid_user_id ], false ) );
		$this->assertEquals( '', $this->instance->validate( [ 'id' => $invalid_user_id ], true ) );
		$this->assertEquals( $expected_wp_user->get( 'display_name' ), $this->instance->validate( [ 'id' => $valid_user_id ], true ) );

		// If the value is a string, instead of the expected object, assert the proper values.
		$this->assertFalse( $this->instance->validate( 'Example username', false ) );
		$this->assertEquals( '', $this->instance->validate( 'Baz username', true ) );
	}
}
