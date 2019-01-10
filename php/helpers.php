<?php
/**
 * Helper functions.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Echos out the value of a block field.
 *
 * @param string $name The name of the field as created in the UI.
 * @param bool   $echo Whether to echo and return the field, or just return the field.
 *
 * @return mixed|null
 */
function block_field( $name, $echo = true ) {
	/*
	 * Defined in Block_Lab\Blocks\Loader->render_block_template().
	 *
	 * @var array
	 */
	global $block_lab_attributes, $block_lab_config;

	if (
		! isset( $block_lab_attributes ) ||
		! is_array( $block_lab_attributes )
	) {
		return null;
	}

	$value = false; // This is a good default, it allows us to pick up on unchecked checkboxes.
	if ( array_key_exists( $name, $block_lab_attributes ) ) {
		$value = $block_lab_attributes[ $name ];
	}

	// Cast block value as correct type.
	if ( isset( $block_lab_config['fields'][ $name ]['type'] ) ) {
		switch ( $block_lab_config['fields'][ $name ]['type'] ) {
			case 'string':
				$value = strval( $value );
				break;
			case 'boolean':
				if ( 1 === $value ) {
					$value = true;
				}
				break;
			case 'integer':
				$value = intval( $value );
				break;
			case 'array':
				$value = (array) $value;
				break;
		}
	}

	if ( $echo ) {
		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}

		if ( true === $value ) {
			$value = __( 'Yes', 'block-lab' );
		}

		if ( false === $value ) {
			$value = __( 'No', 'block-lab' );
		}

		/**
		 * Escaping this value may cause it to break in some use cases.
		 * If this happens, retrieve the field's value using block_value(),
		 * and then output the field with a more suitable escaping function.
		 */
		echo wp_kses_post( $value );
	}

	return $value;
}

/**
 * Convenience method to return the value of a block field.
 *
 * @param string $name The name of the field as created in the UI.
 *
 * @uses block_field()
 *
 * @return mixed|null
 */
function block_value( $name ) {
	return block_field( $name, false );
}

/**
 * Convenience method to return the block configuration.
 *
 * @return array
 */
function block_config() {
	global $block_lab_config;
	return $block_lab_config;
}

/**
 * Convenience method to return a field's configuration.
 *
 * @param string $name The name of the field as created in the UI.
 *
 * @return array|null
 */
function block_field_config( $name ) {
	global $block_lab_config;
	if ( ! isset( $block_lab_config['fields'][ $name ] ) ) {
		return null;
	}
	return $block_lab_config['fields'][ $name ];
}

/**
 * Loads a template part to render the block.
 *
 * @param string $name The name of the block (slug as defined in UI).
 * @param string $type The type of template to load. Only 'block' supported at this stage.
 */
function block_lab_template_part( $name, $type = 'block' ) {
	// Loading async it might not come from a query, this breaks load_template().
	global $wp_query;

	// So lets fix it.
	if ( empty( $wp_query ) ) {
		$wp_query = new WP_Query(); // Override okay.
	}

	$types         = (array) $type;
	$located       = '';
	$template_file = '';

	foreach ( $types as $type ) {

		if ( ! empty( $located ) ) {
			continue;
		}

		$template_file = "blocks/{$type}-{$name}.php";
		$generic_file  = "blocks/{$type}.php";
		$templates     = [
			$generic_file,
			$template_file,
		];

		$located = block_lab_locate_template( $templates );
	}

	if ( ! empty( $located ) ) {
		$theme_template = apply_filters( 'block_lab_override_theme_template', $located );

		// This is not a load once template, so require_once is false.
		load_template( $theme_template, false );
	} else {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning">%s</div>',
			wp_kses_post(
				// Translators: Placeholder is a file path.
				sprintf( __( 'Template file %s not found.' ), '<code>' . esc_html( $template_file ) . '</code>' )
			)
		);
	}
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
function block_lab_locate_template( $template_names, $path = '', $single = true ) {
	$path            = apply_filters( 'block_lab_template_path', $path );
	$stylesheet_path = get_template_directory();
	$template_path   = get_stylesheet_directory();

	$located = [];

	foreach ( (array) $template_names as $template_name ) {

		if ( ! $template_name ) {
			continue;
		}

		if ( ! empty( $path ) && file_exists( $path . '/' . $template_name ) ) {
			$located[] = $path . '/' . $template_name;
			if ( $single ) {
				break;
			}
		}

		if ( file_exists( $stylesheet_path . '/' . $template_name ) ) {
			$located[] = $stylesheet_path . '/' . $template_name;
			if ( $single ) {
				break;
			}
		}

		if ( file_exists( $template_path . '/' . $template_name ) ) {
			$located[] = $template_path . '/' . $template_name;
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
function block_lab_get_icons() {
	// This is on the local filesystem, so file_get_contents() is ok to use here.
	$json_file = block_lab()->get_assets_path( 'icons.json' );
	$json      = file_get_contents( $json_file ); // @codingStandardsIgnoreLine
	$icons     = json_decode( $json, true );

	return apply_filters( 'block_lab_icons', $icons );
}

/**
 * Provides a list of allowed tags to be used by an <svg>.
 *
 * @return array
 */
function block_lab_allowed_svg_tags() {
	$allowed_tags = array(
		'svg'    => array(
			'xmlns'   => true,
			'width'   => true,
			'height'  => true,
			'viewbox' => true,
		),
		'g'      => array( 'fill' => true ),
		'title'  => array( 'title' => true ),
		'path'   => array(
			'd'       => true,
			'fill'    => true,
			'opacity' => true,
		),
		'circle' => array(
			'cx'   => true,
			'cy'   => true,
			'r'    => true,
			'fill' => true,
		),
	);

	return apply_filters( 'block_lab_allowed_svg_tags', $allowed_tags );
}
