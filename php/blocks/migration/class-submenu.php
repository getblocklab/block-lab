<?php
/**
 * Migration submenu.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Migration;

use Block_Lab\Component_Abstract;

/**
 * Class Post_Type
 */
class Submenu extends Component_Abstract {

	/**
	 * The menu slug for the migration menu.
	 */
	const MENU_SLUG = 'block-lab-migration';

	/**
	 * Adds the actions.
	 */
	public function register_hooks() {
		add_action( 'admin_menu', [ $this, 'add_submenu_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Adds the submenu page for migration.
	 */
	public function add_submenu_page() {
		add_submenu_page(
			'edit.php?post_type=block_lab',
			__( 'Migrate to Genesis Custom Blocks', 'block-lab' ),
			__( 'Migrate', 'block-lab' ),
			'manage_options',
			self::MENU_SLUG,
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Adds the scripts for the submenu.
	 */
	public function enqueue_scripts() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		// Only enqueue if on the migration page.
		if ( self::MENU_SLUG === $page ) {
			wp_enqueue_style(
				self::MENU_SLUG,
				block_lab()->get_url( 'css/admin.migration.css' ),
				[],
				block_lab()->get_version()
			);

			$script_config = require block_lab()->get_path( 'js/admin.migration.asset.php' );
			wp_enqueue_script(
				self::MENU_SLUG,
				block_lab()->get_url( 'js/admin.migration.js' ),
				$script_config['dependencies'],
				$script_config['version'],
				true
			);
		}
	}

	/**
	 * Renders the submenu page.
	 */
	public function render_page() {
		echo '<div class="bl-migration__content"></div>';
	}
}
