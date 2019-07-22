<?php
/**
 * Repeater control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Repeater
 */
class Repeater extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'repeater';

	/**
	 * Field variable type.
	 *
	 * This has the names of the rows.
	 * For example, they might be [ '13513afd624bc', '641be16fc1a', 'ad61ce1531cs' ].
	 *
	 * @var string[]
	 */
	public $type = 'array';

	/**
	 * Repeater constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Repeater', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting( $this->settings_config['help'] );
	}
}
