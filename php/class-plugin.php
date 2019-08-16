<?php
/**
 * Primary plugin file.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab;

/**
 * Class Plugin
 */
class Plugin extends Plugin_Abstract {
	/**
	 * WP Admin resources.
	 *
	 * @var Admin\Admin
	 */
	public $admin;

	/**
	 * Execute this once plugins are loaded. (not the best place for all hooks)
	 */
	public function plugin_loaded() {
		$this->admin = new Admin\Admin();
		$this->register_component( $this->admin );
	}
}
