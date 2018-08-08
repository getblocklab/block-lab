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
	 * @var Option[]
	 */
	public $options = array();

	/**
	 * Text constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->options[] = new Option( array(
			'name'    => 'is_required',
			'label'   => __( 'Required?', 'advanced-custom-blocks' ),
			'type'    => 'checkbox',
			'default' => false,
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
					if ( method_exists( $this, 'render_control_' . $option->name ) ) {
						call_user_func( array( $this, 'render_options_' . $option->name, $option, $id ) );
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
	 * @param Option $option
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
}
