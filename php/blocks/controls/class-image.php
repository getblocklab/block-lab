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
		$is_image = wp_attachment_is( 'image', $value ) && is_numeric( $value );

		if ( $echo ) {
			return $is_image ? wp_get_attachment_image_url( $value ) : '';
		} else {
			return $is_image ? $this->get_attachment_id_from_url( $value ) : false;
		}
	}

	/**
	 * Gets the attachment (image) ID, given its URL.
	 *
	 * Based on Micah Wood's solution,
	 * this creates a \WP_Query to search for the attachment ID.
	 *
	 * @see https://wpscholar.com/blog/get-attachment-id-from-wp-image-url/
	 * @param string $url The URL of the attachment (image).
	 * @return int|false The attachment ID, or false if none was found.
	 */
	public function get_attachment_id_from_url( $url ) {
		$image_file = basename( $url );
		$query      = new \WP_Query(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'fields'      => 'ids',
				'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'value'   => $image_file,
						'compare' => 'LIKE',
						'key'     => '_wp_attachment_metadata',
					),
				),
			)
		);

		if ( ! $query->have_posts() ) {
			return false;
		}

		foreach ( $query->posts as $post_id ) {
			$meta = wp_get_attachment_metadata( $post_id );
			if ( ! isset( $meta['file'], $meta['sizes'] ) ) {
				continue;
			}

			$original_file       = basename( $meta['file'] );
			$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
			if ( $original_file === $image_file || in_array( $image_file, $cropped_image_files, true ) ) {
				return $post_id;
			}
		}

		return false;
	}
}
