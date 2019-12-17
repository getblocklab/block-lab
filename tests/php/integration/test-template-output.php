<?php
/**
 * Test_Template_Output
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;
use Block_Lab\Post_Types;

/**
 * Class Test_Template_Output
 *
 * @package Block_Lab
 */
class Test_Template_Output extends Abstract_Attribute {

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
			'classic-text' => '<h1>Here is a heading</h1><p>This is paragraph text that is <strong>bold</strong> and <em>italic</em></p><ul><li>Here is a li</li><li>And the next</li></ul>',
			'className'    => $this->class_name,
			'checkbox'     => true,
			'text'         => 'Here is a text field',
			'textarea'     => 'And here is something',
			'url'          => 'https://yourdomain.com/entered',
			'email'        => 'entered@email.com',
			'number'       => 15134,
			'color'        => '#777444',
			'image'        => $this->get_image_attribute(),
			'select'       => 'foo',
			'multiselect'  => [ 'foo' ],
			'toggle'       => true,
			'range'        => 7,
			'radio'        => 'baz',
			'post'         => $this->get_post_attributes(),
			'rich-text'    => '<p>This is <strong>bold</strong> and this is <em>italic</em></p><p></p><p>Here is a new line with a space above</p>',
			'taxonomy'     => $this->get_taxonomy_attributes(),
			'user'         => $this->get_user_attributes(),
		];

		$this->string_fields = [
			'classic-text',
			'text',
			'textarea',
			'url',
			'email',
			'number',
			'color',
			'select',
			'range',
			'radio',
		];

		$this->object_fields = [
			'multiselect',
			'post',
			'taxonomy',
			'user',
		];

		$image     = wp_get_attachment_image_src( $this->attributes['image'], 'full' );
		$rich_text = '<p>This is <strong>bold</strong> and this is <em>italic</em></p></p><p>Here is a new line with a space above</p></p>';

		$this->special_case_fields = [
			'checkbox'  => [
				'block_field' => 'Yes',
				'block_value' => 1,
			],
			'image'     => [
				'block_field' => isset( $image[0] ) ? $image[0] : '',
				'block_value' => $this->attributes['image'],
			],
			'rich-text' => [
				'block_field' => $rich_text,
				'block_value' => $rich_text,
			],
			'toggle'    => [
				'block_field' => 'Yes',
				'block_value' => 1,
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

		$all_fields = array_merge(
			$this->string_fields,
			$this->object_fields,
			$this->special_case_field_names
		);

		foreach ( $all_fields as $field_name ) {
			$control_name          = str_replace( '-', '_', $field_name );
			$control               = $block_post->get_control( $control_name );
			$fields[ $field_name ] = [
				'control' => str_replace( '-', '_', $field_name ),
				'name'    => $control_name,
				'type'    => $control->type,
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
	 * Tests whether the rendered block template has the expected values.
	 *
	 * Every field except the Repeater is tested.
	 * This sets mock block attributes, like those that would be saved from a block.
	 * Then, it loads the mock template in the theme's blocks/ directory,
	 * and ensures that all of these fields appear correctly in it.
	 *
	 * @covers \block_field()
	 * @covers \block_value()
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
		foreach ( $this->string_fields as $field ) {
			$this->assertContains(
				sprintf(
					'Here is the result of calling block_value() for %s: %s',
					$field,
					$this->attributes[ $field ]
				),
				$actual_template
			);

			$this->assertContains(
				sprintf(
					'Here is the result of block_field() for %s: %s',
					$field,
					$this->attributes[ $field ]
				),
				$actual_template
			);
		}

		$object_fields = [
			'post'     => [
				'object'     => get_post( $this->attributes['post']['id'] ),
				'properties' => [ 'ID', 'post_name' ],
			],
			'taxonomy' => [
				'object'     => get_term( $this->attributes['taxonomy']['id'] ),
				'properties' => [ 'term_id', 'name' ],
			],
			'user'     => [
				'object'     => get_user_by( 'id', $this->attributes['user']['id'] ),
				'properties' => [ 'ID', 'first_name' ],
			],
		];

		/*
		 * The fields here return objects for block_value(), so test that some of the properties are correct.
		 * For example, block_value( 'post' )->ID.
		 */
		foreach ( $object_fields as $name => $field ) {
			foreach ( $field['properties'] as $property ) {
				$this->assertContains(
					sprintf(
						'Here is the result of passing %s to block_value() with the property %s: %s',
						$name,
						$property,
						$field['object']->$property
					),
					$actual_template
				);
			}
		}

		// Test the fields that don't fit well into the tests above.
		foreach ( $this->special_case_fields as $field_name => $expected ) {
			$this->assertContains(
				sprintf(
					'Here is the result of block_field() for %s: %s',
					$field_name,
					$expected['block_field']
				),
				$actual_template
			);

			$this->assertContains(
				sprintf(
					'Here is the result of calling block_value() for %s: %s',
					$field_name,
					$expected['block_value']
				),
				$actual_template
			);
		}
	}
}
