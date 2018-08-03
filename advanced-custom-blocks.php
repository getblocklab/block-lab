<?php
/**
 * Advanced Custom Blocks
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, Rheinard Korf
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: Advanced Custom Blocks
 * Plugin URI: https://github.com/rheinardkorf/advanced-custom-blocks
 * Description: Create Advanced Custom Blocks effortlessly with no Gutenberg development know-how required.
 * Version: 0.1
 * Author: Rheinard Korf, Luke Carbis, Rob Stinson
 * Author URI: https://github.com/rheinardkorf/advanced-custom-blocks
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: advanced-custom-blocks
 * Domain Path: languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Setup the plugin auto loader.
require_once( 'php/autoloader.php' );

/**
 * Admin notice for incompatible versions of PHP.
 */
function advanced_custom_blocks_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( advanced_custom_blocks_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * "Namespace" is a PHP 5.3 introduced feature. This is a hard requirement
 * for the plugin structure.
 *
 * "Traits" is a PHP 5.4 introduced feature. Remove "Traits" support from
 * php/autoloader if you want to support a lower PHP version.
 * Remember to update the checked version below if you do.
 *
 * @return string
 */
function advanced_custom_blocks_php_version_text() {
	return __( 'Advanced Custom Blocks plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.4 or higher.', 'advanced-custom-blocks' );
}

// If the PHP version is too low, show warning and return.
if ( version_compare( phpversion(), '5.4', '<' ) ) {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( advanced_custom_blocks_php_version_text() );
	} else {
		add_action( 'admin_notices', 'advanced_custom_blocks_php_version_error' );
	}

	return;
}

// Load some helpers.
require_once __DIR__ . '/php/helpers.php';

/**
 * Get the plugin object.
 *
 * @return \AdvancedCustomBlocks\PluginInterface
 */
function advanced_custom_blocks() {
	static $instance;

	if ( null === $instance ) {
		$instance = new \AdvancedCustomBlocks\Plugin();
	}

	return $instance;
}

/**
 * Setup the plugin instance.
 */
advanced_custom_blocks()
	->set_basename( plugin_basename( __FILE__ ) )
	->set_directory( plugin_dir_path( __FILE__ ) )
	->set_file( __FILE__ )
	->set_slug( 'advanced-custom-blocks' )
	->set_url( plugin_dir_url( __FILE__ ) );

/**
 * Register plugin components.
 */
advanced_custom_blocks()
	->register_component( new \AdvancedCustomBlocks\View\Admin_Menu() )
	->register_component( new \AdvancedCustomBlocks\PostTypes\Block_Post_Type() )
	->register_component( new \AdvancedCustomBlocks\Blocks\Loader() )
	->register_component( new \AdvancedCustomBlocks\Endpoints\Preview() );

/**
 * Sometimes we need to do some things after the plugin is loaded, so call the PluginInterface::plugin_loaded().
 */
add_action( 'plugins_loaded', array( advanced_custom_blocks(), 'plugin_loaded' ) );
