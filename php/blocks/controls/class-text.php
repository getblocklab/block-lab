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
	 * Control label.
	 *
	 * @var string
	 */
	public $label = '';

	public function __construct() {
		$this->label = __( 'Text', 'advanced-custom-blocks' );
	}

	/**
	 * Output the control options.
	 */
	public function render_options() {
	}
}
