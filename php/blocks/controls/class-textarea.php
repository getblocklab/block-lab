<?php
/**
 * Textarea control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
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
		foreach ( [ 'location', 'width', 'help' ] as $setting ) {
			$this->settings[] = new Control_Setting( $this->settings_config[ $setting ] );
		}

		$this->settings[] = new Control_Setting(
			[
				'name'     => 'default',
				'label'    => __( 'Default Value', 'block-lab' ),
				'type'     => 'textarea',
				'default'  => '',
				'sanitize' => 'sanitize_textarea_field',
			]
		);
		$this->settings[] = new Control_Setting( $this->settings_config['placeholder'] );
		$this->settings[] = new Control_Setting(
			[
				'name'     => 'maxlength',
				'label'    => __( 'Character Limit', 'block-lab' ),
				'type'     => 'number_non_negative',
				'default'  => '',
				'sanitize' => [ $this, 'sanitize_number' ],
			]
		);
		$this->settings[] = new Control_Setting(
			[
				'name'     => 'number_rows',
				'label'    => __( 'Number of Rows', 'block-lab' ),
				'type'     => 'number_non_negative',
				'default'  => 4,
				'sanitize' => [ $this, 'sanitize_number' ],
			]
		);
		$this->settings[] = new Control_Setting(
			[
				'name'     => 'new_lines',
				'label'    => __( 'New Lines', 'block-lab' ),
				'type'     => 'new_line_format',
				'default'  => 'autop',
				'sanitize' => [ $this, 'sanitize_new_line_format' ],
			]
		);
	}

	/**
	 * Renders a <select> of new line rendering formats.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_new_line_format( $setting, $name, $id ) {
		$formats = $this->get_new_line_formats();
		$this->render_select( $setting, $name, $id, $formats );
	}

	/**
	 * Gets the new line formats.
	 *
	 * @return array {
	 *     An associative array of new line formats.
	 *
	 *     @type string $key    The option value to save.
	 *     @type string $label  The label.
	 * }
	 */
	public function get_new_line_formats() {
		$formats = [
			'autop'  => __( 'Automatically add paragraphs', 'block-lab' ),
			'autobr' => __( 'Automatically add line breaks', 'block-lab' ),
			'none'   => __( 'No formatting', 'block-lab' ),
		];
		return $formats;
	}

	/**
	 * Sanitize the new line format, to ensure that it's valid.
	 *
	 * @param string $value The format to sanitize.
	 * @return string|null The sanitized rest_base of the post type, or null.
	 */
	public function sanitize_new_line_format( $value ) {
		if ( is_string( $value ) && array_key_exists( $value, $this->get_new_line_formats() ) ) {
			return $value;
		}
		return null;
	}
}
