<?php

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
		echo $value;
	}

	return $value;
}

/**
 * Convenience method to return the value of an ACB block field.
 *
 * @param string $key  The name of the field as created in the UI.
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
 * @param string $slug The name of the block (slug as defined in UI)
 * @param string $type The type of template to load. Only 'block' supported at this stage.
 */
function acb_template_part( $slug, $type = 'block' ) {

	// Loading async it might not come from a query, this breaks load_template();
	global $wp_query;
	
	// So lets fix it.
	if ( empty( $wp_query ) ) {
		$wp_query = new WP_Query();
	}

	$types = (array) $type;
	$located = '';

	foreach( $types as $type ) {

		if ( ! empty( $located ) ) {
			continue;
		}

		$template_file = "blocks/{$type}-{$slug}.php";
		$generic_file = "blocks/{$type}.php";
		$templates = [
			$generic_file,
			$template_file,
		];

		$located = abc_locate_template( $templates );
	}

	if ( ! empty( $located) ) {
		$theme_template = apply_filters( 'acb_override_theme_template', $located );

		// This is not a load once template, so require_once is false.
		load_template( $theme_template, false );
	} else {
		printf(
			'<div class="notice notice-warning">%s</div>',
			wp_kses_post(
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
 * @param string       $path (Optional) Path to located the templates first.
 *
 * @return string
 */
function abc_locate_template( $template_names, $path = '' ) {

	$path  = apply_filters( 'acb_template_path', $path );
	$stylesheet_path = get_template_directory();
	$template_path   = get_stylesheet_directory();

	$located = '';

	foreach ( (array) $template_names as $template_name ) {

		if ( !$template_name ) {
			continue;
		}

		if ( ! empty( $path ) && file_exists( $path . '/' . $template_name ) ) {
			$located = $path . '/' . $template_name;
		} elseif ( file_exists($stylesheet_path . '/' . $template_name)) {
			$located = $stylesheet_path . '/' . $template_name;
			break;
		} elseif ( file_exists($template_path . '/' . $template_name) ) {
			$located = $template_path . '/' . $template_name;
			break;
		} elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
			$located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
			break;
		}
	}

	return $located;
}
