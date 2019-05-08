<?php
/**
 * Textarea control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Textarea
 */
class Textarea extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'textarea';

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'textarea';

	/**
	 * Textarea constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Textarea', 'block-lab' );
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
				'name'     => 'maxlength',
				'label'    => __( 'Character Limit', 'block-lab' ),
				'type'     => 'number_non_negative',
				'default'  => '',
				'sanitize' => array( $this, 'sanitize_number' ),
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'number_rows',
				'label'    => __( 'Number of Rows', 'block-lab' ),
				'type'     => 'number_non_negative',
				'default'  => 4,
				'sanitize' => array( $this, 'sanitize_number' ),
			)
		);
		$this->settings[] = new Control_Setting(
			array(
				'name'     => 'should_autop',
				'label'    => __( 'Convert newlines to p tags', 'block-lab' ),
				'type'     => 'checkbox',
				'default'  => '0',
				'sanitize' => array( $this, 'sanitize_checkbox' ),
			)
		);
	}
}
