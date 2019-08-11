<?php
/**
 * Helper functions for the Block_Lab plugin.
 *
 * These are publicly accessible via a magic method, like block_lab()->get_template_locations().
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
	public function get_template_locations( $name, $type = 'block' ) {
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
	public function get_stylesheet_locations( $name, $type = 'block' ) {
		return array(
			"blocks/{$name}/{$type}.css",
			"blocks/css/{$type}-{$name}.css",
			"blocks/{$type}-{$name}.css",
		);
	}

	/**
	 * Locates templates.
	 *
	 * Works similar to `locate_template`, but allows specifying a path outside of themes
	 * and allows to be called when STYLESHEET_PATH has not been set yet. Handy for async.
	 *
	 * @param string|array $template_names Templates to locate.
	 * @param string       $path           (Optional) Path to locate the templates first.
	 * @param bool         $single         `true` - Returns only the first found item. Like standard `locate_template`
	 *                                     `false` - Returns all found templates.
	 *
	 * @return string|array
	 */
	public function locate_template( $template_names, $path = '', $single = true ) {
		/**
		 * Filters the path where block templates are saved.
		 *
		 * Note that template names are prefixed with the blocks directory.
		 * e.g. `blocks/block-template.php`
		 * The logic below will look for the prefixed template name inside the $path.
		 *
		 * @param string       $path           The absolute path to the stylesheet directory.
		 * @param string|array $template_names Templates to locate.
		 */
		$path = apply_filters( 'block_lab_template_path', $path, $template_names );

		$stylesheet_path = get_template_directory();
		$template_path   = get_stylesheet_directory();

		$located = [];

		foreach ( (array) $template_names as $template_name ) {

			if ( ! $template_name ) {
				continue;
			}

			if ( ! empty( $path ) && file_exists( trailingslashit( $path ) . $template_name ) ) {
				$located[] = trailingslashit( $path ) . $template_name;
				if ( $single ) {
					break;
				}
			}

			if ( file_exists( trailingslashit( $template_path ) . $template_name ) ) {
				$located[] = trailingslashit( $template_path ) . $template_name;
				if ( $single ) {
					break;
				}
			}

			if ( file_exists( trailingslashit( $stylesheet_path ) . $template_name ) ) {
				$located[] = trailingslashit( $stylesheet_path ) . $template_name;
				if ( $single ) {
					break;
				}
			}

			if ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
				$located[] = ABSPATH . WPINC . '/theme-compat/' . $template_name;
				if ( $single ) {
					break;
				}
			}
		}

		// Remove duplicates and re-index array.
		$located = array_values( array_unique( $located ) );

		if ( $single ) {
			return array_shift( $located );
		}

		return $located;
	}
}
