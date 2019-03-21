<?php
/**
 * Image control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
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
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'help',
				'label'    => __( 'Help Text', 'block-lab' ),
				'type'     => 'text',
				'default'  => '',
				'sanitize' => 'sanitize_text_field',
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'default',
				'label'    => __( 'Default Value', 'block-lab' ),
				'type'     => 'url',
				'default'  => '',
				'sanitize' => 'esc_url_raw',
				'help'     => __( 'An image URL.' ),
			)
		);
	}

	/**
	 * Validates the value to be made available to the front-end template.
	 *
	 * @param mixed $value The value to either make available as a variable or echoed on the front-end template.
	 * @param bool  $echo Whether this value will be echoed.
	 * @return mixed $value The value to be made available or echoed on the front-end template.
	 */
	public function validate( $value, $echo ) {
		$attachment = get_post( $value );
		if ( $echo ) {
			return $attachment ? wp_get_attachment_image_url( $attachment->ID ) : '';
		} else {
			return $attachment ? $attachment : false;
		}
	}
}
