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
	 * Control options.
	 *
	 * @var Control_Option[]
	 */
	public $options = array();

	/**
	 * Control constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->register_options();
	}

	/**
	 * Register options.
	 *
	 * @return void
	 */
	abstract public function register_options();

	/**
	 * Render additional options in table rows.
	 *
	 * @param Field $field
	 * @param string $uid
	 *
	 * @return void
	 */
	public function render_options( $field, $uid ) {
		foreach ( $this->options as $option ) {
			if ( isset( $field->options[ $option->name ] ) ) {
				$option->value = $field->options[ $option->name ];
			} else {
				$option->value = $option->default;
			}

			$classes = array(
				'acb-fields-edit-options-' . $this->name . '-' . $option->name,
				'acb-fields-edit-options-' . $this->name,
				'acb-fields-edit-options',
			);
			$name    = 'acb-fields-options[' . $uid . '][' . $option->name . ']';
			$id      = 'acb-fields-edit-options-' . $this->name . '-' . $option->name . '_' . $uid;
			?>
			<tr class="<?php echo esc_attr( implode( $classes, ' ' ) ); ?>">
				<td class="spacer"></td>
				<th scope="row">
					<label for="<?php echo esc_attr( $id ); ?>">
						<?php echo esc_html( $option->label ); ?>
					</label>
				</th>
				<td>
					<?php
					$method = 'render_options_' . $option->type;
					if ( method_exists( $this, $method ) ) {
						$this->$method( $option, $name, $id );
					} else {
						$this->render_options_text( $option, $name, $id );
					}
					?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Render text options
	 *
	 * @param Control_Option $option
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_options_text( $option, $name, $id ) {
		?>
		<input
			name="<?php echo esc_attr( $name ); ?>"
			type="text"
			id="<?php echo esc_attr( $id ); ?>"
			class="regular-text"
			value="<?php echo esc_attr( $option->get_value() ); ?>" />
		<?php
	}

	/**
	 * Render textarea options
	 *
	 * @param Control_Option $option
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_options_textarea( $option, $name, $id ) {
		?>
		<textarea
			name="<?php echo esc_attr( $name ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			class="large-text"><?php echo esc_attr( $option->get_value() ); ?></textarea>
		<?php
	}

	/**
	 * Render checkbox options
	 *
	 * @param Control_Option $option
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_options_checkbox( $option, $name, $id ) {
		?>
		<input
			name="<?php echo esc_attr( $name ); ?>"
			type="checkbox"
			id="<?php echo esc_attr( $id ); ?>"
			class=""
			value="1"
			<?php checked( '1', $option->get_value() ); ?> />
		<?php
	}

	/**
	 * Render number options
	 *
	 * @param Control_Option $option
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_options_number( $option, $name, $id ) {
		?>
		<input
			name="<?php echo esc_attr( $name ); ?>"
			type="number"
			id="<?php echo esc_attr( $id ); ?>"
			class="regular-text"
			min="1"
			value="<?php echo esc_attr( $option->get_value() ); ?>" />
		<?php
	}
}
