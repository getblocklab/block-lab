<?php
/**
 * Tests for helpers.php.
 *
 * @package Block_Lab
 */

namespace Block_Lab;

/**
 * Tests for helpers.php.
 */
class Test_Helpers extends \WP_UnitTestCase {

	/**
	 * Test block_field().
	 *
	 * @covers block_field.
	 */
	public function test_block_field() {
		global $block_lab_attributes, $block_lab_config;

		$mock_name         = 'test-user';
		$mock_slug         = 'mock-user-slug';
		$mock_display_name = 'mock-display-name';
		$expected_wp_user  = $this->factory()->user->create_and_get( array(
			'user_login'   => $mock_slug,
			'display_name' => $mock_display_name,
		) );

		$user_login                                          = $expected_wp_user->get( 'user_login' );
		$block_lab_attributes[ $mock_name ]                  = $user_login;
		$mock_name                                           = 'test-user';
		$block_lab_config['fields'][ $mock_name ]['control'] = 'user';
		$actual_wp_user                                      = block_field( $mock_name, false );

		// Because block_field() had the second argument of false, this should return a WP_User.
		$this->assertEquals( 'WP_User', get_class( $actual_wp_user ) );
		$this->assertEquals( $expected_wp_user, $actual_wp_user );

		ob_start();
		block_field( $mock_name );
		$actual_user_login = ob_get_clean();

		// Because block_field() did not have a second argument, this should echo the user_login (slug).
		$this->assertEquals( $expected_wp_user->get( 'display_name' ), $actual_user_login );
	}
}
