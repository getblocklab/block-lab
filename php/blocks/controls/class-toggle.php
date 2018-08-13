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
 * Class Toggle
 */
class Toggle extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'toggle';

	/**
	 * Text constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Toggle', 'advanced-custom-blocks' );
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
			'name'     => 'default',
			'label'    => __( 'Default Value', 'advanced-custom-blocks' ),
			'type'     => 'checkbox',
			'default'  => '0',
			'sanitize' => array( $this, 'sanitise_checkbox' ),
		) );
	}
}
