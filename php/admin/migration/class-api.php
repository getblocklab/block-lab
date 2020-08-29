<?php
/**
 * Migration REST API endpoints.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin\Migration;

use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;
use WP_Error;
use WP_REST_Response;
use Block_Lab\Component_Abstract;

/**
 * Class Post_Type
 */
class Api extends Component_Abstract {

	/**
	 * Adds the actions.
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_route_install_gcb' ] );
		add_action( 'rest_api_init', [ $this, 'register_route_migrate_post_content' ] );
		add_action( 'rest_api_init', [ $this, 'register_route_migrate_post_type' ] );
	}

	/**
	 * Registers a route to install and activate the plugin Genesis Custom Blocks.
	 */
	public function register_route_install_gcb() {
		register_rest_route(
			block_lab()->get_slug(),
			'install-activate-gcb',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_install_gcb_response' ],
				'permission_callback' => function() {
					return current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' );
				},
			]
		);
	}

	/**
	 * Installs and activates Genesis Custom Blocks, and returns the result.
	 *
	 * @param array $data Data sent in the POST request.
	 * @return WP_REST_Response|WP_Error Response to the request.
	 */
	public function get_install_gcb_response( $data ) {
		unset( $data );

		$installation_result = $this->install_plugin();
		if ( is_wp_error( $installation_result ) ) {
			return $installation_result;
		}

		$activation_result = $this->activate_plugin();
		if ( is_wp_error( $activation_result ) ) {
			return $activation_result;
		}

		return rest_ensure_response( [ 'message' => __( 'Plugin installed and activated', 'block-lab' ) ] );
	}

	/**
	 * Installs the new plugin.
	 *
	 * Mainly copied from Gutenberg, with slight changes.
	 * The main change being that it returns true
	 * if the plugin is already downloaded, not a WP_Error.
	 *
	 * @see https://github.com/WordPress/gutenberg/blob/fef0445bf47adc6c8d8b69e19616feb8b6de8c2e/lib/class-wp-rest-plugins-controller.php#L271-L369
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	private function install_plugin() {
		global $wp_filesystem;

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

		// Check if the plugin is already installed.
		if ( array_key_exists( $this->get_new_plugin_file(), get_plugins() ) ) {
			return true;
		}

		// Verify filesystem is accessible first.
		$filesystem_available = $this->is_filesystem_available();
		if ( is_wp_error( $filesystem_available ) ) {
			return $filesystem_available;
		}

		$download_link = $this->get_download_link();
		if ( is_wp_error( $download_link ) ) {
			return $download_link;
		}

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$result = $upgrader->install( $download_link );

		if ( is_wp_error( $result ) ) {
			$result->add_data( [ 'status' => 500 ] );

			return $result;
		}

		// This should be the same as $result above.
		if ( is_wp_error( $skin->result ) ) {
			$skin->result->add_data( [ 'status' => 500 ] );

			return $skin->result;
		}

		if ( $skin->get_errors()->has_errors() ) {
			$error = $skin->get_errors();
			$error->add_data( [ 'status' => 500 ] );

			return $error;
		}

		if ( is_null( $result ) ) {
			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && isset( $wp_filesystem->errors ) && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
				return new WP_Error( 'unable_to_connect_to_filesystem', $wp_filesystem->errors->get_error_message(), [ 'status' => 500 ] );
			}

			return new WP_Error( 'unable_to_connect_to_filesystem', __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'block-lab' ), [ 'status' => 500 ] );
		}

		$file = $upgrader->plugin_info();
		if ( ! $file ) {
			return new WP_Error( 'unable_to_determine_installed_plugin', __( 'Unable to determine what plugin was installed.', 'block-lab' ), [ 'status' => 500 ] );
		}

		return true;
	}

	/**
	 * Determines if the filesystem is available.
	 *
	 * Only the 'Direct' filesystem transport, and SSH/FTP when credentials are stored are supported at present.
	 * Copied from Gutenberg.
	 *
	 * @see https://github.com/WordPress/gutenberg/blob/8d64aa3092d5d9e841895bf2d495565c9a770238/lib/class-wp-rest-plugins-controller.php#L799-L815
	 *
	 * @return true|WP_Error True if filesystem is available, WP_Error otherwise.
	 */
	private function is_filesystem_available() {
		if ( 'direct' === get_filesystem_method() ) {
			return true;
		}

		ob_start();
		$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
		ob_end_clean();

		if ( $filesystem_credentials_are_stored ) {
			return true;
		}

		return new WP_Error( 'fs_unavailable', __( 'The filesystem is currently unavailable for managing plugins.', 'block-lab' ), [ 'status' => 500 ] );
	}

	/**
	 * Gets the GCB Pro download link.
	 *
	 * @return string|WP_Error The download link, or a WP_Error.
	 */
	public function get_download_link() {
		if ( ! empty( get_transient( Subscription_Api::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK ) ) ) {
			return get_transient( Subscription_Api::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK );
		} else {
			$api = plugins_api(
				'plugin_information',
				[
					'slug'   => 'genesis-custom-blocks',
					'fields' => [
						'sections' => false,
					],
				]
			);

			if ( is_wp_error( $api ) ) {
				if ( false !== strpos( $api->get_error_message(), 'Plugin not found.' ) ) {
					$api->add_data( [ 'status' => 404 ] );
				} else {
					$api->add_data( [ 'status' => 500 ] );
				}

				return $api;
			}

			if ( empty( $api->download_link ) ) {
				return new WP_Error(
					'no_download_link',
					__( 'There was no download_link in the API', 'block-lab' )
				);
			}

			return $api->download_link;
		}
	}

	/**
	 * Activates the new plugin.
	 *
	 * Mainly copied from Gutenberg's WP_REST_Plugins_Controller::handle_plugin_status().
	 *
	 * @see https://github.com/WordPress/gutenberg/blob/fef0445bf47adc6c8d8b69e19616feb8b6de8c2e/lib/class-wp-rest-plugins-controller.php#L679-L709
	 *
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	private function activate_plugin() {
		$activation_result = activate_plugin( $this->get_new_plugin_file(), '', false, true );
		if ( is_wp_error( $activation_result ) ) {
			$activation_result->add_data( [ 'status' => 500 ] );
			return $activation_result;
		}

		return true;
	}

	/**
	 * Registers a route to migrate the post content to the new namespace.
	 */
	public function register_route_migrate_post_content() {
		register_rest_route(
			block_lab()->get_slug(),
			'migrate-post-content',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_migrate_post_content_response' ],
				'permission_callback' => function() {
					return current_user_can( Submenu::MIGRATION_CAPABILITY );
				},
			]
		);
	}

	/**
	 * Gets the REST API response for the post content migration.
	 *
	 * @return WP_REST_Response The response to the request.
	 */
	public function get_migrate_post_content_response() {
		return rest_ensure_response( ( new Post_Content( 'block-lab', 'genesis-custom-blocks' ) )->migrate_all() );
	}

	/**
	 * Registers a route to migrate the post type.
	 */
	public function register_route_migrate_post_type() {
		register_rest_route(
			block_lab()->get_slug(),
			'migrate-post-type',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_migrate_post_type_response' ],
				'permission_callback' => function() {
					return current_user_can( Submenu::MIGRATION_CAPABILITY );
				},
			]
		);
	}

	/**
	 * Gets the REST API response for the post type migration.
	 *
	 * @return WP_REST_Response The response to the request.
	 */
	public function get_migrate_post_type_response() {
		return rest_ensure_response( ( new Post_Type( 'block_lab', 'block-lab', 'block_lab', 'genesis_custom_block', 'genesis-custom-blocks', 'genesis_custom_blocks' ) )->migrate_all() );
	}

	/**
	 * Gets the directory and file of the new plugin to install.
	 *
	 * @return string The plugin file.
	 */
	public function get_new_plugin_file() {
		if ( ! empty( get_transient( Subscription_Api::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK ) ) ) {
			return 'genesis-custom-blocks-pro/genesis-custom-blocks-pro.php';
		}

		return 'genesis-custom-blocks/genesis-custom-blocks.php';
	}
}
