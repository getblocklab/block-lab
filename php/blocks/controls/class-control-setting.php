<?php
/**
 * Control_Setting.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Control_Setting
 */
class Control_Setting {

	/**
	 * Setting name (slug).
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Setting label.
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Setting type.
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
	 * Help text.
	 *
	 * @var string
	 */
	public $help = '';

	/**
	 * Sanitizing function.
	 *
	 * @var mixed
	 */
	public $sanitize = '';

	/**
	 * Validating function.
	 *
	 * @var mixed
	 */
	public $validate = '';

	/**
	 * Current value. Null for unset.
	 *
	 * @var mixed
	 */
	public $value = null;

	/**
	 * Control_Setting constructor.
	 *
	 * @param array $args An associative array with keys corresponding to the Option's properties.
	 *
	 * @return void
	 */
	public function __construct( $args = [] ) {
		if ( isset( $args['name'] ) ) {
			$this->name = $args['name'];
		}
		if ( isset( $args['label'] ) ) {
			$this->label = $args['label'];
		}
		if ( isset( $args['type'] ) ) {
			$this->type = $args['type'];
		}
		if ( isset( $args['default'] ) ) {
			$this->default = $args['default'];
		}
		if ( isset( $args['help'] ) ) {
			$this->help = $args['help'];
		}
		if ( isset( $args['sanitize'] ) ) {
			$this->sanitize = $args['sanitize'];
		}
		if ( isset( $args['validate'] ) ) {
			$this->validate = $args['validate'];
		}
		if ( isset( $args['value'] ) ) {
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
