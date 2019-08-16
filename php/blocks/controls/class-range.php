<?php
/**
 * Range control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

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
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'integer';

	/**
	 * Range constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Range', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting( $this->settings_config['location'] );
		$this->settings[] = new Control_Setting( $this->settings_config['help'] );
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'min',
				'label'    => __( 'Minimum Value', 'block-lab' ),
				'type'     => 'number',
				'default'  => '',
				'sanitize' => array( $this, 'sanitize_number' ),
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'max',
				'label'    => __( 'Maximum Value', 'block-lab' ),
				'type'     => 'number',
				'default'  => '',
				'sanitize' => array( $this, 'sanitize_number' ),
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'step',
				'label'    => __( 'Step Size', 'block-lab' ),
				'type'     => 'number_non_negative',
				'default'  => 1,
				'sanitize' => array( $this, 'sanitize_number' ),
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'default',
				'label'    => __( 'Default Value', 'block-lab' ),
				'type'     => 'number',
				'default'  => '',
				'sanitize' => array( $this, 'sanitize_number' ),
			)
		);
	}
}
