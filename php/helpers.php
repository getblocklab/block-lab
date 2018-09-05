<?php
/**
 * Helper functions.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Echos out the value of an ACB block field.
 *
 * @param string $key  The name of the field as created in the UI.
 * @param bool   $echo Whether to echo and return the field, or just return the field.
 *
 * @return mixed|null
 */
function acb_field( $key, $echo = true ) {
	global $acb_block_attributes;

	if ( ! isset( $acb_block_attributes ) || ! is_array( $acb_block_attributes ) || ! array_key_exists( $key, $acb_block_attributes ) ) {
		return null;
	}

	$value = $acb_block_attributes[ $key ];

	if ( $echo ) {
		/**
		 * Escaping this value may cause it to break in some use cases.
		 * If this happens, retrieve the field's value using acb_value(),
		 * and then output the field with a more suitable escaping function.
		 */
		echo wp_kses_post( $value );
	}

	return $value;
}

/**
 * Convenience method to return the value of an ACB block field.
 *
 * @param string $key The name of the field as created in the UI.
 *
 * @uses acb_field()
 *
 * @return mixed|null
 */
function acb_value( $key ) {
	return acb_field( $key, false );
}

/**
 * Loads a template part to render the ACB block.
 *
 * @param string $slug The name of the block (slug as defined in UI).
 * @param string $type The type of template to load. Only 'block' supported at this stage.
 */
function acb_template_part( $slug, $type = 'block' ) {
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

		$template_file = "blocks/{$type}-{$slug}.php";
		$generic_file  = "blocks/{$type}.php";
		$templates     = [
			$generic_file,
			$template_file,
		];

		$located = abc_locate_template( $templates );
	}

	if ( ! empty( $located ) ) {
		$theme_template = apply_filters( 'acb_override_theme_template', $located );

		// This is not a load once template, so require_once is false.
		load_template( $theme_template, false );
	} else {
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
 * Locates ACB templates.
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
function acb_locate_template( $template_names, $path = '', $single = true ) {
	$path            = apply_filters( 'acb_template_path', $path );
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
 * To include custom icons in this list use a unique key, and specify a type of 'svg' with a value of raw SVG content.
 *
 * For example:
 * $icons['foo'] = array( 'type' => 'svg', 'value' => '<svg role="img" xmlns="http://www.w3.org/2000/svg">…</svg>' );
 *
 * @return array
 */
function acb_get_icons() {
	$icons = array();

	// This is on the local filesystem, so file_get_contents() is ok to use here.
	$json_file      = advanced_custom_blocks()->get_assets_path( 'icons.json' );
	$json           = file_get_contents( $json_file ); // @codingStandardsIgnoreLine
	$material_icons = json_decode( $json, true );

	foreach ( $material_icons as $icon ) {
		$icons[ $icon ] = array(
			'type'  => 'material-icon',
			'value' => $icon,
		);
	}

	$icons['example'] = array(
		'type' => 'svg',
		'value' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.5,11h-2V9H1v6h1.5v-2.5h2V15H6V9H4.5V11z M7,10.5h1.5V15H10v-4.5h1.5V9H7V10.5z M14.5,10l-1-1H12v6h1.5v-3.9  l1,1l1-1V15H17V9h-1.5L14.5,10z M19.5,13.5V9H18v6h5v-1.5H19.5z" /></svg>'
	);

	return apply_filters( 'acb_icons', $icons );
}
