<?php
/**
 * WP Admin resources.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab;

/**
 * Class Admin
 */
class Admin extends Component_Abstract {
	/**
	 * Display Pro version messaging
	 *
	 * @var bool
	 */
	public $show_pro;

	/**
	 * Initialise the Admin component.
	 */
	public function init() {
		$this->show_pro = apply_filters( 'block_lab_show_pro', false );
		if ( $this->show_pro ) {
			block_lab()->register_component( new Settings() );
		}
	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
	}
}
