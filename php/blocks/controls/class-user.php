<?php
/**
 * User control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class User
 */
class User extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'user';

	/**
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'object';

	/**
	 * User constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'User', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		foreach ( [ 'location', 'width', 'help' ] as $setting ) {
			$this->settings[] = new Control_Setting( $this->settings_config[ $setting ] );
		}
	}

	/**
	 * Validates the value to be made available to the front-end template.
	 *
	 * @param mixed $value The value to either make available as a variable or echoed on the front-end template.
	 * @param bool  $echo Whether this will be echoed.
	 * @return mixed $value The value to be made available or echoed on the front-end template.
	 */
	public function validate( $value, $echo ) {
		$wp_user = isset( $value['id'] ) ? get_user_by( 'id', $value['id'] ) : null;

		if ( $echo ) {
			return $wp_user ? $wp_user->get( 'display_name' ) : '';
		} else {
			return $wp_user ? $wp_user : false;
		}
	}
}
