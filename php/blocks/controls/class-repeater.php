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
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'columns',
				'label'    => __( 'Columns', 'block-lab' ),
				'type'     => 'columns',
				'default'  => '100',
				'sanitize' => 'sanitize_text_field',
			)
		);
		$this->settings[] = new Control_Setting( $this->settings_config['help'] );
	}

	/**
	 * Renders a button group of field widths.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_columns( $setting, $name, $id ) {
		$columns = array(
			'25'  => '4',
			'33'  => '3',
			'50'  => '2',
			'100' => '1',
		);
		$this->render_button_group( $setting, $name, $id, $columns );
	}
}
