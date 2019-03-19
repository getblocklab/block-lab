<?php
/**
 * Enable and validate Pro version licensing.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;

/**
 * Class License
 */
class License extends Component_Abstract {
	/**
	 * URL of the Block Lab store.
	 *
	 * @var string
	 */
	public $store_url;

	/**
	 * Product slug of the Pro version on the Block Lab store.
	 *
	 * @var string
	 */
	public $product_slug;

	/**
	 * Initialise the Pro component.
	 */
	public function init() {
		$this->store_url    = 'https://getblocklab.com';
		$this->product_slug = 'block-lab-pro';
	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_filter( 'pre_update_option_block_lab_license_key', array( $this, 'save_license_key' ), 10, 1 );
	}

	/**
	 * Check that the license key is valid before saving.
	 *
	 * @param string $key The license key that was submitted.
	 *
	 * @return string
	 */
	public function save_license_key( $key ) {
		$this->activate_license( $key );

		if ( ! $this->is_valid() ) {
			$key = '';
			block_lab()->admin->settings->prepare_notice( $this->license_error_message() );
		} else {
			block_lab()->admin->settings->prepare_notice( $this->license_success_message() );
		}

		return $key;
	}

	/**
	 * Check if the license if valid.
	 *
	 * @return bool
	 */
	public function is_valid() {
		$license = $this->get_license();

		if ( isset( $license['license'] ) && 'valid' === $license['license'] ) {
			if ( isset( $license['expires'] ) && time() < strtotime( $license['expires'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieve the license data.
	 *
	 * @return mixed
	 */
	public function get_license() {
		$license = get_transient( 'block_lab_license' );

		if ( ! $license ) {
			$key = get_option( 'block_lab_license_key' );
			if ( ! empty( $key ) ) {
				$this->activate_license( $key );
				$license = get_transient( 'block_lab_license' );
			}
		}

		return $license;
	}

	/**
	 * Try to activate the license.
	 *
	 * @param string $key The license key to activate.
	 *
	 * @return bool
	 */
	public function activate_license( $key ) {
		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $key,
			'item_name'  => rawurlencode( $this->product_slug ),
			'url'        => home_url(),
		);

		// Call the Block Lab store's API.
		$response = wp_remote_post(
			$this->store_url,
			array(
				'timeout'   => 15,
				'sslverify' => true,
				'body'      => $api_params,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license    = json_decode( wp_remote_retrieve_body( $response ), true );
		$expiration = DAY_IN_SECONDS;

		set_transient( 'block_lab_license', $license, $expiration );
	}

	/**
	 * Admin notice for correct license details.
	 *
	 * @return string
	 */
	public function license_success_message() {
		$message = __( 'Your Block Lab license was successfully activated!', 'block-lab' );
		return sprintf( '<div class="notice notice-success"><p>%s</p></div>', esc_html( $message ) );
	}

	/**
	 * Admin notice for incorrect license details.
	 *
	 * @return string
	 */
	public function license_error_message() {
		$message = __( 'There was a problem activating your Block Lab license.', 'block-lab' );
		return sprintf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
	}
}
