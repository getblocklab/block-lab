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
			'admin_menu', function () {
				add_menu_page(
					__( 'Advanced Custom Blocks', 'acb' ),
					__( 'Custom Blocks', 'acb' ),
					'manage_options',
					'acb',
					array( $this, 'render' ),
					$this->plugin->get_assets_url( 'images/admin-menu-icon.svg' )
				);
			}
		);
		// Register other hooks here.
	}

	/**
	 * Render the Menu Page.
	 *
	 * @return void
	 */
	public function render() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Advanced Custom Blocks Settings', 'advanced-custom-blocks' ); ?></h2>
			<p class="description"><?php esc_html_e( 'This is a description for this page.', 'advanced-custom-blocks' ); ?></p>
		</div>
		<?php
	}
}
