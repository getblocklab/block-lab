<?php
/**
 * Test_The_Plugin
 *
 * @package AdvancedCustomBlocks
 */

namespace AdvancedCustomBlocks;

/**
 * Class Test_The_Plugin
 *
 * @package AdvancedCustomBlocks
 */
class Test_The_Plugin extends \WP_UnitTestCase {
	/**
	 * Test _advanced_custom_blocks_php_version_error().
	 *
	 * @see _advanced_custom_blocks_php_version_error()
	 */
	public function test_advanced_custom_blocks_php_version_error() {
		ob_start();
		_advanced_custom_blocks_php_version_error();
		$buffer = ob_get_clean();
		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test _advanced_custom_blocks_php_version_text().
	 *
	 * @see _advanced_custom_blocks_php_version_text()
	 */
	public function test_advanced_custom_blocks_php_version_text() {
		$this->assertContains( 'Advanced Custom Blocks plugin error:', _advanced_custom_blocks_php_version_text() );
	}
}
