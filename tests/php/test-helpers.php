<?php
/**
 * Tests for helpers.php.
 *
 * @package Block_Lab
 */

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

		$field_name                                           = 'test-user';
		$mock_login                                           = 'mock-user-login';
		$block_lab_attributes[ $field_name ]                  = $mock_login;
		$block_lab_config['fields'][ $field_name ]['control'] = 'user';

		// Because block_field() had the second argument of false, this should return the value stored in the field, not echo it.
		ob_start();
		$return_value = block_field( $field_name, false );
		$echoed = ob_get_clean();
		$this->assertEquals( $mock_login, $return_value );
		$this->assertEmpty( $echoed );

		ob_start();
		$return_value = block_field( $field_name, true );
		$actual_user_login = ob_get_clean();

		// Because block_field() has a second argument of true, this should echo the user login and return it.
		$this->assertEquals( $mock_login, $actual_user_login );
		$this->assertEquals( $return_value, $actual_user_login);
	}
}
