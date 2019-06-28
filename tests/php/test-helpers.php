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
}
