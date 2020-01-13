<?php
/**
 * Block Field.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks;

/**
 * Class Field
 */
class Field {

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
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'string';

	/**
	 * Field order.
	 *
	 * @var int
	 */
	public $order = 0;

	/**
	 * Field settings.
	 *
	 * @var array
	 */
	public $settings = [];

	/**
	 * Field constructor.
	 *
	 * @param array $config An associative array with keys corresponding to the Field's properties.
	 */
	public function __construct( $config = [] ) {
		$this->from_array( $config );
	}

	/**
	 * Get field properties as an array, ready to be stored as JSON.
	 *
	 * @return array
	 */
	public function to_array() {
		$config = [
			'name'    => $this->name,
			'label'   => $this->label,
			'control' => $this->control,
			'type'    => $this->type,
			'order'   => $this->order,
		];

		$config = array_merge(
			$config,
			$this->settings
		);

		// Handle the sub-fields setting used by the Repeater.
		if ( isset( $this->settings['sub_fields'] ) ) {
			/**
			 * Recursively loop through sub-fields.
			 *
			 * @var string $key   The name of the sub-field's parent.
			 * @var Field  $field The sub-field.
			 */
			foreach ( $this->settings['sub_fields'] as $key => $field ) {
				$config['sub_fields'][ $key ] = $field->to_array();
			}
		}

		return $config;
	}

	/**
	 * Set field properties from an array, after being stored as JSON.
	 *
	 * @param array $config An array containing field parameters.
	 */
	public function from_array( $config ) {
		if ( isset( $config['name'] ) ) {
			$this->name = $config['name'];
		}
		if ( isset( $config['label'] ) ) {
			$this->label = $config['label'];
		}
		if ( isset( $config['control'] ) ) {
			$this->control = $config['control'];
		}
		if ( isset( $config['type'] ) ) {
			$this->type = $config['type'];
		}
		if ( isset( $config['order'] ) ) {
			$this->order = $config['order'];
		}
		if ( isset( $config['settings'] ) ) {
			$this->settings = $config['settings'];
		}

		if ( ! isset( $config['type'] ) ) {
			$control_class_name  = 'Block_Lab\\Blocks\\Controls\\';
			$control_class_name .= ucwords( $this->control, '_' );
			if ( class_exists( $control_class_name ) ) {
				/**
				 * An instance of the control, to retrieve the correct type.
				 *
				 * @var Control_Abstract $control_class
				 */
				$control_class = new $control_class_name();
				$this->type    = $control_class->type;
			}
		}

		// Add any other non-default keys to the settings array.
		$field_defaults = [ 'name', 'label', 'control', 'type', 'order', 'settings' ];
		$field_settings = array_diff( array_keys( $config ), $field_defaults );

		foreach ( $field_settings as $settings_key ) {
			$this->settings[ $settings_key ] = $config[ $settings_key ];
		}

		// Handle the sub-fields setting used by the Repeater.
		if ( isset( $this->settings['sub_fields'] ) ) {
			/**
			 * Recursively loop through sub-fields.
			 */
			foreach ( $this->settings['sub_fields'] as $key => $field ) {
				$this->settings['sub_fields'][ $key ] = new Field( $field );
			}
		}
	}

	/**
	 * Return the value with the correct variable type.
	 *
	 * @param mixed $value The value to typecast.
	 * @return mixed
	 */
	public function cast_value( $value ) {
		switch ( $this->type ) {
			case 'string':
				$value = strval( $value );
				break;
			case 'textarea':
				$value = strval( $value );
				if ( isset( $this->settings['new_lines'] ) ) {
					if ( 'autop' === $this->settings['new_lines'] ) {
						$value = wpautop( $value );
					}
					if ( 'autobr' === $this->settings['new_lines'] ) {
						$value = nl2br( $value );
					}
				}
				break;
			case 'boolean':
				if ( 1 === $value ) {
					$value = true;
				}
				break;
			case 'integer':
				$value = intval( $value );
				break;
			case 'array':
				if ( ! $value ) {
					$value = [];
				} else {
					$value = (array) $value;
				}
				break;
		}

		return $value;
	}

	/**
	 * Gets the field value as a string.
	 *
	 * @param mixed $value The field value.
	 *
	 * @return string $value The value to echo.
	 */
	public function cast_value_to_string( $value ) {
		if ( is_array( $value ) ) {
			return implode( ', ', $value );
		}

		if ( true === $value ) {
			return __( 'Yes', 'block-lab' );
		}

		if ( false === $value ) {
			return __( 'No', 'block-lab' );
		}

		return strval( $value );
	}
}
