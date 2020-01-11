<?php
/**
 * Text control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Text
 */
class Text extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'text';

	/**
	 * Text constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Text', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		foreach ( [ 'location', 'width', 'help', 'default', 'placeholder' ] as $setting ) {
			$this->settings[] = new Control_Setting( $this->settings_config[ $setting ] );
		}

		$this->settings[] = new Control_Setting(
			[
				'name'     => 'maxlength',
				'label'    => __( 'Character Limit', 'block-lab' ),
				'type'     => 'number_non_negative',
				'default'  => '',
				'sanitize' => [ $this, 'sanitize_number' ],
			]
		);
	}
}
