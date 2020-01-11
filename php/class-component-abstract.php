<?php
/**
 * Component abstract.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab;

/**
 * Class ComponentAbstract
 */
abstract class Component_Abstract implements Component_Interface {

	/**
	 * Point to the $plugin instance.
	 *
	 * @var Plugin_Interface
	 */
	protected $plugin;

	/**
	 * Set the plugin so that it can be referenced later.
	 *
	 * @param Plugin_Interface $plugin The plugin.
	 *
	 * @return Component_Interface $this
	 */
	public function set_plugin( Plugin_Interface $plugin ) {
		$this->plugin = $plugin;
		return $this;
	}

	/**
	 * Handle deprecated component methods.
	 *
	 * @param string $name      The name of the method called in this class.
	 * @param array  $arguments The arguments passed to the method.
	 *
	 * @return mixed The result of calling the deprecated method, if it exists.
	 *
	 * @throws \Error Fallback to a standard PHP error.
	 */
	public function __call( $name, $arguments ) {
		$class         = get_class( $this );
		$class_name    = strtolower( str_replace( '\\', '__', $class ) );
		$function_name = "${class_name}__${name}";

		if ( function_exists( $function_name ) ) {
			return call_user_func_array( $function_name, $arguments );
		}

		// Intentionally untranslated, to match PHP's error message.
		throw new \Error( "Call to undefined method $class::$name()" );
	}

	/**
	 * Register any hooks that this component needs.
	 *
	 * @return void
	 */
	abstract public function register_hooks();
}
