<?php
/**
 * Admin Menu.
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace AdvancedCustomBlocks\View;

use AdvancedCustomBlocks\ComponentAbstract;

/**
 * Class AdminMenu
 */
class AdminMenu extends ComponentAbstract {

	/**
	 * Register hooks for this view.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action(
			'admin_menu', function() {
				add_menu_page(
					__( 'Advanced Custom Blocks', 'advanced-custom-blocks' ),
					__( 'Custom Blocks', 'advanced-custom-blocks' ),
					'manage_options',
					'acb',
					null,
					'data:image/svg+xml;base64,' . base64_encode(
						file_get_contents(
							$this->plugin->get_assets_path( 'images/admin-menu-icon.svg' )
						)
					)
				);
			}
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_menu_style' ) );
		// Register other hooks here.
	}

	/**
	 * Fix for extra padding applied to custom menu icons.
	 *
	 * @return void
	 */
	public function admin_menu_style() {
		$custom_css = '#adminmenu .toplevel_page_acb .wp-menu-image img { padding-top: 0; }';
		wp_add_inline_style( 'admin-menu', $custom_css );
	}
}
