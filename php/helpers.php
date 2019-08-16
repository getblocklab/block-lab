<?php
/**
 * Helper functions.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

use Block_Lab\Blocks;

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
		! is_array( $block_lab_attributes ) ||
		( ! isset( $block_lab_config->fields[ $name ] ) && 'className' !== $name )
	) {
		return null;
	}

	$value = false; // This is a good default, it allows us to pick up on unchecked checkboxes.
	if ( array_key_exists( $name, $block_lab_attributes ) ) {
		$value = $block_lab_attributes[ $name ];
	}

	// Cast default Editor attributes appropriately.
	if ( 'className' === $name ) {
		$value = strval( $value );
	}

	// Cast block value as correct type.
	if ( isset( $block_lab_config->fields[ $name ]->type ) ) {
		switch ( $block_lab_config->fields[ $name ]->type ) {
			case 'string':
				$value = strval( $value );
				break;
			case 'textarea':
				$value = strval( $value );
				if ( isset( $block_lab_config->fields[ $name ]->settings['new_lines'] ) ) {
					if ( 'autop' === $block_lab_config->fields[ $name ]->settings['new_lines'] ) {
						$value = wpautop( $value );
					}
					if ( 'autobr' === $block_lab_config->fields[ $name ]->settings['new_lines'] ) {
						$value = nl2br( $value );
					}
				}
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
				if ( ! $value ) {
					$value = array();
				} else {
					$value = (array) $value;
				}
				break;
		}
	}

	$control = isset( $block_lab_config->fields[ $name ]->control ) ? $block_lab_config->fields[ $name ]->control : null;

	/**
	 * Filters the value to be made available or echoed on the front-end template.
	 *
	 * @param mixed       $value The value.
	 * @param string|null $control The type of the control, like 'user', or null if this is the 'className', which has no control.
	 * @param bool        $echo Whether or not this value will be echoed.
	 */
	$value = apply_filters( 'block_lab_field_value', $value, $control, $echo );

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
	return (array) $block_lab_config;
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
	if ( ! isset( $block_lab_config->fields[ $name ] ) ) {
		return null;
	}
	return (array) $block_lab_config->fields[ $name ];
}
