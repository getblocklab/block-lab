<?php
/**
 * Tests for class Control_Setting.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Control_Setting.
 */
class Test_Control_Setting extends \WP_UnitTestCase {

	/**
	 * Instance of Control_Setting.
	 *
	 * @var Controls\Control_Setting
	 */
	public $instance;

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Setting::__construct()
	 */
	public function test_construct() {
		$this->instance          = new Controls\Control_Setting( [] );
		$initial_property_values = [
			'name'     => '',
			'label'    => '',
			'type'     => '',
			'default'  => '',
			'help'     => '',
			'sanitize' => '',
			'validate' => '',
			'value'    => null,
		];

		// When an empty array is passed to the constructor, it should have the initial property values.
		foreach ( $initial_property_values as $initial_property_name => $initial_property_value ) {
			$this->assertEquals( $initial_property_value, $this->instance->$initial_property_name );
		}
		$this->assertEquals( 'Block_Lab\Blocks\Controls\Control_Setting', get_class( $this->instance ) );

		// Each of the properties below passed in the constructor should be added as properties.
		$expected_properties = [
			'name'     => 'help',
			'label'    => 'Help Text',
			'type'     => 'text',
			'default'  => '',
			'help'     => '',
			'sanitize' => 'sanitize_text_field',
			'validate' => '',
			'value'    => null,
		];

		$this->instance = new Controls\Control_Setting( $expected_properties );
		foreach ( $expected_properties as $property_key => $property_value ) {
			$this->assertEquals( $property_value, $this->instance->$property_key );
		}

		// A property should be set as long isset(), so test that empty properties are set.
		$empty_properties = [
			'default'  => 0,
			'validate' => [],
			'value'    => 0,
		];

		$this->instance = new Controls\Control_Setting( $empty_properties );
		foreach ( $empty_properties as $empty_property_key => $empty_property_value ) {
			$this->assertEquals( $empty_property_value, $this->instance->$empty_property_key );
		}

		// When non-whitelisted array keys appear, they shouldn't be added as properties.
		$incorrect_properties = [
			'wrong_property' => 'something',
			'baz_prop'       => 'example',
			'bar_property'   => 'foo bar',
		];

		$this->instance = new Controls\Control_Setting( $incorrect_properties );
		foreach ( $incorrect_properties as $incorrect_property_key => $incorrect_property_value ) {
			$this->assertFalse( property_exists( $this->instance, $incorrect_property_key ) );
		}
	}

	/**
	 * Test get_value.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Setting::get_value()
	 */
	public function get_value() {
		$default        = 'this is a default';
		$this->instance = new Controls\Control_Setting( [ 'default' => $default ] );

		// If the value is null, this should return the default.
		$this->assertEquals( $default, $this->instance->get_value() );

		$expected_value = 'Here is a value';
		$this->instance = new Controls\Control_Setting(
			[
				'value'   => $expected_value,
				'default' => $default,
			]
		);

		// If the value is anything other than null, this should return it.
		$this->assertEquals( $expected_value, $this->instance->get_value() );

		$int_expected_value = 5400;
		$this->instance     = new Controls\Control_Setting(
			[
				'value'   => $int_expected_value,
				'default' => $default,
			]
		);
		$this->assertEquals( $int_expected_value, $this->instance->get_value() );
	}
}
