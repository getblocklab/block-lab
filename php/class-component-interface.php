<?php
/**
 * Component interface.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab;

/**
 * Interface ComponentInterface
 */
interface Component_Interface {

	/**
	 * Set the plugin so that it can be referenced later.
	 *
	 * @param Plugin_Interface $plugin The plugin.
	 *
	 * @return Component_Interface $this
	 */
	public function set_plugin( Plugin_Interface $plugin );

	/**
	 * Register any hooks that this component needs.
	 *
	 * @return void
	 */
	public function register_hooks();
}
