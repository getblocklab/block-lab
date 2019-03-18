<?php
/**
 * Test_The_Plugin
 *
 * @package Block_Lab
 */

namespace Block_Lab;

/**
 * Class Test_The_Plugin
 *
 * @package Block_Lab
 */
class Test_The_Plugin extends \WP_UnitTestCase {
	/**
	 * Test block_lab_php_version_error().
	 *
	 * @covers ::block_lab_php_version_error()
	 */
	public function testblock_lab_php_version_error() {
		ob_start();
		block_lab_php_version_error();
		$buffer = ob_get_clean();
		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test block_lab_php_version_text().
	 *
	 * @covers ::block_lab_php_version_text()
	 */
	public function testblock_lab_php_version_text() {
		$this->assertContains( 'Block Lab plugin error:', block_lab_php_version_text() );
	}
}
