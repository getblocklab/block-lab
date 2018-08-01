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

	$template_file = "blocks/{$type}-{$slug}.php";
	$generic_file = "blocks/{$type}.php";
	$templates = [
		$generic_file,
		$template_file,
	];

	// Check for `blocks/block*` in child/parent theme first.
	if ( $theme_template = locate_template( $templates ) ) {
		$theme_template = apply_filters( 'acb_override_theme_template', $theme_template );

		// This is not a load once template, so require_once is false.
		load_template( $theme_template, false );
	} else {
		$template_path = apply_filters( 'acb_template_path', '' );
		if ( file_exists( trailingslashit( $template_path ) . $template_file ) ) {

			// This is not a load once template, so require_once is false.
			load_template( trailingslashit( $template_path ) . $template_file, false );
		} elseif ( file_exists( trailingslashit( $template_path ) . $generic_file ) ) {

			// This is not a load once template, so require_once is false.
			load_template( trailingslashit( $template_path ) . $generic_file, false );
		} else {
			echo '<div class="warning">' . esc_html( $template_file ) . ' not found.</div>';
		}
	}
}

