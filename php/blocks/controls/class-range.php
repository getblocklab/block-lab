<?php
/**
 * Range control.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Advanced_Custom_Blocks\Blocks\Controls;

/**
 * Class Range
 */
class Range extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'range';

	/**
	 * Range constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Range', 'advanced-custom-blocks' );
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
			'name'     => 'min',
			'label'    => __( 'Minimum Value', 'advanced-custom-blocks' ),
			'type'     => 'number',
			'default'  => '',
			'sanitize' => array( $this, 'sanitise_number' ),
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'max',
			'label'    => __( 'Maximum Value', 'advanced-custom-blocks' ),
			'type'     => 'number',
			'default'  => '',
			'sanitize' => array( $this, 'sanitise_number' ),
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'step',
			'label'    => __( 'Step Size', 'advanced-custom-blocks' ),
			'type'     => 'number',
			'default'  => 1,
			'sanitize' => array( $this, 'sanitise_number' ),
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'default',
			'label'    => __( 'Default Value', 'advanced-custom-blocks' ),
			'type'     => 'number',
			'default'  => '',
			'sanitize' => array( $this, 'sanitise_number' ),
		) );
	}
}
