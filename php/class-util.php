<?php
/**
 * Helper functions for the Block_Lab plugin.
 *
 * These are publicly accessible via a magic method, like block_lab()->get_template_locations().
 * So these methods should generally be 'getter' functions, and should not affect the global state.
 *
 * @package Block_Lab
 */

namespace Block_Lab;

use Block_Lab\Blocks;

/**
 * Class Util
 */
class Util extends Component_Abstract {

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
	 * Get the loop handler.
	 *
	 * @return Blocks\Loop
	 */
	public function loop() {
		static $instance;

		if ( null === $instance ) {
			$instance = new Blocks\Loop();
			return $instance;
		}

		return $instance;
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
		return [
			"blocks/{$name}/{$type}.php",
			"blocks/{$type}-{$name}.php",
			"blocks/{$type}.php",
		];
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
		return [
			"blocks/{$name}/{$type}.css",
			"blocks/css/{$type}-{$name}.css",
			"blocks/{$type}-{$name}.css",
		];
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

	/**
	 * Provides a list of all available block icons.
	 *
	 * To include additional icons in this list, use the block_lab_icons filter, and add a new svg string to the array,
	 * using a unique key. For example:
	 *
	 * $icons['foo'] = '<svg>…</svg>';
	 *
	 * @return array
	 */
	public function get_icons() {
		// This is on the local filesystem, so file_get_contents() is ok to use here.
		$json_file = block_lab()->get_assets_path( 'icons.json' );
		$json      = file_get_contents( $json_file ); // @codingStandardsIgnoreLine
		$icons     = json_decode( $json, true );

		/**
		 * The available block icons.
		 *
		 * @param array $icons The available icons.
		 */
		return apply_filters( 'block_lab_icons', $icons );
	}

	/**
	 * Provides a list of allowed tags to be used by an <svg>.
	 *
	 * @return array
	 */
	public function allowed_svg_tags() {
		$allowed_tags = [
			'svg'    => [
				'xmlns'   => true,
				'width'   => true,
				'height'  => true,
				'viewbox' => true,
			],
			'g'      => [ 'fill' => true ],
			'title'  => [ 'title' => true ],
			'path'   => [
				'd'       => true,
				'fill'    => true,
				'opacity' => true,
			],
			'circle' => [
				'cx'   => true,
				'cy'   => true,
				'r'    => true,
				'fill' => true,
			],
		];

		/**
		 * The tags that an <svg> allows.
		 *
		 * @param array $allowed_tags The allowed tags.
		 */
		return apply_filters( 'block_lab_allowed_svg_tags', $allowed_tags );
	}

	/**
	 * Gets the slug of the post type that stores the blocks.
	 *
	 * @return string The slug.
	 */
	public function get_post_type_slug() {
		return $this->plugin->post_type_slug;
	}

	/**
	 * Get a relative URL from a path.
	 *
	 * @param string $path The absolute path to a file.
	 *
	 * @return string
	 */
	public function get_url_from_path( $path ) {
		$abspath = ABSPATH;

		// Workaround for weird hosting situations.
		if ( trailingslashit( ABSPATH ) . 'wp-content' !== WP_CONTENT_DIR && isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
			$abspath = sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) );
		}

		$stylesheet_url = str_replace( untrailingslashit( $abspath ), '', $path );

		return $stylesheet_url;
	}
}
