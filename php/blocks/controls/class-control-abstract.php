<?php
/**
 * Control abstract.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Advanced_Custom_Blocks\Blocks\Controls;

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
	 * Text constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->options[] = new Control_Option( array(
			'name'    => 'default',
			'label'   => __( 'Default Value', 'advanced-custom-blocks' ),
			'type'    => 'text',
			'default' => '',
		) );
		$this->options[] = new Control_Option( array(
			'name'    => 'help',
			'label'   => __( 'Field instructions', 'advanced-custom-blocks' ),
			'type'    => 'textarea',
			'default' => '',
		) );
	}

	/**
	 * Render additional options in table rows.
	 *
	 * @return void
	 */
	public function render_options() {
		$uid = uniqid();
		foreach ( $this->options as $option ) {
			$classes = array(
				'acb-fields-edit-options-' . $this->name . '-' . $option->name,
				'acb-fields-edit-options-' . $this->name,
				'acb-fields-edit-options',
			);
			$id = 'acb-fields-edit-options-' . $this->name . '-' . $option->name . '_' . $uid;
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
						$this->$method( $option, $id );
					} else {
						$this->render_options_text( $option, $id );
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
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_options_text( $option, $id ) {
		?>
		<input
			name="acb-fields-options[<?php echo esc_attr( $option->name ); ?>]"
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
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_options_textarea( $option, $id ) {
		?>
		<textarea
			name="acb-fields-options[<?php echo esc_attr( $option->name ); ?>]"
			id="<?php echo esc_attr( $id ); ?>"
			class="large-text"><?php echo esc_attr( $option->get_value() ); ?></textarea>
		<?php
	}

	/**
	 * Render checkbox options
	 *
	 * @param Control_Option $option
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_options_checkbox( $option, $id ) {
		?>
		<input
			name="acb-fields-options[<?php echo esc_attr( $option->name ); ?>]"
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
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_options_number( $option, $id ) {
		?>
		<input
				name="acb-fields-options[<?php echo esc_attr( $option->name ); ?>]"
				type="number"
				id="<?php echo esc_attr( $id ); ?>"
				class="regular-text"
				min="0"
				value="<?php echo esc_attr( $option->get_value() ); ?>" />
		<?php
	}
}
