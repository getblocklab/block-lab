<?php
/**
 * Plugin interface.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab;

/**
 * Interface Plugin_Interface
 */
interface Plugin_Interface {

	/**
	 * Get the plugin basename.
	 *
	 * @return string The basename.
	 */
	public function get_basename();

	/**
	 * Set the plugin basename.
	 *
	 * @param string $basename The basename.
	 *
	 * @return Plugin_Interface The plugin instance.
	 */
	public function set_basename( $basename );

	/**
	 * Get the plugin's directory.
	 *
	 * @return string The directory.
	 */
	public function get_directory();

	/**
	 * Set the plugin's directory.
	 *
	 * @param string $directory The directory.
	 *
	 * @return Plugin_Interface The plugin instance.
	 */
	public function set_directory( $directory );

	/**
	 * Get the relative path to the plugin's directory.
	 *
	 * @param string $path Relative path to return.
	 *
	 * @return string The path.
	 */
	public function get_path( $path = '' );

	/**
	 * Get the plugin file.
	 *
	 * @return string The file.
	 */
	public function get_file();

	/**
	 * Set the plugin file.
	 *
	 * @param string $file The plugin file.
	 *
	 * @return Plugin_Interface The plugin instance.
	 */
	public function set_file( $file );

	/**
	 * Get the plugin's slug.
	 *
	 * @return string The slug.
	 */
	public function get_slug();

	/**
	 * Set the plugin's slug.
	 *
	 * @param string $slug The slug.
	 *
	 * @return Plugin_Interface The plugin instance.
	 */
	public function set_slug( $slug );

	/**
	 * Get the relative url.
	 *
	 * @param string $path The relative url to get.
	 *
	 * @return string The url.
	 */
	public function get_url( $path = '' );

	/**
	 * Set the plugin's url.
	 *
	 * @param string $url The url.
	 *
	 * @return Plugin_Interface The plugin instance.
	 */
	public function set_url( $url );

	/**
	 * Get the plugin's version.
	 *
	 * @return string The version.
	 */
	public function get_version();

	/**
	 * Set the plugin's version, based on the file.
	 *
	 * @param string $file The absolute path to the plugin file.
	 *
	 * @return Plugin_Interface The plugin instance.
	 */
	public function set_version( $file );

	/**
	 * Get url relative to assets url.
	 *
	 * @param string $path The relative url to get.
	 *
	 * @return string The url.
	 */
	public function get_assets_url( $path = '' );

	/**
	 * Get the relative path to the assets directory.
	 *
	 * @param string $path Relative path to return.
	 *
	 * @return string The path.
	 */
	public function get_assets_path( $path = '' );

	/**
	 * Register a new Component.
	 *
	 * @param Component_Interface $component The new component.
	 *
	 * @return Plugin_Interface The plugin instance.
	 */
	public function register_component( Component_Interface $component );

	/**
	 * Runs once 'plugins_loaded' hook fires.
	 *
	 * @return void Nothing to return.
	 */
	public function plugin_loaded();
}
