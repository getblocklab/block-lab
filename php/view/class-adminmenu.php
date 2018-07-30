<?php
/**
 * AdminMenu.
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, The Author
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
					'Advanced Custom Blocks',
					'Advanced Custom Blocks',
					'manage_options',
					'advanced-custom-blocks-menu',
					array( $this, 'render' ),
					$this->plugin->get_assets_url( 'images/admin-menu-icon.svg' )
				);
			}
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_menu_style' ) );
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

	/**
	 * Fix for extra padding applied to custom menu icons.
	 *
	 * @return void
	 */
	public function admin_menu_style() {
		$custom_css = '#adminmenu .toplevel_page_acb-menu .wp-menu-image img { padding-top: 0; }';
		wp_add_inline_style( 'admin-menu', $custom_css );
	}
}
