<?php
/**
 * Classic Text control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Classic_Text
 */
class Classic_Text extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'classic_text';

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Classic Text', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		foreach ( [ 'help', 'default' ] as $setting ) {
			$this->settings[] = new Control_Setting( $this->settings_config[ $setting ] );
		}
	}
}
