<?php
/**
 * Plugin Autoloader
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Register Autoloader
 */
spl_autoload_register( function ( $class ) {

	// Assume we're using namespaces (because that's how the plugin is structured).
	$namespace = explode( '\\', $class );
	$root      = array_shift( $namespace );

	// If a class ends with "Trait" then prefix the filename with 'trait-', else use 'class-'.
	$class_trait = preg_match( '/Trait$/', $class ) ? 'trait-' : 'class-';

	// If we're not in the plugin's namespace then just return.
	if ( 'AdvancedCustomBlocks' !== $root ) {
		return;
	}

	// Class name is the last part of the FQN.
	$class_name = array_pop( $namespace );

	// Remove "Trait" from the class name.
	if ( 'trait-' === $class_trait ) {
		$class_name = str_replace( 'Trait', '', $class_name );
	}

	// For file naming, the namespace is everything but the class name and the root namespace.
	$namespace = trim( implode( DIRECTORY_SEPARATOR, $namespace ) );

	// Get the path to our files.
	$directory = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '../php';
	if ( ! empty( $namespace ) ) {
		$directory .= DIRECTORY_SEPARATOR . $namespace;
	}

	// Because WordPress file naming conventions are odd.
	$file = strtolower( str_replace( '_', '-', $class_name ) );

	$file = $directory . DIRECTORY_SEPARATOR . $class_trait . $file . '.php';

	if ( file_exists( $file ) ) {
		require_once( $file );
	}
} );
