<?php
/**
 * Primary plugin file.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
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
	 * Utility methods.
	 *
	 * @var Blocks\Utils
	 */
	public $utils;

	/**
	 * Execute this once plugins are loaded. (not the best place for all hooks)
	 */
	public function plugin_loaded() {
		$this->utils = new Blocks\Utils();
		$this->admin = new Admin\Admin();
		$this->register_component( $this->utils );
		$this->register_component( $this->admin );
	}

	/**
	 * Allows calling methods in the Utils class, directly in this class.
	 *
	 * When calling a method in this class that isn't defined, this calls it in $this->utils if it exists.
	 * For example, on calling ->example_method() in this class,
	 * this look for $this->utils->example_method().
	 *
	 * @param string $name      The name of the method called in this class.
	 * @param array  $arguments The arguments passed to the method.
	 * @return mixed The result of calling the utils function, if it exists.
	 */
	public function __call( $name, $arguments ) {
		if ( method_exists( $this->utils, $name ) ) {
			return call_user_func_array( array( $this->utils, $name ), $arguments );
		}
	}
}
