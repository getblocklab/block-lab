<?php
/**
 * Testing Blocks
 *
 * @package Block_Lab
 *
 * Plugin Name: Testing Blocks
 * Plugin URI: https://github.com/getblocklab/block-lab
 * Author: Block Lab contributors
 */

// Make Block Lab look for templates in this plugin instead of a theme.
add_filter(
	'block_lab_template_path',
	static function( $path ) {
		unset( $path );
		return __DIR__;
	}
);
