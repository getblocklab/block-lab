<?php
/**
 * Verifies the Genesis Pro subscription, and saves the link to download GCB Pro.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin\Migration;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Block_Lab\Component_Abstract;

/**
 * Class Subscription_Api
 */
class Subscription_Api extends Component_Abstract {

	/**
	 * Option name where the subscription key is stored for Genesis Pro plugins.
	 *
	 * @var string
	 */
	const OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY = 'genesis_pro_subscription_key';

	/**
	 * Transient name where the subscription endpoint response is stored.
	 *
	 * @var string
	 */
	const TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK = 'genesis_custom_blocks_pro_download_link';

	/**
	 * Adds the component action.
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_route_update_subscription_key' ] );
	}

	/**
	 * Registers a route to update the subscription key.
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
				'accept_json'         => true,
			]
		);
	}

	/**
	 * Gets the REST API response to the request to update the subscription key.
	 *
	 * @param WP_REST_Request $data Data sent in the POST request.
	 * @return WP_REST_Response|WP_Error A WP_REST_Response on success, WP_Error on failure.
	 */
	public function get_update_subscription_key_response( $data ) {
		$key = $data->get_param( 'subscriptionKey' );

		if ( empty( $key ) ) {
			$this->delete_subscription_data();
			return new WP_Error( 'empty_subscription_key', __( 'Empty subscription key', 'block-lab' ) );
		}

		$sanitized_key         = $this->sanitize_subscription_key( $key );
		$subscription_response = $this->get_subscription_response( $sanitized_key );
		if ( $subscription_response->is_valid() && ! empty( $subscription_response->get_product_info()->download_link ) ) {
			$was_option_update_successful = update_option( self::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY, $sanitized_key );

			if ( ! $was_option_update_successful ) {
				$existing_option = get_option( self::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY );

				// update_option() will return false when trying to save the same option that's already saved.
				// In that case, there's no need for an error, but any other failure should be an error.
				if ( $sanitized_key !== $existing_option ) {
					$this->delete_subscription_data();
					return new WP_Error( 'option_not_updated', __( 'The option was not updated', 'block-lab' ) );
				}
			}

			set_transient(
				self::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK,
				esc_url_raw( $subscription_response->get_product_info()->download_link )
			);

			return rest_ensure_response( [ 'success' => true ] );
		} else {
			$this->delete_subscription_data();
			return new WP_Error(
				$subscription_response->get_error_code(),
				$this->get_subscription_invalid_message( $subscription_response->get_error_code() )
			);
		}
	}

	/**
	 * Deletes the stored Genesis Pro key and the GCB Pro download link.
	 */
	public function delete_subscription_data() {
		delete_option( self::OPTION_NAME_GENESIS_PRO_SUBSCRIPTION_KEY );
		delete_transient( self::TRANSIENT_NAME_GCB_PRO_DOWNLOAD_LINK );
	}

	/**
	 * Gets a new subscription response.
	 *
	 * @param string $key The subscription key to check.
	 * @return Subscription_Response The subscription response.
	 */
	public function get_subscription_response( $key ) {
		return new Subscription_Response( $key );
	}

	/**
	 * Admin message for incorrect subscription details.
	 *
	 * @param string $error_code The error code from the endpoint.
	 * @return string The error message to display.
	 */
	public function get_subscription_invalid_message( $error_code ) {
		switch ( $error_code ) {
			case 'key-unknown':
				return esc_html__( 'The subscription key you entered appears to be invalid or is not associated with this product. Please verify the key you have saved here matches the key in your WP Engine Account Portal.', 'block-lab' );

			case 'key-invalid':
				return esc_html__( 'The subscription key you entered is invalid. Get your subscription key in the WP Engine Account Portal.', 'block-lab' );

			case 'key-deleted':
				return esc_html__( 'Your subscription key was regenerated in the WP Engine Account Portal but was not updated in this settings page. Update your subscription key here to receive updates.', 'block-lab' );

			case 'subscription-expired':
				return esc_html__( 'Your Genesis Pro subscription has expired. Please renew it.', 'block-lab' );

			case 'subscription-notfound':
				return esc_html__( 'A valid subscription for your subscription key was not found. Please contact support.', 'block-lab' );

			case 'product-unknown':
				return esc_html__( 'The product you requested information for is unknown. Please contact support.', 'block-lab' );

			default:
				return esc_html__( 'There was an unknown error connecting to the update service. Please ensure the key you have saved here matches the key in your WP Engine Account Portal. This issue could be temporary. Please contact support if this error persists.', 'block-lab' );
		}
	}

	/**
	 * Gets the sanitized subscription key.
	 *
	 * @param string $subscription_key The subscription key.
	 * @return string The sanitized key.
	 */
	public function sanitize_subscription_key( $subscription_key ) {
		return preg_replace( '/[^A-Za-z0-9_-]/', '', $subscription_key );
	}
}
