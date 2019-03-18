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

	/**
	 * Test block_lab_enqueue_styles
	 *
	 * @covers ::block_lab_enqueue_styles()
	 */
	public function test_block_lab_enqueue_styles() {
		global $wp_styles;

		$block_name      = 'mock-block';
		$block_handle    = "block-lab__block-{$block_name}";
		$stylesheet_path = get_template_directory();

		if ( ! file_exists( $stylesheet_path . '/blocks/' ) ) {
			mkdir( $stylesheet_path . '/blocks/' );
			mkdir( $stylesheet_path . '/blocks/css/' );
		}

		// In order of reverse priority.
		$files = array(
			"{$stylesheet_path}/blocks/block-{$block_name}.css",
			"{$stylesheet_path}/blocks/css/block-{$block_name}.css",
			"{$stylesheet_path}/blocks/preview-{$block_name}.css",
			"{$stylesheet_path}/blocks/css/preview-{$block_name}.css",
		);

		// Remove previous template files so that we can correctly check load order.
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				unlink( $file );
			}
		}

		// Check that the correct stylesheet is enqueued.
		foreach ( $files as $key => $file ) {
			file_put_contents( $file, '' ); // @codingStandardsIgnoreLine
			$file_url = str_replace( untrailingslashit( ABSPATH ), '', $file );

			block_lab_enqueue_styles( $block_name, array( 'preview', 'block' ) );

			$this->assertContains( $block_handle, $wp_styles->queue );
			$this->assertArrayHasKey( $block_handle, $wp_styles->registered );
			$this->assertSame( $wp_styles->registered[ $block_handle ]->src, $file_url, "Trying to enqueue file #{$key} ({$file_url})." );

			wp_deregister_style( $block_handle );
			wp_dequeue_style( $block_handle );
		}

		// Check that nothing is enqueued if the file doesn't exist.
		block_lab_enqueue_styles( 'does-not-exist', 'block' );
		$this->assertNotContains( $block_handle, $wp_styles->queue );
		$this->assertArrayNotHasKey( $block_handle, $wp_styles->registered );
	}
}
