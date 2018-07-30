<?php
/**
 * Component interface.
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace AdvancedCustomBlocks;

/**
 * Interface ComponentInterface
 */
interface ComponentInterface {

	/**
	 * Set the plugin so that it can be referenced later.
	 *
	 * @param PluginInterface $plugin The plugin.
	 *
	 * @return ComponentInterface $this
	 */
	public function set_plugin( PluginInterface $plugin );

	/**
	 * Register any hooks that this component needs.
	 *
	 * @return void
	 */
	public function register_hooks();
}
