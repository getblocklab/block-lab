<?php
/**
 * Block Lab Upgrade Page.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
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
	 * Initialise the Upgrade component.
	 */
	public function init() {
	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'add_submenu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'update_option_block_lab_license_key', array( $this, 'settings_redirect' ) );
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
				array(),
				$this->plugin->get_version()
			);
		}
	}

	/**
	 * Redirect to the Settings screen if the license is being saved.
	 */
	public function settings_redirect() {
		wp_safe_redirect(
			add_query_arg(
				array(
					'post_type' => 'block_lab',
					'page'      => 'block-lab-settings',
					'tab'       => 'license',
				),
				admin_url( 'edit.php' )
			)
		);
		die();
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
			array( $this, 'render_page' )
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
