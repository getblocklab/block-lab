<?php
/**
 * Deprecated functions.
 *
 * Deprecated methods can also appear as functions here, with the format namespace__class__method().
 *
 * @see Block_Lab\Component_Abstract->_call()
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Show a PHP error to warn developers using deprecated functions.
 *
 * @param string $function    The function that was called.
 * @param string $version     The version of Block Lab that deprecated the function.
 * @param string $replacement The function that should have been called.
 */
function block_lab_deprecated_function( $function, $version, $replacement ) {
	if ( WP_DEBUG && apply_filters( 'deprecated_function_trigger_error', true ) ) {
		trigger_error( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			esc_html(
				sprintf(
					// translators: (1) a function name, (2) a version number, (3) a function name.
					__( '%1$s is deprecated since Block Lab version %2$s! Use %3$s instead.', 'block-lab' ),
					$function,
					$version,
					$replacement
				)
			)
		);
	}
}
