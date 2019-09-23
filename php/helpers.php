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
 * Return the value of a block field.
 *
 * @param string $name The name of the field.
 * @param bool   $echo Whether to echo and return the field, or just return the field.
 *
 * @return mixed
 */
function block_field( $name, $echo = true ) {
	/*
	 * Defined in Block_Lab\Blocks\Loader->render_block_template().
	 *
	 * @var array
	 */
	global $block_lab_attributes, $block_lab_config;

	if ( ! isset( $block_lab_attributes ) || ! is_array( $block_lab_attributes ) ) {
		return null;
	}

	$default_fields = array( 'className' => 'string' );

	/**
	 * Filters the default fields that are allowed in addition to Block Lab fields.
	 *
	 * Adding an attribute to this can enable outputting it via block_field().
	 * Normally, this function only returns or echoes Block Lab attributes (fields), and one default field.
	 * But this allows getting block attributes that might have been added by other plugins or JS.
	 * To allow getting another attribute, add it to the $default_fields associative array.
	 * For example, 'your-example-field' => 'array'.
	 *
	 * @param array  $default_fields An associative array of $field_name => $field_type.
	 * @param string $name The name of value to get.
	 */
	$default_fields = apply_filters( 'block_lab_default_fields', $default_fields, $name );

	if ( ! isset( $block_lab_config->fields[ $name ] ) && ! isset( $default_fields[ $name ] ) ) {
		return null;
	}

	$field   = null;
	$value   = false; // This is a good default, it allows us to pick up on unchecked checkboxes.
	$control = null;

	if ( array_key_exists( $name, $block_lab_attributes ) ) {
		$value = $block_lab_attributes[ $name ];
	}

	if ( isset( $block_lab_config->fields[ $name ] ) ) {
		// Cast the value with the correct type.
		$field   = $block_lab_config->fields[ $name ];
		$value   = $field->cast_value( $value );
		$control = $field->control;
	} elseif ( isset( $default_fields[ $name ] ) ) {
		// Cast default Editor attributes and those added via a filter.
		$field = new Blocks\Field( array( 'type' => $default_fields[ $name ] ) );
		$value = $field->cast_value( $value );
	}

	/**
	 * Filters the value to be made available or echoed on the front-end template.
	 *
	 * @param mixed       $value The value.
	 * @param string|null $control The type of the control, like 'user', or null if this is the 'className', which has no control.
	 * @param bool        $echo Whether or not this value will be echoed.
	 */
	$value = apply_filters( 'block_lab_field_value', $value, $control, $echo );

	if ( $echo ) {
		if ( $field ) {
			$value = $field->cast_value_to_string( $value );
		}

		/*
		 * Escaping this value may cause it to break in some use cases.
		 * If this happens, retrieve the field's value using block_value(),
		 * and then output the field with a more suitable escaping function.
		 */
		echo wp_kses_post( $value );
	}

	return $value;
}

/**
 * Return the value of a block field, without echoing it.
 *
 * @param string $name The name of the field as created in the UI.
 *
 * @uses block_field()
 *
 * @return mixed
 */
function block_value( $name ) {
	return block_field( $name, false );
}

/**
 * Prepare a loop with the first or next row in a repeater.
 *
 * @param string $name The name of the repeater field.
 *
 * @return int
 */
function block_row( $name ) {
	block_lab()->loop()->set_active( $name );
	return block_lab()->loop()->increment( $name );
}

/**
 * Determine whether another repeater row exists to loop through.
 *
 * @param string $name The name of the repeater field.
 *
 * @return bool
 */
function block_rows( $name ) {
	global $block_lab_attributes;

	if ( ! isset( $block_lab_attributes[ $name ] ) ) {
		return false;
	}

	$current_row = block_lab()->loop()->get_row( $name );

	if ( false === $current_row ) {
		$next_row = 0;
	} else {
		$next_row = $current_row + 1;
	}

	if ( isset( $block_lab_attributes[ $name ]['rows'][ $next_row ] ) ) {
		return true;
	}

	return false;
}

/**
 * Resets the repeater block rows after the while loop.
 *
 * Similar to wp_reset_postdata(). Call this after the repeater loop.
 * For example:
 *
 * while ( block_rows( 'example-repeater-name' ) ) :
 *     block_row( 'example-repeater-name' );
 *     block_sub_field( 'example-field' );
 * endwhile;
 * reset_block_rows( 'example-repeater-name' );
 *
 * @param string $name The name of the repeater field.
 */
function reset_block_rows( $name ) {
	block_lab()->loop()->reset( $name );
}

/**
 * Return the total amount of rows in a repeater.
 *
 * @param string $name The name of the repeater field.
 * @return int|bool The total amount of rows. False if the repeater isn't found.
 */
function block_row_count( $name ) {
	global $block_lab_attributes;

	if ( ! isset( $block_lab_attributes[ $name ]['rows'] ) ) {
		return false;
	}

	return count( $block_lab_attributes[ $name ]['rows'] );
}

/**
 * Return the index of the current repeater row.
 *
 * Note: The index is zero-based, which means that the first row in a repeater has
 * an index of 0, the second row has an index of 1, and so on.
 *
 * @param string $name (Optional) The name of the repeater field.
 * @return int|bool The index of the row. False if the repeater isn't found.
 */
function block_row_index( $name = '' ) {
	if ( '' === $name ) {
		$name = block_lab()->loop()->active;
	}

	if ( ! isset( block_lab()->loop()->loops[ $name ] ) ) {
		return false;
	}

	return block_lab()->loop()->loops[ $name ];
}

/**
 * Return the value of a sub-field.
 *
 * @param string $name The name of the sub-field.
 * @param bool   $echo Whether to echo and return the field, or just return the field.
 *
 * @return mixed
 */
function block_sub_field( $name, $echo = true ) {
	/*
	 * Defined in Block_Lab\Blocks\Loader->render_block_template().
	 *
	 * @var array
	 */
	global $block_lab_attributes, $block_lab_config;

	if ( ! isset( $block_lab_attributes ) || ! is_array( $block_lab_attributes ) ) {
		return null;
	}

	$parent  = block_lab()->loop()->active;
	$pointer = block_lab()->loop()->get_row( $parent );

	if ( ! isset( $block_lab_config->fields[ $parent ] ) ) {
		return null;
	}

	$value   = false; // This is a good default, it allows us to pick up on unchecked checkboxes.
	$control = null;

	// Get the value from the block attributes, with the correct type.
	if ( ! array_key_exists( $parent, $block_lab_attributes ) || ! isset( $block_lab_attributes[ $parent ]['rows'] ) ) {
		return;
	}

	$parent_attributes = $block_lab_attributes[ $parent ]['rows'];
	$row_attributes    = $parent_attributes[ $pointer ];

	if ( ! array_key_exists( $name, $row_attributes ) ) {
		return;
	}

	$field   = $block_lab_config->fields[ $parent ]->settings['sub_fields'][ $name ];
	$control = $field->control;
	$value   = $row_attributes[ $name ];
	$value   = $field->cast_value( $value );

	/**
	 * Filters the value to be made available or echoed on the front-end template.
	 *
	 * @param mixed       $value The value.
	 * @param string|null $control The type of the control, like 'user', or null if this is the 'className', which has no control.
	 * @param bool        $echo Whether or not this value will be echoed.
	 */
	$value = apply_filters( 'block_lab_sub_field_value', $value, $control, $echo );

	if ( $echo ) {
		$value = $field->cast_value_to_string( $value );

		/*
		 * Escaping this value may cause it to break in some use cases.
		 * If this happens, retrieve the field's value using block_value(),
		 * and then output the field with a more suitable escaping function.
		 */
		echo wp_kses_post( $value );
	}

	return $value;
}

/**
 * Return the value of a sub-field, without echoing it.
 *
 * @param string $name The name of the sub-field.
 *
 * @uses block_field()
 *
 * @return mixed
 */
function block_sub_value( $name ) {
	return block_sub_field( $name, false );
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

/**
 * Add a new block.
 *
 * @param string $name The block name (slug), like 'example-block'.
 * @param array  $config {
 *     An associative array containing the block configuration.
 *
 *     @type string   $title    The block title.
 *     @type string   $icon     The block icon. See assets/icons.json for a JSON array of all possible values. Default: 'block_lab'.
 *     @type string   $category The slug of a registered category. Categories include: common, formatting, layout, widgets, embed. Default: 'common'.
 *     @type array    $excluded Exclude the block in these post types. Default: [].
 *     @type string[] $keywords An array of up to three keywords. Default: [].
 *     @type array    $fields {
 *         An associative array containing block fields. Each key in the array should be the field slug.
 *
 *         @type array {$slug} {
 *             An associative array describing a field.
 *
 *             @type string $name    The field name.
 *             @type string $label   The field label.
 *             @type string $control The field control type. Default: 'text'.
 *             @type string $type    The field variable type. Default: 'string'.
 *             @type int    $order   The order that the field appears in. Default: 0.
 *             @type array  $settings {
 *                 An associative array of settings for the field. Each field has a different set of possible settings.
 *                 Check the register_settings method for the field, found in php/blocks/controls/class-{field name}.php.
 *             }
 *         }
 *     }
 * }
 */
function block_lab_add_block( $name, $config = array() ) {
	$config['name'] = str_replace( '_', '-', sanitize_title( $name ) );

	if ( ! isset( $config['title'] ) ) {
		$config['title'] = ucwords( $config['name'], '-' );
		$config['title'] = str_replace( '-', ' ', $config['title'] );
	}

	if ( ! isset( $config['icon'] ) ) {
		$config['icon'] = 'block_lab';
	}

	if ( ! isset( $config['category'] ) ) {
		$config['category'] = 'common';
	}

	if ( ! isset( $config['excluded'] ) ) {
		$config['excluded'] = array();
	}

	if ( ! isset( $config['keywords'] ) ) {
		$config['keywords'] = array();
	}

	if ( ! isset( $config['fields'] ) ) {
		$config['fields'] = array();
	}

	add_filter(
		'block_lab_blocks',
		function( $blocks ) use ( $config ) {
			if ( ! isset( $config['name'] ) ) {
				return $blocks;
			}
			$blocks[ "block-lab/{$config['name']}" ] = $config;
			return $blocks;
		}
	);
}
