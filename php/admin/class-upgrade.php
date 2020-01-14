<?php
/**
 * Block Lab Upgrade Page.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;

/**
 * Class Upgrade
 */
class Upgrade extends Component_Abstract {

	/**
	 * Page slug.
	 *
	 * @var string
	 */
	public $slug = 'block-lab-pro';

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'admin_menu', [ $this, 'add_submenu_pages' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueue scripts and styles used by the Upgrade screen.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		// Enqueue scripts and styles on the edit screen of the Block post type.
		if ( $this->slug === $page ) {
			wp_enqueue_style(
				$this->slug,
				$this->plugin->get_url( 'css/admin.upgrade.css' ),
				[],
				$this->plugin->get_version()
			);
		}
	}

	/**
	 * Add submenu pages to the Block Lab menu.
	 */
	public function add_submenu_pages() {
		add_submenu_page(
			'edit.php?post_type=block_lab',
			__( 'Block Lab Pro', 'block-lab' ),
			__( 'Go Pro', 'block-lab' ),
			'manage_options',
			$this->slug,
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Render the Upgrade page.
	 */
	public function render_page() {
		?>
		<div class="wrap block-lab-pro">
			<h2 class="screen-reader-text"><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php include block_lab()->get_path() . 'php/views/upgrade.php'; ?>
		</div>
		<?php
	}
}
