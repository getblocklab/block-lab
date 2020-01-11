<?php
/**
 * Toggle control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

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
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'boolean';

	/**
	 * Toggle constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Toggle', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting( $this->settings_config['location'] );
		$this->settings[] = new Control_Setting( $this->settings_config['width'] );
		$this->settings[] = new Control_Setting( $this->settings_config['help'] );
		$this->settings[] = new Control_Setting(
			[
				'name'     => 'default',
				'label'    => __( 'Default Value', 'block-lab' ),
				'type'     => 'checkbox',
				'default'  => '0',
				'sanitize' => [ $this, 'sanitize_checkbox' ],
			]
		);
	}
}
