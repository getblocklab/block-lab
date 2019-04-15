<?php
/**
 * Post control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Post
 */
class Post extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'post';

	/**
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'object';

	/**
	 * Post constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Post', 'block-lab' );
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
				'name'     => 'placeholder',
				'label'    => __( 'Placeholder Text', 'block-lab' ),
				'type'     => 'text',
				'default'  => '',
				'sanitize' => 'sanitize_text_field',
			)
		);
	}

	/**
	 * Validates the value to be made available to the front-end template.
	 *
	 * @param mixed $value The value to either make available as a variable or echoed on the front-end template.
	 * @param bool  $echo Whether this will be echoed.
	 * @return string|WP_Post|null $value The value to be made available or echoed on the front-end template.
	 */
	public function validate( $value, $echo ) {
		if ( $echo ) {
			return isset( $value['title'] ) ? $value['title'] : '';
		} else {
			$post_id = isset( $value['id'] ) ? $value['id'] : 0;
			return get_post( $post_id );
		}
	}
}
