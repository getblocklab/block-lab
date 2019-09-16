<?php
/**
 * Repeater control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Repeater
 */
class Repeater extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'repeater';

	/**
	 * Field variable type.
	 *
	 * The Repeater control is an array of objects, with each row being an object.
	 * For example, a repeater with one row might be [ { 'example-text': 'Foo', 'example-image': 4232 } ].
	 *
	 * @var string
	 */
	public $type = 'object';

	/**
	 * Repeater constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Repeater', 'block-lab' );
		$this->register_hooks();
	}

	/**
	 * Register all the hooks.
	 */
	public function register_hooks() {
		add_filter( 'block_lab_cast_field_value_to_string', array( $this, 'cast_as_string' ), 10, 2 );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting( $this->settings_config['help'] );
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'min',
				'label'    => __( 'Minimum Rows', 'block-lab' ),
				'type'     => 'number_non_negative',
				'sanitize' => array( $this, 'sanitize_number' ),
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'max',
				'label'    => __( 'Maximum Rows', 'block-lab' ),
				'type'     => 'number_non_negative',
				'sanitize' => array( $this, 'sanitize_number' ),
			)
		);
	}

	/**
	 * Remove empty placeholder rows.
	 *
	 * @param mixed $value The value to either make available as a variable or echoed on the front-end template.
	 * @param bool  $echo Whether this will be echoed.
	 * @return mixed $value The value to be made available or echoed on the front-end template.
	 */
	public function validate( $value, $echo ) {
		unset( $echo );

		if ( isset( $value['rows'] ) ) {
			foreach ( $value['rows'] as $key => $row ) {
				unset( $value['rows'][ $key ][''] );
				unset( $value['rows'][ $key ][0] );
			}
		}

		return $value;
	}

	/**
	 * Show a warning if someone tries to use block_field() with a repeater.
	 *
	 * @param mixed $value  The value to cast as a string.
	 * @param Field $field  The field that is being cast.
	 * @return string
	 */
	public function cast_as_string( $value, $field ) {
		if ( 'repeater' === $field->control ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				return sprintf(
					// translators: Placeholders are the opening and closing anchor tags of a link.
					__( '⚠️ Please use Block Lab\'s %1$srepeater functions%2$s to display repeater fields in your template.', 'block-lab' ),
					'<a href="https://getblocklab.com/docs/fields/repeater/">',
					'</a>'
				);
			}
		}

		return $value;
	}
}
