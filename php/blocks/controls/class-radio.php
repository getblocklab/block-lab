<?php
/**
 * Radio control.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Advanced_Custom_Blocks\Blocks\Controls;

/**
 * Class Range
 */
class Radio extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'radio';

	/**
	 * Radio constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Radio', 'advanced-custom-blocks' );
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
			'name'     => 'options',
			'label'    => __( 'Choices', 'advanced-custom-blocks' ),
			'type'     => 'textarea_array',
			'default'  => '',
			'help'     => sprintf(
				'%s %s<br />%s<br />%s',
				__( 'Enter each choice on a new line.', 'advanced-custom-blocks' ),
				__( 'To specify the value and label separately, use this format:', 'advanced-custom-blocks'),
				_x( 'foo : Foo', 'Format for the menu values. option_value : Option Name', 'advanced-custom-blocks' ),
				_x( 'bar : Bar', 'Format for the menu values. option_value : Option Name', 'advanced-custom-blocks' )
			),
			'sanitize' => array( $this, 'sanitise_textarea_assoc_array' ),
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'default',
			'label'    => __( 'Default Value', 'advanced-custom-blocks' ),
			'type'     => 'text',
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
		) );
	}
}
