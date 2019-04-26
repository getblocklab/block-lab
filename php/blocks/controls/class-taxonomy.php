<?php
/**
 * Taxonomy control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Taxonomy
 */
class Taxonomy extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'taxonomy';

	/**
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'object';

	/**
	 * Taxonomy constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Taxonomy', 'block-lab' );
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
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'rest_slug',
				'label'    => __( 'Taxonomy Type', 'block-lab' ),
				'type'     => 'taxonomy_type_rest_slug',
				'default'  => 'posts',
				'sanitize' => array( $this, 'sanitize_taxonomy_type_rest_slug' ),
			)
		);
	}

	/**
	 * Validates the value to be made available to the front-end template.
	 *
	 * @param mixed $value The value to either make available as a variable or echoed on the front-end template.
	 * @param bool  $echo Whether this will be echoed.
	 * @return string|WP_Term|null $value The value to be made available or echoed on the front-end template.
	 */
	public function validate( $value, $echo ) {
		$value = wp_parse_args(
			$value,
			array(
				'title' => '',
				'id'    => 0,
			)
		);

		if ( $echo ) {
			return $value['title'];
		} else {
			return get_term( $value['id'] );
		}
	}
}
