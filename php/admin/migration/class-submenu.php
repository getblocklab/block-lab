<?php
/**
 * Migration submenu.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin\Migration;

use Block_Lab\Component_Abstract;

/**
 * Class Post_Type
 */
class Submenu extends Component_Abstract {

	/**
	 * The menu slug for the migration menu.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'block-lab-migration';

	/**
	 * The user capability to migrate posts and post content.
	 *
	 * @var string
	 */
	const MIGRATION_CAPABILITY = 'edit_others_posts';

	/**
	 * The query var to deactivate this plugin and activate the new one.
	 *
	 * @var string
	 */
	const QUERY_VAR_DEACTIVATE_AND_ACTIVATE = 'bl_deactivate_and_activate';

	/**
	 * The query var to deactivate this plugin and activate the new one.
	 *
	 * @var string
	 */
	const NONCE_ACTION_DEACTIVATE_AND_ACTIVATE = 'deactivate_bl_and_activate_new';

	/**
	 * Adds the actions.
	 */
	public function register_hooks() {
		add_action( 'admin_menu', [ $this, 'add_submenu_page' ], 9 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_bar_init', [ $this, 'maybe_activate_plugin' ] );
	}

	/**
	 * Adds the submenu page for migration.
	 */
	public function add_submenu_page() {
		if ( $this->user_can_view_migration_page() ) {
			add_submenu_page(
				'edit.php?post_type=block_lab',
				__( 'Migrate to Genesis Custom Blocks', 'block-lab' ),
				__( 'Migrate', 'block-lab' ),
				'manage_options',
				self::MENU_SLUG,
				[ $this, 'render_page' ]
			);
		}
	}

	/**
	 * Adds the scripts for the submenu.
	 */
	public function enqueue_scripts() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		// Only enqueue if on the migration page.
		if ( self::MENU_SLUG === $page && $this->user_can_view_migration_page() ) {
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

			$activate_url = add_query_arg(
				[
					self::QUERY_VAR_DEACTIVATE_AND_ACTIVATE => true,
					'_wpnonce' => wp_create_nonce( self::NONCE_ACTION_DEACTIVATE_AND_ACTIVATE ),
				],
				admin_url()
			);

			wp_add_inline_script(
				self::MENU_SLUG,
				'const blockLabMigration = ' . wp_json_encode(
					[
						'isPro'       => block_lab()->is_pro(),
						'activateUrl' => $activate_url,
					]
				),
				'before'
			);
		}
	}

	/**
	 * Gets whether the current user can view the migration page.
	 */
	public function user_can_view_migration_page() {
		return current_user_can( 'install_plugins' ) && current_user_can( self::MIGRATION_CAPABILITY );
	}

	/**
	 * Renders the submenu page.
	 */
	public function render_page() {
		echo '<div class="bl-migration__content"></div>';
	}

	/**
	 * Conditionally deactivates this plugin and activates Genesis Custom Blocks.
	 *
	 * The logic to deactivate the plugin was mainly copied from Core.
	 * https://github.com/WordPress/wordpress-develop/blob/61803a37a41eca95efe964c7e02c768de6df75fa/src/wp-admin/plugins.php#L196-L221
	 */
	public function maybe_activate_plugin() {
		$previous_plugin_file = 'block-lab/block-lab.php';
		$new_plugin_file      = 'genesis-custom-blocks/genesis-custom-blocks.php';

		if ( empty( $_GET[ self::QUERY_VAR_DEACTIVATE_AND_ACTIVATE ] ) ) {
			return;
		}

		if ( ! current_user_can( 'deactivate_plugin', $previous_plugin_file ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to deactivate this plugin.', 'block-lab' ) );
		}

		check_admin_referer( self::NONCE_ACTION_DEACTIVATE_AND_ACTIVATE );

		if ( ! is_network_admin() && is_plugin_active_for_network( $previous_plugin_file ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to deactivate this network-active plugin.', 'block-lab' ) );
		}

		deactivate_plugins( $previous_plugin_file, false, is_network_admin() );

		if ( ! is_network_admin() ) {
			update_option( 'recently_activated', [ $previous_plugin_file => time() ] + (array) get_option( 'recently_activated' ) );
		} else {
			update_site_option( 'recently_activated', [ $previous_plugin_file => time() ] + (array) get_site_option( 'recently_activated' ) );
		}

		// Activate the new plugin.
		wp_safe_redirect(
			add_query_arg(
				[
					'action'        => 'activate',
					'plugin'        => rawurlencode( $new_plugin_file ),
					'plugin_status' => 'all',
					'paged'         => 1,
					'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $new_plugin_file ),
				],
				admin_url( 'plugins.php' )
			)
		);
	}
}
