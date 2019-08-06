<?php
/**
 * Helper functions for the Block_Lab plugin.
 *
 * These are publicly accessible in components via ->plugin->utils,
 * so they should generally be 'getter' functions, and not affect the global state.
 *
 * @package Block_Lab
 */

namespace Block_Lab\Blocks;

/**
 * Class Utils
 */
class Utils {

	/**
	 * Gets an array of possible template locations.
	 *
	 * @param string $name The name of the block (slug as defined in UI).
	 * @param string $type The type of template to load. Typically block or preview.
	 *
	 * @return array
	 */
	public function get_block_lab_template_locations( $name, $type = 'block' ) {
		return array(
			"blocks/{$name}/{$type}.php",
			"blocks/{$type}-{$name}.php",
			"blocks/{$type}.php",
		);
	}

	/**
	 * Gets an array of possible stylesheet locations.
	 *
	 * @param string $name The name of the block (slug as defined in UI).
	 * @param string $type The type of template to load. Typically block or preview.
	 *
	 * @return array
	 */
	public function get_block_lab_stylesheet_locations( $name, $type = 'block' ) {
		return array(
			"blocks/{$name}/{$type}.css",
			"blocks/css/{$type}-{$name}.css",
			"blocks/{$type}-{$name}.css",
		);
	}
}
