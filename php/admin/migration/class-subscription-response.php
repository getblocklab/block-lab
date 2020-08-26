<?php
/**
 * The Genesis Pro subscription response.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin\Migration;

use stdClass;

/**
 * Class Subscription_Response
 */
class Subscription_Response {

	/**
	 * Endpoint to validate the Genesis Pro subscription key.
	 *
	 * @var string
	 */
	const ENDPOINT = 'https://wp-product-info.wpesvc.net/v1/plugins/genesis-custom-blocks-pro/subscriptions/';

	/**
	 * The code expected in a success response.
	 *
	 * @var string
	 */
	const SUCCESS_CODE = 200;

	/**
	 * Whether the subscription key is valid.
	 *
	 * @var bool
	 */
	private $is_valid = false;

	/**
	 * The error code, if any.
	 *
	 * @var string|null
	 */
	private $error_code;

	/**
	 * The product info.
	 *
	 * @var stdClass|null
	 */
	private $product_info;

	/**
	 * Constructs the class.
	 *
	 * @param string $subscription_key The subscription key to check.
	 */
	public function __construct( $subscription_key ) {
		$this->evaluate( $subscription_key );
	}

	/**
	 * Evaluates the response, storing the response body and a possible error message.
	 *
	 * @param string $subscription_key The subscription key to check.
	 */
	public function evaluate( $subscription_key ) {
		$response = wp_remote_get(
			self::ENDPOINT . $subscription_key,
			[
				'timeout'    => defined( 'DOING_CRON' ) && DOING_CRON ? 30 : 3,
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
				'body'       => [
					'version' => block_lab()->get_version(),
				],
			]
		);

		if ( is_wp_error( $response ) || self::SUCCESS_CODE !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$this->error_code = $response->get_error_code();
			} else {
				$response_body    = json_decode( wp_remote_retrieve_body( $response ), false );
				$this->error_code = ! empty( $response_body->error_code ) ? $response_body->error_code : 'unknown';
			}

			return;
		}

		$this->is_valid     = true;
		$this->product_info = new stdClass();
		$response_body      = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_object( $response_body ) ) {
			$response_body = new stdClass();
		}

		$this->product_info = $response_body;
	}

	/**
	 * Gets whether the subscription key is valid.
	 *
	 * @return bool
	 */
	public function is_valid() {
		return $this->is_valid;
	}

	/**
	 * Gets the error code, if any.
	 *
	 * @return string|null
	 */
	public function get_error_code() {
		return $this->error_code;
	}

	/**
	 * Gets the product info, or null if there isn't any.
	 *
	 * @return stdClass|null
	 */
	public function get_product_info() {
		return $this->product_info;
	}
}
