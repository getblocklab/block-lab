<?php
/**
 * Block Post Type.
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace AdvancedCustomBlocks\View;

use AdvancedCustomBlocks\ComponentAbstract;

/**
 * Class Plugin
 */
class BlockPostType extends ComponentAbstract {

	/**
	 * Slug used for the custom post type.
	 *
	 * @var String
	 */
	public $slug = 'acb_block';

	/**
	 * Register any hooks that this component needs.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'init', array( $this, 'register_post_type' ) );
	}

	/**
	 * Register the custom post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Custom Blocks', 'post type general name', 'acb' ),
			'singular_name'      => _x( 'Custom Block', 'post type singular name', 'acb' ),
			'menu_name'          => _x( 'Custom Blocks', 'admin menu', 'acb' ),
			'name_admin_bar'     => _x( 'Custom Block', 'add new on admin bar', 'acb' ),
			'add_new'            => _x( 'Add New', 'book', 'acb' ),
			'add_new_item'       => __( 'Add New Custom Block', 'acb' ),
			'new_item'           => __( 'New Custom Block', 'acb' ),
			'edit_item'          => __( 'Edit Custom Block', 'acb' ),
			'view_item'          => __( 'View Custom Block', 'acb' ),
			'all_items'          => __( 'All Custom Blocks', 'acb' ),
			'search_items'       => __( 'Search Custom Blocks', 'acb' ),
			'parent_item_colon'  => __( 'Parent Custom Blocks:', 'acb' ),
			'not_found'          => __( 'No custom blocks found.', 'acb' ),
			'not_found_in_trash' => __( 'No custom blocks found in Trash.', 'acb' )
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => '?page=acb',
			'query_var'       => true,
			'rewrite'         => array( 'slug' => 'acb_block' ),
			'capability_type' => 'post',
			'supports'        => array( 'title' )
		);

		register_post_type( $this->slug, $args );
	}
}
