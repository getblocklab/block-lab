<?php
/**
 * Migration REST API endpoints.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin\Migration;

use WP_REST_Response;
use Block_Lab\Component_Abstract;

/**
 * Class Post_Type
 */
class Api extends Component_Abstract {

	/**
	 * The option name where the Genesis Pro subscription key is stored.
	 *
	 * @var string
	 */
	const OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY = 'genesis_pro_subscription_key';

	/**
	 * Adds the actions.
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_route_migrate_post_content' ] );
		add_action( 'rest_api_init', [ $this, 'register_route_migrate_post_type' ] );
		add_action( 'rest_api_init', [ $this, 'register_route_update_subscription_key' ] );
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
	 * Registers a route to migrate the post type.
	 */
	public function register_route_update_subscription_key() {
		register_rest_route(
			block_lab()->get_slug(),
			'update-subscription-key',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'get_update_subscription_key_response' ],
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
			]
		);
	}

	/**
	 * Gets the REST API response to the request to update the subscription key.
	 *
	 * @param array $data       Data sent in the POST request.
	 * @return WP_REST_Response Response to the request.
	 */
	public function get_update_subscription_key_response( $data ) {
		$key = 'subscriptionKey';
		if ( empty( $data[ $key ] ) ) {
			return rest_ensure_response( [ 'success' => false ] );
		}

		return rest_ensure_response(
			[
				'success' => update_option( self::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY, sanitize_key( $data[ $key ] ) ),
			]
		);
	}
}
