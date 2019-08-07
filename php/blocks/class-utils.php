<?php
/**
 * Helper functions for the Block_Lab plugin.
 *
 * These are publicly accessible via a magic method, like block_lab->get_block_lab_template_locations().
 * So these methods should generally be 'getter' functions, and should not affect the global state.
 *
 * @package Block_Lab
 */

namespace Block_Lab\Blocks;

use Block_Lab\Component_Abstract;

/**
 * Class Utils
 */
class Utils extends Component_Abstract {

	/**
	 * Not implemented, as this class only has utility methods.
	 */
	public function register_hooks() {}

	/**
	 * Gets whether a valid Pro license has been activated on this site.
	 *
	 * @return bool
	 */
	public function is_pro() {
		return $this->plugin->admin->license->is_valid();
	}

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
