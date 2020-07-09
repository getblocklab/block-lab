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
	 * The user capability to migrate posts and post content.
	 *
	 * @var string
	 */
	const MIGRATION_CAPABILITY = 'edit_others_posts';

	/**
	 * Adds the actions.
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_route_migrate_post_content' ] );
		add_action( 'rest_api_init', [ $this, 'register_route_migrate_post_type' ] );
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
					return current_user_can( self::MIGRATION_CAPABILITY );
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
					return current_user_can( self::MIGRATION_CAPABILITY );
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
}
