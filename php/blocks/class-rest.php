<?php
/**
 * REST API handling.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks;

use Block_Lab\Component_Abstract;

/**
 * Class Block
 */
class Rest extends Component_Abstract {

	/**
	 * Register all the hooks.
	 */
	public function register_hooks() {
		add_action( 'rest_endpoints', [ $this, 'filter_block_endpoints' ] );
	}

	/**
	 * Filters the Block Lab endpoints to allow POST requests.
	 *
	 * @param array $endpoints The REST API endpoints, an associative array of $route => $handlers.
	 * @return array The filtered endpoints, with the Block Lab endpoints allowing POST requests.
	 */
	public function filter_block_endpoints( $endpoints ) {
		foreach ( $endpoints as $route => $handler ) {
			if ( 0 === strpos( $route, '/wp/v2/block-renderer/(?P<name>block-lab/' ) && isset( $endpoints[ $route ][0] ) ) {
				$endpoints[ $route ][0]['methods']  = [ 'GET', 'POST' ];
				$endpoints[ $route ][0]['callback'] = [ $this, 'get_item' ];
			}
		}

		return $endpoints;
	}

	/**
	 * Returns block output from block's registered render_callback.
	 *
	 * Forked from WP_REST_Block_Renderer_Controller::get_item(), with a simple change to process POST requests.
	 *
	 * @todo: revert this if this has been merged and enough version of Core have passed: https://github.com/WordPress/wordpress-develop/pull/196/
	 * @see https://github.com/WordPress/wordpress-develop/blob/dfa959bbd58f13b504e269aad45412a85f74e491/src/wp-includes/rest-api/endpoints/class-wp-rest-block-renderer-controller.php#L121
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$post_id = isset( $request['post_id'] ) ? intval( $request['post_id'] ) : 0;

		if ( 0 < $post_id ) {
			$GLOBALS['post'] = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

			// Set up postdata since this will be needed if post_id was set.
			setup_postdata( $GLOBALS['post'] );
		}
		$registry = \WP_Block_Type_Registry::get_instance();

		if ( null === $registry->get_registered( $request['name'] ) ) {
			return new WP_Error(
				'block_invalid',
				__( 'Invalid block.', 'block-lab' ),
				[
					'status' => 404,
				]
			);
		}

		$attributes = $request->get_param( 'attributes' );

		// Create an array representation simulating the output of parse_blocks.
		$block = [
			'blockName'    => $request['name'],
			'attrs'        => $attributes,
			'innerHTML'    => '',
			'innerContent' => [],
		];

		// Render using render_block to ensure all relevant filters are used.
		$data = [
			'rendered' => render_block( $block ),
		];

		return rest_ensure_response( $data );
	}
}
