<?php
/**
 * Handles migration to the new plugin.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;
use Block_Lab\Admin\Migration\Notice;
use Block_Lab\Admin\Migration\Submenu;

/**
 * Class Migration
 */
class Migration extends Component_Abstract {

	/**
	 * The migration notice.
	 *
	 * @var Notice
	 */
	private $notice;

	/**
	 * The migration submenu under the Block Lab menu item.
	 *
	 * @var Submenu
	 */
	private $submenu;

	/**
	 * Adds an action for the notice.
	 */
	public function init() {
		$this->notice = new Notice();
		block_lab()->register_component( $this->notice );

		$this->submenu = new Submenu();
		block_lab()->register_component( $this->submenu );
	}

	/**
	 * A stub for the hook registration method.
	 */
	public function register_hooks() {}
}
