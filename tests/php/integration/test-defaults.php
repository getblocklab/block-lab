<?php
/**
 * Test_Defaults
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;
use Block_Lab\Post_Types;

/**
 * Class Test_Defaults
 *
 * @package Block_Lab
 */
class Test_Defaults extends Abstract_Attribute {

	/**
	 * Field defaults.
	 *
	 * An associative array of $field_name => $default_value.
	 *
	 * @var array
	 */
	public $defaults;

	/**
	 * Fields that don't fit well into the other test groups.
	 *
	 * @var array
	 */
	public $special_case_fields;

	/**
	 * Sets class properties.
	 */
	public function set_properties() {
		$this->block_name = 'all-fields-except-repeater';
		$this->attributes = [
			'className' => $this->class_name,
		];

		$this->defaults = [
			'checkbox'    => 1,
			'color'       => '#777777',
			'email'       => 'yourname@example.com',
			'multiselect' => [ 'example-default' ],
			'number'      => '56',
			'radio'       => 'baz',
			'range'       => 5,
			'rich-text'   => 'Here is the Rich Text default value',
			'select'      => 'another',
			'text'        => 'This is the text default value',
			'textarea'    => 'And this is the Textarea default',
			'toggle'      => 1,
			'url'         => 'https://example.com/this-is-a-default',
		];

		$this->string_fields = [
			'color',
			'email',
			'number',
			'radio',
			'range',
			'select',
			'text',
			'textarea',
			'url',
		];

		$rich_text_value           = sprintf( '<p>%s</p></p>', $this->defaults['rich-text'] );
		$this->special_case_fields = [
			'checkbox'    => [
				'block_field' => $this->defaults['checkbox'] ? 'Yes' : 'No',
				'block_value' => $this->defaults['checkbox'] ? '1' : '',
			],
			'multiselect' => [
				'block_field' => 'example-default',
			],
			'rich-text'   => [
				'block_field' => $rich_text_value,
				'block_value' => $rich_text_value,
			],
			'toggle'      => [
				'block_field' => $this->defaults['toggle'] ? 'Yes' : 'No',
				'block_value' => $this->defaults['toggle'] ? '1' : '',
			],
		];
	}

	/**
	 * Gets the block config.
	 *
	 * @return array The config for the block.
	 */
	public function get_block_config() {
		$block_post = new Post_Types\Block_Post();
		$fields     = [];

		foreach ( $this->defaults as $field_name => $default_value ) {
			$control_name          = str_replace( '-', '_', $field_name );
			$control               = $block_post->get_control( $control_name );
			$fields[ $field_name ] = [
				'control'  => str_replace( '-', '_', $field_name ),
				'name'     => $control_name,
				'type'     => $control->type,
				'settings' => [
					'default' => $this->defaults[ $field_name ],
				],
			];
		}

		return [
			'category' => [
				'icon'  => null,
				'slug'  => '',
				'title' => '',
			],
			'excluded' => [],
			'fields'   => $fields,
			'icon'     => 'block_lab',
			'keywords' => [ '' ],
			'name'     => $this->block_name,
			'title'    => 'All Fields',
		];
	}

	/**
	 * Tests whether the rendered block template has the default values.
	 *
	 * Every field that has a possible default value is tested.
	 * This loads the mock template in the theme's blocks/ directory,
	 * and ensures that all of these fields appear correctly in it.
	 */
	public function test_block_template() {
		$block = new Blocks\Block();
		$block->from_array( $this->get_block_config() );
		$rendered_template = $this->invoke_protected_method( block_lab()->loader, 'render_block_template', [ $block, $this->attributes ] );
		$actual_template   = str_replace( [ "\t", "\n" ], '', $rendered_template );

		// The 'className' should be present.
		$this->assertContains(
			sprintf( '<p class="%s">', $this->class_name ),
			$actual_template
		);

		// Test the fields that return a string for block_value().
		foreach ( $this->string_fields as $field_name ) {
			$this->assertContains(
				sprintf(
					'Here is the result of calling block_value() for %s: %s',
					$field_name,
					$this->defaults[ $field_name ]
				),
				$actual_template
			);

			$this->assertContains(
				sprintf(
					'Here is the result of block_field() for %s: %s',
					$field_name,
					$this->defaults[ $field_name ]
				),
				$actual_template
			);
		}

		// Test the fields that don't have simple string results for block_field() and block_value().
		foreach ( $this->special_case_fields as $field_name => $expected_values ) {
			$this->assertContains(
				sprintf(
					'Here is the result of block_field() for %s: %s',
					$field_name,
					$expected_values['block_field']
				),
				$actual_template
			);

			if ( isset( $expected_values['block_value'] ) ) {
				$this->assertContains(
					sprintf(
						'Here is the result of calling block_value() for %s: %s',
						$field_name,
						$expected_values['block_value']
					),
					$actual_template
				);
			}
		}
	}
}
