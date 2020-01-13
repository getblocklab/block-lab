<?php
/**
 * Block Lab Settings.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;

/**
 * Class Settings
 */
class Settings extends Component_Abstract {

	/**
	 * Page slug.
	 *
	 * @var string
	 */
	public $slug = 'block-lab-settings';

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'admin_menu', [ $this, 'add_submenu_pages' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_notices', [ $this, 'show_notices' ] );
	}

	/**
	 * Enqueue scripts and styles used by the Settings screen.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		// Enqueue scripts and styles on the edit screen of the Block post type.
		if ( $this->slug === $page ) {
			wp_enqueue_style(
				$this->slug,
				$this->plugin->get_url( 'css/admin.settings.css' ),
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
			'edit.php?post_type=' . block_lab()->get_post_type_slug(),
			__( 'Block Lab Settings', 'block-lab' ),
			__( 'Settings', 'block-lab' ),
			'manage_options',
			$this->slug,
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Register Block Lab settings.
	 */
	public function register_settings() {
		register_setting( 'block-lab-license-key', 'block_lab_license_key' );
	}

	/**
	 * Render the Settings page.
	 */
	public function render_page() {
		?>
		<div class="wrap block-lab-settings">
			<?php
			$this->render_page_header();
			include block_lab()->get_path() . 'php/views/license.php';
			?>
		</div>
		<?php
	}

	/**
	 * Render the Settings page header.
	 */
	public function render_page_header() {
		?>
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'license' ) ); ?>" title="<?php esc_attr_e( 'License', 'block-lab' ); ?>" class="nav-tab nav-tab-active dashicons-before dashicons-nametag">
				<?php esc_html_e( 'License', 'block-lab' ); ?>
			</a>
			<a href="https://getblocklab.com/docs/" target="_blank" class="nav-tab dashicons-before dashicons-info">
				<?php esc_html_e( 'Documentation', 'block-lab' ); ?>
			</a>
			<a href="https://wordpress.org/support/plugin/block-lab/" target="_blank" class="nav-tab dashicons-before dashicons-sos">
				<?php esc_html_e( 'Help', 'block-lab' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Prepare notices to be displayed after saving the settings.
	 *
	 * @param string $notice The notice text to display.
	 */
	public function prepare_notice( $notice ) {
		$notices   = get_option( 'block_lab_notices', [] );
		$notices[] = $notice;
		update_option( 'block_lab_notices', $notices );
	}

	/**
	 * Show any admin notices after saving the settings.
	 */
	public function show_notices() {
		$notices = get_option( 'block_lab_notices', [] );

		if ( empty( $notices ) || ! is_array( $notices ) ) {
			return;
		}

		foreach ( $notices as $notice ) {
			echo wp_kses_post( $notice );
		}

		delete_option( 'block_lab_notices' );
	}
}
