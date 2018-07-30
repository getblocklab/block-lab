<?php
/**
 * MySQL settings
 *
 * This configuration file will be used by the copy of WordPress being tested.
 * wordpress/wp-config.php will be ignored.
 *
 * WARNING WARNING WARNING!
 *
 * These tests will DROP ALL TABLES in the database with the prefix named below.
 * DO NOT use a production database or one that is shared with something else.
 *
 * @package WPTide
 */

/* Path to the WordPress codebase you'd like to test. Add a backslash in the end. */
define( 'ABSPATH', '../../../../core/' );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

define( 'DB_HOST', 'tests-mysql' );
define( 'DB_NAME', 'wptests' );
define( 'DB_USER', 'wptests' );
define( 'DB_PASSWORD', 'wptests' );

define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// Only numbers, letters, and underscores please!
$table_prefix = 'wptests_';

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
