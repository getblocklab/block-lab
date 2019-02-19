<?php
/**
 * Select control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Select
 */
class Select extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'select';

	/**
	 * Select constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Select', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'help',
				'label'    => __( 'Help Text', 'block-lab' ),
				'type'     => 'text',
				'default'  => '',
				'sanitize' => 'sanitize_text_field',
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'options',
				'label'    => __( 'Choices', 'block-lab' ),
				'type'     => 'textarea_array',
				'default'  => '',
				'help'     => sprintf(
					'%s %s<br />%s<br />%s',
					__( 'Enter each choice on a new line.', 'block-lab' ),
					__( 'To specify the value and label separately, use this format:', 'block-lab' ),
					_x( 'foo : Foo', 'Format for the menu values. option_value : Option Name', 'block-lab' ),
					_x( 'bar : Bar', 'Format for the menu values. option_value : Option Name', 'block-lab' )
				),
				'sanitize' => array( $this, 'sanitize_textarea_assoc_array' ),
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'default',
				'label'    => __( 'Default Value', 'block-lab' ),
				'type'     => 'text',
				'default'  => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => array( $this, 'validate_options' ),
			)
		);
	}
}
