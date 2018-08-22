<?php
/**
 * Control abstract.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Advanced_Custom_Blocks\Blocks\Controls;

use Advanced_Custom_Blocks\Blocks\Field;

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
	 * @param Field $field
	 * @param string $uid
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
				'acb-fields-edit-settings-' . $this->name . '-' . $setting->name,
				'acb-fields-edit-settings-' . $this->name,
				'acb-fields-edit-settings',
			);
			$name    = 'acb-fields-settings[' . $uid . '][' . $setting->name . ']';
			$id      = 'acb-fields-edit-settings-' . $this->name . '-' . $setting->name . '_' . $uid;
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
	 * @param Control_Setting $setting
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_settings_text( $setting, $name, $id ) {
		?>
		<input
			name="<?php echo esc_attr( $name ); ?>"
			type="text"
			id="<?php echo esc_attr( $id ); ?>"
			class="regular-text"
			value="<?php echo esc_attr( $setting->get_value() ); ?>" />
		<?php
	}

	/**
	 * Render textarea settings
	 *
	 * @param Control_Setting $setting
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_settings_textarea( $setting, $name, $id ) {
		?>
		<textarea
			name="<?php echo esc_attr( $name ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			rows="6"
			class="large-text"><?php echo esc_textarea( $setting->get_value() ); ?></textarea>
		<?php
	}

	/**
	 * Render checkbox settings
	 *
	 * @param Control_Setting $setting
	 * @param string $name
	 * @param string $id
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
	 * Render number settings
	 *
	 * @param Control_Setting $setting
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_settings_number( $setting, $name, $id ) {
		?>
		<input
			name="<?php echo esc_attr( $name ); ?>"
			type="number"
			id="<?php echo esc_attr( $id ); ?>"
			class="regular-text"
			min="1"
			value="<?php echo esc_attr( $setting->get_value() ); ?>" />
		<?php
	}

	/**
	 * Sanitize checkbox
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function sanitise_checkbox( $value ) {
		if ( '1' === $value ) {
			return true;
		}
		return false;
	}

	/**
	 * Sanitize non-zero number
	 *
	 * @param string $value
	 *
	 * @return int
	 */
	public function sanitise_number( $value ) {
		if ( empty( $value ) || '0' === $value ) {
			return null;
		}
		return (int) filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
	}
}
