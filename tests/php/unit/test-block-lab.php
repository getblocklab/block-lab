<?php
/**
 * Test_Block_Lab
 *
 * @package Block_Lab
 */

/**
 * Class Test_Block_Lab
 *
 * @package Block_Lab
 */
class Test_Block_Lab extends \WP_UnitTestCase {
	/**
	 * Test block_lab_php_version_error().
	 *
	 * @covers \block_lab_php_version_error()
	 */
	public function test_block_lab_php_version_error() {
		ob_start();
		block_lab_php_version_error();
		$this->assertContains( '<div class="error">', ob_get_clean() );
	}

	/**
	 * Test block_lab_php_version_text().
	 *
	 * @covers \block_lab_php_version_text()
	 */
	public function test_block_lab_php_version_text() {
		$this->assertContains( 'Block Lab plugin error:', block_lab_php_version_text() );
	}

	/**
	 * Test block_lab_wp_version_error().
	 *
	 * @covers \block_lab_wp_version_error()
	 */
	public function test_block_lab_wp_version_error() {
		ob_start();
		block_lab_wp_version_error();

		$this->assertEquals(
			'<div class="error"><p>Block Lab plugin error: Your version of WordPress is too old. You must be running WordPress 5.0 to use Block Lab.</p></div>',
			ob_get_clean()
		);
	}

	/**
	 * Test block_lab_wp_version_text().
	 *
	 * @covers \block_lab_wp_version_text()
	 */
	public function test_block_lab_wp_version_text() {
		$this->assertEquals(
			'Block Lab plugin error: Your version of WordPress is too old. You must be running WordPress 5.0 to use Block Lab.',
			block_lab_wp_version_text()
		);
	}

	/**
	 * Test block_lab().
	 *
	 * @covers \block_lab()
	 */
	public function test_singleton() {
		$this->assertEquals( 'Block_Lab\\Plugin', get_class( block_lab() ) );

		// Calling block_lab() twice should return the same instance.
		$this->assertEquals( block_lab(), block_lab() );
	}
}
