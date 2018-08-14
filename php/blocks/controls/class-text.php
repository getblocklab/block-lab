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
 * Class Text
 */
class Text extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'text';

	/**
	 * Text constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Text', 'advanced-custom-blocks' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting( array(
			'name'     => 'help',
			'label'    => __( 'Field instructions', 'advanced-custom-blocks' ),
			'type'     => 'textarea',
			'default'  => '',
			'sanitize' => 'sanitize_textarea_field',
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'required',
			'label'    => __( 'Required?', 'advanced-custom-blocks' ),
			'type'     => 'checkbox',
			'default'  => '0',
			'sanitize' => array( $this, 'sanitise_checkbox' ),
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'default',
			'label'    => __( 'Default Value', 'advanced-custom-blocks' ),
			'type'     => 'text',
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'placeholder',
			'label'    => __( 'Placeholder Text', 'advanced-custom-blocks' ),
			'type'     => 'text',
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
		) );
		$this->settings[] = new Control_Setting( array(
			'name'    => 'maxlength',
			'label'   => __( 'Character Limit', 'advanced-custom-blocks' ),
			'type'    => 'number',
			'default' => '',
			'sanitize' => function( $value ) {
				if ( empty( $value ) || '0' === $value ) {
					return null;
				}
				return (int) filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
			}
		) );
	}
}
