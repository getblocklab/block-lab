<?php
/**
 * Tests for helpers.php.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Tests for helpers.php.
 */
class Test_Helpers extends \WP_UnitTestCase {

	/**
	 * Test block_field.
	 *
	 * @covers ::block_field()
	 */
	public function test_block_field() {
		global $block_lab_attributes, $block_lab_config;

		$field_name                          = 'test-user';
		$class_key                           = 'className';
		$expected_class                      = 'baz-class';
		$mock_text                           = 'Example text';
		$block_lab_attributes[ $field_name ] = $mock_text;
		$block_lab_attributes[ $class_key ]  = $expected_class;

		$field_config = array( 'control' => 'text' );
		$block_config = array(
			'fields' => array(
				$field_name => $field_config
			)
		);

		$block_lab_config = new Blocks\Block();
		$block_lab_config->from_array( $block_config );

		// Because block_field() had the second argument of false, this should return the value stored in the field, not echo it.
		ob_start();
		$return_value = block_field( $field_name, false );
		$echoed       = ob_get_clean();
		$this->assertEquals( $mock_text, $return_value );
		$this->assertEmpty( $echoed );

		// Test the same scenario as above, but for 'className'.
		ob_start();
		$return_value = block_field( $class_key, false );
		$echoed       = ob_get_clean();
		$this->assertEquals( $expected_class, $return_value );
		$this->assertEmpty( $echoed );

		ob_start();
		$return_value      = block_field( $field_name, true );
		$actual_user_login = ob_get_clean();

		// Because block_field() has a second argument of true, this should echo the user login and return it.
		$this->assertEquals( $mock_text, $actual_user_login );
		$this->assertEquals( $return_value, $actual_user_login );

		ob_start();
		$return_value      = block_field( $class_key, true );
		$actual_class = ob_get_clean();

		// Test the same scenario as above, but for 'className'.
		$this->assertEquals( $expected_class, $actual_class );
		$this->assertEquals( $return_value, $actual_class );
	}

	/**
	 * Test maybe_get_sub_field_name.
	 *
	 * @covers ::maybe_get_sub_field_name()
	 */
	public function test_maybe_get_sub_field_name() {
		global $block_lab_config;

		$field_name_one_without_sub_fields = 'Example name';
		$field_name_two_without_sub_fields = 'Baz name';
		$fields_without_repeater           = array(
			$field_name_one_without_sub_fields => array( 'control' => 'image' ),
			$field_name_two_without_sub_fields => array( 'control' => 'textarea' ),
		);

		$block_config     = array( 'fields' => $fields_without_repeater );
		$block_lab_config = new Blocks\Block();
		$block_lab_config->from_array( $block_config );

		// This does not have sub-fields, so this should return false.
		$this->assertFalse( maybe_get_sub_field_name( $field_name_one_without_sub_fields ) );
		$this->assertFalse( maybe_get_sub_field_name( array( $field_name_one_without_sub_fields, $field_name_two_without_sub_fields ) ) );

		$field_name_with_sub_field = 'Has sub-field';
		$sub_field_name            = 'Example sub-field';
		$fields_with_repeater      = array(
			$field_name_one_without_sub_fields => array( 'control' => 'image' ),
			$field_name_with_sub_field         => array(
				'control'    => 'repeater',
				'sub_fields' => array(
					$sub_field_name => array( 'control' => 'taxonomy' ),
				),
			),
		);

		$block_config     = array( 'fields' => $fields_with_repeater );
		$block_lab_config = new Blocks\Block();
		$block_lab_config->from_array( $block_config );

		// Neither of the strings in the array is a field, so this should return false.
		$this->assertFalse( maybe_get_sub_field_name( array( 'non-existent-field', 'not-a-field' ) ) );

		// The first field doesn't have a sub-field, so this should return false.
		$this->assertFalse( maybe_get_sub_field_name( array( $field_name_one_without_sub_fields, $sub_field_name ) ) );

		// When passing only the sub-field name, this should return false.
		$this->assertFalse( maybe_get_sub_field_name( $sub_field_name ) );
		$this->assertFalse( maybe_get_sub_field_name( array( $sub_field_name ) ) );

		// The array values are correct, but in the wrong order.
		$this->assertFalse( maybe_get_sub_field_name( array( $sub_field_name, $field_name_with_sub_field ) ) );

		// The first 2 array values are correct, but there's a third value that's not needed.
		$this->assertFalse( maybe_get_sub_field_name( array( $field_name_with_sub_field, $sub_field_name, 'extra-field-name' ) ) );

		// The arguments are now in the correct order, so this should return the sub-field name.
		$this->assertEquals( $sub_field_name, maybe_get_sub_field_name( array( $field_name_with_sub_field, $sub_field_name ) ) );
	}
}
