<?php
/**
 * Control abstract.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

use Block_Lab\Blocks\Field;

/**
 * Class Control_Abstract
 */
abstract class Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Control label.
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'string';

	/**
	 * Control settings.
	 *
	 * @var Control_Setting[]
	 */
	public $settings = array();

	/**
	 * Control constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->register_settings();
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	abstract public function register_settings();

	/**
	 * Render additional settings in table rows.
	 *
	 * @param Field  $field The Field containing the options to render.
	 * @param string $uid   A unique ID to used to unify the HTML name, for, and id attributes.
	 *
	 * @return void
	 */
	public function render_settings( $field, $uid ) {
		foreach ( $this->settings as $setting ) {
			if ( isset( $field->settings[ $setting->name ] ) ) {
				$setting->value = $field->settings[ $setting->name ];
			} else {
				$setting->value = $setting->default;
			}

			$classes = array(
				'block-fields-edit-settings-' . $this->name . '-' . $setting->name,
				'block-fields-edit-settings-' . $this->name,
				'block-fields-edit-settings',
			);
			$name    = 'block-fields-settings[' . $uid . '][' . $setting->name . ']';
			$id      = 'block-fields-edit-settings-' . $this->name . '-' . $setting->name . '_' . $uid;
			?>
			<tr class="<?php echo esc_attr( implode( $classes, ' ' ) ); ?>">
				<td class="spacer"></td>
				<th scope="row">
					<label for="<?php echo esc_attr( $id ); ?>">
						<?php echo esc_html( $setting->label ); ?>
					</label>
					<p class="description">
						<?php echo wp_kses_post( $setting->help ); ?>
					</p>
				</th>
				<td>
					<?php
					$method = 'render_settings_' . $setting->type;
					if ( method_exists( $this, $method ) ) {
						$this->$method( $setting, $name, $id );
					} else {
						$this->render_settings_text( $setting, $name, $id );
					}
					?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Render text settings
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_text( $setting, $name, $id ) {
		?>
		<input
			name="<?php echo esc_attr( $name ); ?>"
			type="<?php echo esc_attr( $setting->type ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			class="regular-text"
			value="<?php echo esc_attr( $setting->get_value() ); ?>" />
		<?php
	}

	/**
	 * Render textarea settings
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_textarea( $setting, $name, $id ) {
		?>
		<textarea
			name="<?php echo esc_attr( $name ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			rows="6"
			class="large-text"><?php echo esc_html( $setting->get_value() ); ?></textarea>
		<?php
	}

	/**
	 * Render checkbox settings
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_checkbox( $setting, $name, $id ) {
		?>
		<input
			name="<?php echo esc_attr( $name ); ?>"
			type="checkbox"
			id="<?php echo esc_attr( $id ); ?>"
			class=""
			value="1"
			<?php checked( '1', $setting->get_value() ); ?> />
		<?php
	}

	/**
	 * Render number settings.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_number( $setting, $name, $id ) {
		$this->render_number( $setting, $name, $id );
	}

	/**
	 * Render the number settings, forcing the number in the <input> to be non-negative.
	 * This could be 0, 1, 2, etc, but not -1.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_number_non_negative( $setting, $name, $id ) {
		$this->render_number( $setting, $name, $id, true );
	}

	/**
	 * Render the number settings, optionally outputting a min="0" attribute to enforce a non-negative value.
	 *
	 * @param Control_Setting $setting      The Control_Setting being rendered.
	 * @param string          $name         The name attribute of the option.
	 * @param string          $id           The id attribute of the option.
	 * @param bool            $non_negative Whether to force the number to be non-negative via a min="0" attribute.
	 *
	 * @return void
	 */
	public function render_number( $setting, $name, $id, $non_negative = false ) {
		?>
		<input
			name="<?php echo esc_attr( $name ); ?>"
			type="number"
			id="<?php echo esc_attr( $id ); ?>"
			class="regular-text"
			<?php echo $non_negative ? 'min="0"' : ''; ?>
			value="<?php echo esc_attr( $setting->get_value() ); ?>" />
		<?php
	}

	/**
	 * Render an array of settings inside a textarea.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_textarea_array( $setting, $name, $id ) {
		$options = $setting->get_value();
		if ( is_array( $options ) ) {
			// Convert the array to text separated by new lines.
			$value = '';
			foreach ( $options as $option ) {
				if ( ! is_array( $option ) ) {
					$value .= $option . "\n";
					continue;
				}
				if ( ! isset( $option['value'] ) || ! isset( $option['label'] ) ) {
					continue;
				}
				if ( $option['value'] === $option['label'] ) {
					$value .= $option['label'] . "\n";
				} else {
					$value .= $option['value'] . ' : ' . $option['label'] . "\n";
				}
			}
			$setting->value = trim( $value );
		}
		$this->render_settings_textarea( $setting, $name, $id );
	}

	/**
	 * Render a <select> of public post types.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_post_type_rest_slug( $setting, $name, $id ) {
		$this->render_select(
			array(
				'setting' => $setting,
				'name'    => $name,
				'id'      => $id,
				'values'  => $this->get_post_type_rest_slugs(),
			)
		);
	}

	/**
	 * Gets the REST slugs of public post types, other than 'attachment'.
	 *
	 * @return array {
	 *     An associative array of the post type REST slugs.
	 *
	 *     @type string $rest_slug The REST slug of the post type.
	 *     @type string $name The name of the post type.n
	 * }
	 */
	public function get_post_type_rest_slugs() {
		$post_type_rest_slugs = array();
		foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			if ( ! $post_type_object || empty( $post_type_object->show_in_rest ) ) {
				continue;
			}
			if ( 'attachment' === $post_type ) {
				continue;
			}
			$rest_slug                          = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type;
			$labels                             = get_post_type_labels( $post_type_object );
			$post_type_name                     = isset( $labels->name ) ? $labels->name : $post_type;
			$post_type_rest_slugs[ $rest_slug ] = $post_type_name;
		}
		return $post_type_rest_slugs;
	}

	/**
	 * Renders a <select> of public taxonomy types.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_taxonomy_type_rest_slug( $setting, $name, $id ) {
		$this->render_select(
			array(
				'setting' => $setting,
				'name'    => $name,
				'id'      => $id,
				'values'  => $this->get_taxonomy_type_rest_slugs(),
			)
		);
	}

	/**
	 * Renders a <select> of the passed values.
	 *
	 * @param array $args {
	 *     The arguments to render a <select> element.
	 *
	 *     @type Control_Setting $setting The Control_Setting being rendered.
	 *     @type string          $name    The name attribute of the option.
	 *     @type string          $id      The id attribute of the option.
	 *     @type array           $values {
	 *         An associative array of the post type REST slugs.
	 *
	 *         @type string $rest_slug The rest slug, like 'tags' for the 'post_tag' taxonomy.
	 *         @type string $label     The label to display inside the <option>.
	 *     }
	 * }
	 *
	 * @return void
	 */
	public function render_select( $args ) {
		if ( ! isset( $args['setting'], $args['name'], $args['id'], $args['values'] ) ) {
			return;
		}

		?>
		<select name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php
			foreach ( $args['values'] as $rest_slug => $label ) :
				?>
				<option value="<?php echo esc_attr( $rest_slug ); ?>" <?php selected( $rest_slug, $args['setting']->get_value() ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Gets the REST slugs of public taxonomy types.
	 *
	 * @return array {
	 *     An associative array of the post type REST slugs.
	 *
	 *     @type string $rest_slug The REST slug of the post type.
	 *     @type string $name The name of the post type.
	 * }
	 */
	public function get_taxonomy_type_rest_slugs() {
		$taxonomy_rest_slugs = array();
		foreach ( get_taxonomies( array( 'show_in_rest' => true ) ) as $taxonomy_slug ) {
			$taxonomy_object                   = get_taxonomy( $taxonomy_slug );
			$rest_slug                         = ! empty( $taxonomy_object->rest_base ) ? $taxonomy_object->rest_base : $taxonomy_slug;
			$taxonomy_rest_slugs[ $rest_slug ] = $taxonomy_object->label;
		}
		return $taxonomy_rest_slugs;
	}

	/**
	 * Sanitize checkbox.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_checkbox( $value ) {
		if ( '1' === $value ) {
			return 1;
		}
		return 0;
	}

	/**
	 * Sanitize non-zero number.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return int
	 */
	public function sanitize_number( $value ) {
		if ( empty( $value ) || '0' === $value ) {
			return null;
		}
		return (int) filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
	}

	/**
	 * Sanitize an array of settings inside a textarea.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_textarea_assoc_array( $value ) {
		$rows    = preg_split( '/\r\n|[\r\n]/', $value );
		$options = array();

		foreach ( $rows as $key => $option ) {
			if ( '' === $option ) {
				continue;
			}

			$key_value = explode( ' : ', $option );

			if ( count( $key_value ) > 1 ) {
				$options[ $key ]['label'] = $key_value[1];
				$options[ $key ]['value'] = $key_value[0];
			} else {
				$options[ $key ]['label'] = $option;
				$options[ $key ]['value'] = $option;
			}
		}

		// Reindex array in case of blank lines.
		$options = array_values( $options );

		return $options;
	}

	/**
	 * Sanitize an array of settings inside a textarea.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_textarea_array( $value ) {
		$rows    = preg_split( '/\r\n|[\r\n]/', $value );
		$options = array();

		foreach ( $rows as $key => $option ) {
			if ( '' === $option ) {
				continue;
			}

			$key_value = explode( ' : ', $option );

			if ( count( $key_value ) > 1 ) {
				$options[] = $key_value[0];
			} else {
				$options[] = $option;
			}
		}

		// Reindex array in case of blank lines.
		$options = array_values( $options );

		return $options;
	}

	/**
	 * Validate that the value is contained within a list of options,
	 * and if not, return the first option.
	 *
	 * @param mixed $value    The value to be validated.
	 * @param array $settings The field settings.
	 *
	 * @return mixed
	 */
	public function validate_options( $value, $settings ) {
		if ( ! array_key_exists( 'options', $settings ) ) {
			return $value;
		}

		// Allow an empty value.
		if ( '' === $value ) {
			return $value;
		}

		$options = array();

		// Reindex the options into a more workable format.
		array_walk(
			$settings['options'],
			function( $option ) use ( &$options ) {
				$options[] = $option['value'];
			}
		);

		if ( is_array( $value ) ) {
			// Filter out invalid options where multiple options can be chosen.
			foreach ( $value as $key => $option ) {
				if ( ! in_array( $option, $options, true ) ) {
					unset( $value[ $key ] );
				}
			}
		} else {
			// If the value is not in the set of options, return an empty string.
			if ( ! in_array( $value, $options, true ) ) {
				$value = '';
			}
		}

		return $value;
	}

	/**
	 * Sanitize the post type REST slug, to ensure that it's a public post type.
	 *
	 * This expects the rest_base of the post type, as it's easier to pass that to apiFetch in the Post control.
	 * So this iterates through the public post types, to find if one has the rest_base equal to $value.
	 *
	 * @param string $value The rest_base of the post type to sanitize.
	 * @return string|null The sanitized rest_base of the post type, or null.
	 */
	public function sanitize_post_type_rest_slug( $value ) {
		if ( array_key_exists( $value, $this->get_post_type_rest_slugs() ) ) {
			return $value;
		}
		return null;
	}

	/**
	 * Sanitize the taxonomy type REST slug, to ensure that it's registered and public.
	 *
	 * @param string $value The rest_base of the post type to sanitize.
	 * @return string|null The sanitized rest_base of the post type, or null.
	 */
	public function sanitize_taxonomy_type_rest_slug( $value ) {
		if ( array_key_exists( $value, $this->get_taxonomy_type_rest_slugs() ) ) {
			return $value;
		}
		return null;
	}

}
