<?php
/**
 * Control abstract.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
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
	 * Field variable type (passed as an attribute when registering the block in Javascript).
	 *
	 * @var string
	 */
	public $type = 'string';

	/**
	 * Control settings.
	 *
	 * @var Control_Setting[]
	 */
	public $settings = [];

	/**
	 * Configurations for common settings, like 'help' and 'placeholder'.
	 *
	 * @var array {
	 *     An associative array of setting configurations.
	 *
	 *     @type string $setting_name   The name of the setting, like 'help'.
	 *     @type array  $setting_config The default configuration of the setting.
	 * }
	 */
	public $settings_config = [];

	/**
	 * The possible editor locations, either in the main block editor, or the inspector controls.
	 *
	 * @var array
	 */
	public $locations = [];

	/**
	 * Control constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->create_settings_config();
		$this->register_settings();
	}

	/**
	 * Creates the setting configuration.
	 *
	 * This sets the values for common settings, to make adding settings more DRY.
	 * Then, controls can simply use the values here.
	 *
	 * @return void
	 */
	public function create_settings_config() {
		$this->settings_config = [
			'location'    => [
				'name'     => 'location',
				'label'    => __( 'Field Location', 'block-lab' ),
				'type'     => 'location',
				'default'  => 'editor',
				'sanitize' => [ $this, 'sanitize_location' ],
			],
			'width'       => [
				'name'     => 'width',
				'label'    => __( 'Field Width', 'block-lab' ),
				'type'     => 'width',
				'default'  => '100',
				'sanitize' => 'sanitize_text_field',
			],
			'help'        => [
				'name'     => 'help',
				'label'    => __( 'Help Text', 'block-lab' ),
				'type'     => 'text',
				'default'  => '',
				'sanitize' => 'sanitize_text_field',
			],
			'default'     => [
				'name'     => 'default',
				'label'    => __( 'Default Value', 'block-lab' ),
				'type'     => 'text',
				'default'  => '',
				'sanitize' => 'sanitize_text_field',
			],
			'placeholder' => [
				'name'     => 'placeholder',
				'label'    => __( 'Placeholder Text', 'block-lab' ),
				'type'     => 'text',
				'default'  => '',
				'sanitize' => 'sanitize_text_field',
			],
		];

		$this->locations = [
			'editor'    => __( 'Editor', 'block-lab' ),
			'inspector' => __( 'Inspector', 'block-lab' ),
		];
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
			// Don't render the location setting for sub-fields.
			if ( 'location' === $setting->type && isset( $field->settings['parent'] ) ) {
				continue;
			}

			// Don't render the field width setting for sub-fields.
			if ( 'width' === $setting->type && isset( $field->settings['parent'] ) ) {
				continue;
			}

			if ( isset( $field->settings[ $setting->name ] ) ) {
				$setting->value = $field->settings[ $setting->name ];
			} else {
				$setting->value = $setting->default;
			}

			$classes = [
				"block-fields-edit-settings-{$this->name}-{$setting->name}",
				"block-fields-edit-{$setting->name}-settings",
				"block-fields-edit-settings-{$this->name}",
				"block-fields-edit-{$setting->name}-settings",
				'block-fields-edit-settings',
			];
			$name    = 'block-fields-settings[' . $uid . '][' . $setting->name . ']';
			$id      = 'block-fields-edit-settings-' . $this->name . '-' . $setting->name . '_' . $uid;
			?>
			<tr class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
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
	 * Renders a <select> of locations.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_location( $setting, $name, $id ) {
		$this->render_select( $setting, $name, $id, $this->locations );
	}

	/**
	 * Renders a button group of field widths.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_width( $setting, $name, $id ) {
		$widths = [
			'25'  => '25%',
			'50'  => '50%',
			'75'  => '75%',
			'100' => '100%',
		];
		?>
		<div class="button-group">
		<?php
		foreach ( $widths as $value => $label ) {
			?>
			<input
				class="button"
				name="<?php echo esc_attr( $name ); ?>"
				type="radio"
				value="<?php echo esc_attr( $value ); ?>"
				<?php checked( $value, $setting->get_value() ); ?>
				/>
			<label><?php echo esc_html( $label ); ?></label>
			<?php
		}
		?>
		</div>
		<?php
	}

	/**
	 * Renders a <select> of the passed values.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 * @param array           $values {
	 *     An associative array of the post type REST slugs.
	 *
	 *     @type string $rest_slug The rest slug, like 'tags' for the 'post_tag' taxonomy.
	 *     @type string $label     The label to display inside the <option>.
	 * }
	 *
	 * @return void
	 */
	public function render_select( $setting, $name, $id, $values ) {
		?>
		<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>">
			<?php
			foreach ( $values as $value => $label ) :
				?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $setting->get_value() ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
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
		$options = [];

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
		$options = [];

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
	 * Sanitize a location value.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_location( $value ) {
		if ( is_string( $value ) && array_key_exists( $value, $this->locations ) ) {
			return $value;
		}
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

		$options = [];

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
}
