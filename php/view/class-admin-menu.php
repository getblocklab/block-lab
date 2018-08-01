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
class Admin_Menu extends ComponentAbstract {

	/**
	 * Register hooks for this view.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action(
			'admin_menu', function () {
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
		// Register other hooks here.
	}
}
