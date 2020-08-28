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
use Block_Lab\Admin\License;

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
	const QUERY_VAR_DEACTIVATE_AND_GCB_PAGE = 'bl_deactivate_and_gcb';

	/**
	 * The query var to deactivate this plugin and activate the new one.
	 *
	 * @var string
	 */
	const NONCE_ACTION_DEACTIVATE = 'deactivate_bl_and_activate_new';

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

			$gcb_url = add_query_arg(
				[
					self::QUERY_VAR_DEACTIVATE_AND_GCB_PAGE => true,
					'_wpnonce' => wp_create_nonce( self::NONCE_ACTION_DEACTIVATE ),
				],
				admin_url()
			);

			$is_pro                       = block_lab()->is_pro();
			$genesis_pro_subscription_key = get_option( Subscription_Api::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY );
			$script_data                  = [
				'isPro'  => $is_pro,
				'gcbUrl' => $gcb_url,
			];

			if ( $genesis_pro_subscription_key ) {
				$script_data['genesisProKey'] = $genesis_pro_subscription_key;
			}

			wp_add_inline_script(
				self::MENU_SLUG,
				'const blockLabMigration = ' . wp_json_encode( $script_data ) . ';',
				'before'
			);
		}
	}

	/**
	 * Gets whether the current user can view the migration page.
	 *
	 * @return bool Whether the user can view the migration page.
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
	 * Conditionally deactivates this plugin and goes to the Genesis Custom Blocks page.
	 *
	 * The logic to deactivate the plugin was mainly copied from Core.
	 * https://github.com/WordPress/wordpress-develop/blob/61803a37a41eca95efe964c7e02c768de6df75fa/src/wp-admin/plugins.php#L196-L221
	 */
	public function maybe_activate_plugin() {
		$previous_plugin_file = 'block-lab/block-lab.php';

		if ( empty( $_GET[ self::QUERY_VAR_DEACTIVATE_AND_GCB_PAGE ] ) ) {
			return;
		}

		if ( ! current_user_can( 'deactivate_plugin', $previous_plugin_file ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to deactivate this plugin.', 'block-lab' ) );
		}

		check_admin_referer( self::NONCE_ACTION_DEACTIVATE );

		if ( ! is_network_admin() && is_plugin_active_for_network( $previous_plugin_file ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to deactivate this network-active plugin.', 'block-lab' ) );
		}

		deactivate_plugins( $previous_plugin_file, false, is_network_admin() );

		if ( ! is_network_admin() ) {
			update_option( 'recently_activated', [ $previous_plugin_file => time() ] + (array) get_option( 'recently_activated' ) );
		} else {
			update_site_option( 'recently_activated', [ $previous_plugin_file => time() ] + (array) get_site_option( 'recently_activated' ) );
		}

		// Go to the Genesis Custom Blocks page.
		wp_safe_redirect(
			add_query_arg(
				'post_type',
				'genesis_custom_block',
				admin_url( 'edit.php' )
			)
		);
	}
}
