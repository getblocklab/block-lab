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
class Select extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'select';

	/**
	 * Text constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Select', 'advanced-custom-blocks' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting( array(
			'name'     => 'help',
			'label'    => __( 'Field instructions', 'advanced-custom-blocks' ),
			'type'     => 'textarea',
			'default'  => '',
			'sanitize' => 'sanitize_textarea_field',
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'required',
			'label'    => __( 'Required?', 'advanced-custom-blocks' ),
			'type'     => 'checkbox',
			'default'  => '0',
			'sanitize' => array( $this, 'sanitise_checkbox' ),
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'options',
			'label'    => __( 'Choices', 'advanced-custom-blocks' ),
			'type'     => 'textarea_options',
			'default'  => '',
			'help'     => sprintf(
				'%s %s<br />%s<br />%s',
				__( 'Enter each choice on a new line.', 'advanced-custom-blocks' ),
				__( 'To specify the value and label separately, use this format:', 'advanced-custom-blocks'),
				_x( 'foo : Foo', 'Format for the menu values. option_value : Option Name', 'advanced-custom-blocks' ),
				_x( 'bar : Bar', 'Format for the menu values. option_value : Option Name', 'advanced-custom-blocks' )
			),
			'sanitize' => array( $this, 'sanitise_choices' ),
		) );
		$this->settings[] = new Control_Setting( array(
			'name'     => 'default',
			'label'    => __( 'Default Value', 'advanced-custom-blocks' ),
			'type'     => 'textarea_default',
			'default'  => '',
			'help'     => __( 'Enter each default value on a new line.', 'advanced-custom-blocks' ),
			'sanitize' => array( $this, 'sanitise_choices' ),
		) );
		$this->settings[] = new Control_Setting( array(
			'name'    => 'multiple',
			'label'   => __( 'Allow multiple choices?', 'advanced-custom-blocks' ),
			'type'    => 'checkbox',
			'default' => '',
			'sanitize' => array( $this, 'sanitise_checkbox' ),
		) );
	}

	/**
	 * Render options settings
	 *
	 * @param Control_Setting $setting
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_settings_textarea_options( $setting, $name, $id ) {
		$options = $setting->get_value();
		if ( is_array( $options ) ) {
			// Convert the array to text separated by new lines
			$value = '';
			foreach ( $options as $key => $option ) {
				if ( $key === $option ) {
					$value .= $option . "\n";
				} else {
					$value .= $key . ' : ' . $option . "\n";
				}
			}
			$setting->value = trim( $value );
		}
		parent::render_settings_textarea( $setting, $name, $id );
	}

	/**
	 * Render default settings
	 *
	 * @param Control_Setting $setting
	 * @param string $name
	 * @param string $id
	 *
	 * @return void
	 */
	public function render_settings_textarea_default( $setting, $name, $id ) {
		$options = $setting->get_value();
		if ( is_array( $options ) ) {
			// Convert the array to text separated by new lines
			$value = '';
			foreach ( $options as $option ) {
				$value .= $option . "\n";
			}
			$setting->value = trim( $value );
		}
		parent::render_settings_textarea( $setting, $name, $id );
	}

	/**
	 * Sanitize choices
	 *
	 * @param string $value
	 *
	 * @return array
	 */
	public function sanitise_choices( $value ) {
		$rows    = preg_split( '/\r\n|[\r\n]/', $value );
		$options = array();

		foreach( $rows as $key => $option ) {
			if ( '' === $option ) {
				continue;
			}

			$key_value = explode( ' : ', $option );

			if ( sizeof( $key_value ) > 1 ) {
				$options[ $key_value[0] ] = $key_value[1];
			} else {
				$options[ $option ] = $option;
			}
		}

		return $options;
	}

	/**
	 * Sanitize defaults
	 *
	 * @param string $value
	 *
	 * @return array
	 */
	public function sanitise_defaults( $value ) {
		$rows    = preg_split( '/\r\n|[\r\n]/', $value );
		$options = array();

		foreach( $rows as $key => $option ) {
			if ( '' === $option ) {
				continue;
			}

			$key_value = explode( ' : ', $option );

			if ( sizeof( $key_value ) > 1 ) {
				$options[] = $key_value[0];
			} else {
				$options[] = $option;
			}
		}

		return $options;
	}
}
