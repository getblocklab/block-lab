<?php
/**
 * PHPUnit bootstrap file
 *
 * @package PluginTest
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

// Travis CI & Vagrant SSH tests directory, this logic taken from wp-dev-lib.
if ( empty( $_tests_dir ) ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	$plugin_root = dirname( __DIR__ );
	require $plugin_root . '/vendor/autoload.php';
	require $plugin_root . '/vendor/antecedent/patchwork/Patchwork.php';
	require $plugin_root . '/tests/php/unit/helpers/trait-testing-helper.php';
	require $plugin_root . '/block-lab.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
