<?php
/**
 * Custom Block Field.
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace AdvancedCustomBlocks\PostTypes;

/**
 * Class CustomBlock
 */
class Custom_Field {

	/**
	 * Field name (slug).
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Field label.
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Field control type.
	 *
	 * @var string
	 */
	public $control = 'text';

	/**
	 * Field location.
	 *
	 * @var string
	 */
	public $location = 'editor';

	/**
	 * Field order.
	 *
	 * @var int
	 */
	public $order = 0;

	/**
	 * CustomField constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		if ( isset( $config['name'] ) ) {
			$this->name = $config['name'];
		}
		if ( isset( $config['label'] ) ) {
			$this->label = $config['label'];
		}
		if ( isset( $config['control'] ) ) {
			$this->control = $config['control'];
		}
		if ( isset( $config['location'] ) ) {
			$this->location = $config['location'];
		}
		if ( isset( $config['order'] ) ) {
			$this->order = $config['order'];
		}
	}
}
