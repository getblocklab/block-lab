<?php
/**
 * Control abstract.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Advanced_Custom_Blocks\Blocks\Controls;

/**
 * Class Text
 */
class Textarea extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'textarea';

	/**
	 * Textarea constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->label = __( 'Textarea', 'advanced-custom-blocks' );

		$this->options[] = new Control_Option( array(
			'name'    => 'required',
			'label'   => __( 'Required?', 'advanced-custom-blocks' ),
			'type'    => 'checkbox',
			'default' => false,
		) );
		$this->options[] = new Control_Option( array(
			'name'    => 'placeholder',
			'label'   => __( 'Placeholder Text', 'advanced-custom-blocks' ),
			'type'    => 'text',
			'default' => '',
		) );
		$this->options[] = new Control_Option( array(
			'name'    => 'maxlength',
			'label'   => __( 'Character Limit', 'advanced-custom-blocks' ),
			'type'    => 'number',
			'default' => '',
		) );
	}
}
