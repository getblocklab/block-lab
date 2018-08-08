<?php
/**
 * Block.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Advanced_Custom_Blocks\Blocks\Controls;

/**
 * Class Option
 */
class Control_Option {

	/**
	 * Option name (slug).
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Option label.
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Option type.
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Default value.
	 *
	 * @var mixed
	 */
	public $default = '';

	/**
	 * Current value. Null for unset.
	 *
	 * @var mixed
	 */
	public $value = null;

	/**
	 * Option constructor.
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {
		if ( isset ( $args['name'] ) ) {
			$this->name = $args['name'];
		}
		if ( isset ( $args['label'] ) ) {
			$this->label = $args['label'];
		}
		if ( isset ( $args['type'] ) ) {
			$this->type = $args['type'];
		}
		if ( isset ( $args['default'] ) ) {
			$this->default = $args['default'];
		}
		if ( isset ( $args['value'] ) ) {
			$this->value = $args['value'];
		}
	}

	/**
	 * Get the current value, using the default if there is none set.
	 *
	 * @return mixed
	 */
	public function get_value() {
		if ( null === $this->value ) {
			return $this->default;
		}

		return $this->value;
	}
}