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
	 * The Repeater control is an array of arrays, with each row being its own array.
	 * For example, a repeater with two rows might be:
	 * [ 'rows': [ 0: [ 'example-text': 'Foo', 'example-image': 42 ], 1: [ 'example-text': 'Bar', 'example-image': 32 ] ] ].
	 *
	 * @var string
	 */
	public $type = 'array';

	/**
	 * Repeater constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Repeater', 'block-lab' );
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
}
