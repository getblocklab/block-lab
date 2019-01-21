<?php
/**
 * Rich Text control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class RichText
 */
class Rich_Text extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'rich_text';

	/**
	 * Textarea constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Rich Text', 'block-lab' );
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
				'name'     => 'multiline',
				'label'    => __( 'Multiline', 'block-lab' ),
				'help'     => __( 'New lines create new paragraphs.', 'block-lab' ),
				'type'     => 'checkbox',
				'default'  => '0',
				'sanitize' => array( $this, 'sanitize_checkbox' ),
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
	}
}
