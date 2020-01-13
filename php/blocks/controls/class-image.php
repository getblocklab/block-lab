<?php
/**
 * Image control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Image
 */
class Image extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'image';

	/**
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'integer';

	/**
	 * Text constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Image', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		foreach ( [ 'location', 'width', 'help' ] as $setting ) {
			$this->settings[] = new Control_Setting( $this->settings_config[ $setting ] );
		}
	}

	/**
	 * Validates the value to be made available to the front-end template.
	 *
	 * @param string $value The value to either make available as a variable or echoed on the front-end template.
	 * @param bool   $echo Whether this value will be echoed.
	 * @return string|int $value The value to be made available or echoed on the front-end template, possibly 0 if none found.
	 */
	public function validate( $value, $echo ) {
		$image_id = intval( $value );

		// Backwards compatibility, as the value used to be the image's URL instead of its post ID.
		if ( empty( $image_id ) && is_string( $value ) ) {
			$legacy_src = $value;
			$legacy_id  = attachment_url_to_postid( $value );
		}

		if ( $echo ) {
			if ( isset( $legacy_src ) ) {
				return $legacy_src;
			}
			$image = wp_get_attachment_image_src( $image_id, 'full' );
			return ! empty( $image[0] ) ? $image[0] : '';
		} else {
			return isset( $legacy_id ) ? $legacy_id : $image_id;
		}
	}
}
