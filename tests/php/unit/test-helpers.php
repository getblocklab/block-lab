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
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		remove_all_filters( 'block_lab_default_fields' );
		$GLOBALS['block_lab_attributes'] = array();
		$GLOBALS['block_lab_config']     = array();
		parent::tearDown();
	}

	/**
	 * Test block_field.
	 *
	 * @covers ::block_field()
	 */
	public function test_block_field() {
		$field_name                                     = 'test-user';
		$class_key                                      = 'className';
		$expected_class                                 = 'baz-class';
		$mock_text                                      = 'Example text';
		$GLOBALS['block_lab_attributes'][ $field_name ] = $mock_text;
		$GLOBALS['block_lab_attributes'][ $class_key ]  = $expected_class;

		$field_config = array( 'control' => 'text' );
		$block_config = array(
			'fields' => array(
				$field_name => $field_config,
			),
		);

		$GLOBALS['block_lab_config'] = new Blocks\Block();
		$GLOBALS['block_lab_config']->from_array( $block_config );

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
		$return_value = block_field( $class_key, true );
		$actual_class = ob_get_clean();

		// Test the same scenario as above, but for 'className'.
		$this->assertEquals( $expected_class, $actual_class );
		$this->assertEquals( $return_value, $actual_class );

		$additional_field_name           = 'example_additional_field';
		$additional_field_value          = 'Here is some text';
		$GLOBALS['block_lab_attributes'] = array(
			$additional_field_name => $additional_field_value,
		);

		ob_start();
		$return_value = block_field( $additional_field_name, true );
		$echoed_value = ob_get_clean();

		// When a field isn't in the $block_lab_config, it should not be echoed or returned.
		$this->assertEmpty( $return_value );
		$this->assertEmpty( $echoed_value );

		$default_fields_filter = 'block_lab_default_fields';
		add_filter(
			$default_fields_filter,
			function( $default_fields ) use ( $additional_field_name ) {
				$default_fields[] = $additional_field_name;
			}
		);

		ob_start();
		$return_value = block_field( $additional_field_name, true );
		$echoed_value = ob_get_clean();

		// In case the filter accidentally doesn't return anything, there should still not be a fatal error, there should just be no output.
		$this->assertEmpty( $return_value );
		$this->assertEmpty( $echoed_value );
		remove_all_filters( $default_fields_filter );

		add_filter(
			$default_fields_filter,
			function( $default_fields ) use ( $additional_field_name ) {
				$default_fields[] = $additional_field_name;
				return $default_fields;
			}
		);

		ob_start();
		$return_value = block_field( $additional_field_name, true );
		$echoed_value = ob_get_clean();

		// Now that the filter returns true, the field should be echoed, even though it's not in $block_lab_config.
		$this->assertEquals( $additional_field_value, $return_value );
		$this->assertEquals( $additional_field_value, $echoed_value );
	}
}
