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
	 * Test advanced_custom_blocks_php_version_error().
	 *
	 * @see advanced_custom_blocks_php_version_error()
	 */
	public function testadvanced_custom_blocks_php_version_error() {
		ob_start();
		advanced_custom_blocks_php_version_error();
		$buffer = ob_get_clean();
		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test advanced_custom_blocks_php_version_text().
	 *
	 * @see advanced_custom_blocks_php_version_text()
	 */
	public function testadvanced_custom_blocks_php_version_text() {
		$this->assertContains( 'Advanced Custom Blocks plugin error:', advanced_custom_blocks_php_version_text() );
	}
}
