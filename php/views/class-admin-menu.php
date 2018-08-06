<?php
/**
 * Admin Menu.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Advanced_Custom_Blocks\Views;

use Advanced_Custom_Blocks\Component_Abstract;

/**
 * Class AdminMenu
 */
class Admin_Menu extends Component_Abstract {

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
