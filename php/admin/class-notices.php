<?php
/**
 * Block Lab Notices.
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
class Notices extends Component_Abstract {

	/**
	 * User Meta Key.
	 *
	 * @var string
	 */
	public $meta_key = 'block-lab-notices';

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_notice_endpoints' ) );
	}

	/**
	 * Register REST API endpoint for saving dismissed notices
	 */
	public function register_notice_endpoints() {
		register_rest_route(
			'block-lab/v1',
			'/notices/(?<id>[\w-]+)',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'rest_get_notice_status' ),
			)
		);

		register_rest_route(
			'block-lab/v1',
			'/notices/(?<id>[\w-]+)',
			array(
				'methods'  => \WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'rest_update_notice_status' ),
			)
		);
	}

	/**
	 * REST API Endpoint for retrieving a notice status
	 *
	 * @param array $request The arguments sent with the request.
	 * @return \WP_REST_Response
	 */
	public function rest_get_notice_status( $request ) {
		if ( ! isset( $request['id'] ) ) {
			return new \WP_Error(
				'block_lab_rest_no_id',
				esc_html__( 'Something went horribly wrong.', 'block-lab' ),
				array( 'status' => 500 )
			);
		}

		$notice_status = $this->get_notice_status( $request['id'] );
		return rest_ensure_response( $notice_status );
	}

	/**
	 * REST API Endpoint for retrieving a notice status
	 *
	 * @param array $request The arguments sent with the request.
	 * @return \WP_REST_Response
	 */
	public function rest_update_notice_status( $request ) {
		if ( ! isset( $request['id'] ) || ! isset( $request['status'] ) ) {
			return new \WP_Error(
				'block_lab_rest_no_id',
				esc_html__( 'Something went horribly wrong.', 'block-lab' ),
				array( 'status' => 500 )
			);
		}

		$update = $this->update_notice_status( $request['id'], $request['status'] );
		return rest_ensure_response( $update );
	}

	/**
	 * Check if a notice has been dismissed.
	 *
	 * @param string   $notice_id The ID of the notice to check.
	 * @param int|bool $user_id   The ID of the user to check. Will use the current user ID if not specified.
	 * @return string
	 */
	public function get_notice_status( $notice_id, $user_id = false ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$notices = get_user_meta( $user_id, $this->meta_key, true );

		if ( empty( $notices ) ) {
			$notices = array();
		}

		if ( isset( $notices[ $notice_id ] ) ) {
			return $notices[ $notice_id ];
		}

		return 'active';
	}

	/**
	 * Store a dismissed notice id in user meta, so that it's not displayed again.
	 *
	 * @param string   $notice_id The ID of the notice to check.
	 * @param string   $status    The status to update the notice to. Should be 'active' or 'dismissed'.
	 * @param int|bool $user_id   The ID of the user to check. Will use the current user ID if not specified.
	 */
	public function update_notice_status( $notice_id, $status, $user_id = false ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( 'active' !== $status && 'dismissed' !== $status ) {
			return new \WP_Error(
				'block_lab_invalid_status',
				__( 'Invalid notice status. Use "active" or "dismissed".', 'block_lab' ),
				array( 'status' => 500 )
			);
		}

		$notices = get_user_meta( $user_id, $this->meta_key, true );

		if ( empty( $notices ) ) {
			$notices = array();
		}

		$notices[ $notice_id ] = $status;
return $user_id;
		return update_user_meta( $user_id, $this->meta_key, $notices );
	}
}
