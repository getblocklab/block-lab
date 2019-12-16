<?php
/**
 * Test_Repeater_Template_Output
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;
use Block_Lab\Post_Types;

/**
 * Class Test_Repeater_Template_Output
 *
 * @package Block_Lab
 */
class Test_Repeater_Template_Output extends Abstract_Attribute {

	/**
	 * The field name of the repeater.
	 *
	 * @var string
	 */
	const REPEATER_FIELD_NAME = 'repeater';

	/**
	 * Fields that don't fit well into the other test groups.
	 *
	 * @var array[]
	 */
	public $special_case_fields = [];

	/**
	 * Sets class properties.
	 */
	public function set_properties() {
		$this->block_name = 'repeater-all-fields';
		$this->attributes = [
			'className'               => $this->class_name,
			self::REPEATER_FIELD_NAME => [
				'rows' => [
					[
						'checkbox'    => true,
						'text'        => 'Here is a text field',
						'textarea'    => 'This is the first lineAnd here is another',
						'url'         => 'https://example/an-example-url',
						'email'       => 'bravo@emal.com',
						'number'      => 51315,
						'color'       => 'rgba(68, 34, 65, 0.26666666666666666)',
						'image'       => $this->get_image_attribute(),
						'select'      => 'bar',
						'multiselect' => [ 'bar' ],
						'toggle'      => true,
						'range'       => 6,
						'radio'       => 'bar',
						'post'        => $this->get_post_attributes(),
						'rich-text'   => '<p>This is <strong>bold</strong></p><p>And this is <em>italic</em></p>',
						'taxonomy'    => $this->get_taxonomy_attributes(),
						'user'        => $this->get_user_attributes(),
					],
					[
						''            => '',
						'checkbox'    => false,
						'text'        => 'This is the second row',
						'textarea'    => 'Here is the textarea of the second rowAnd another line',
						'url'         => 'https://example.com/example-url-here',
						'email'       => 'yours@example.com',
						'number'      => 14,
						'color'       => 'rgba(53, 158, 53, 0.26666666666666666)',
						'image'       => $this->get_image_attribute(),
						'select'      => 'foo',
						'multiselect' => [ 'bar' ],
						'toggle'      => false,
						'range'       => 9,
						'radio'       => 'bar',
						'post'        => $this->get_post_attributes(),
						'rich-text'   => '<p>This is the first line</p><p>Here is <em>italic</em> and here is <strong>bold</strong></p>',
						'taxonomy'    => $this->get_taxonomy_attributes(),
						'user'        => $this->get_user_attributes(),
					],
				],
			],
		];

		$this->string_fields = [
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

		foreach ( $this->attributes[ self::REPEATER_FIELD_NAME ]['rows'] as $row ) {
			$image = wp_get_attachment_image_src( $row['image'], 'full' );

			$this->special_case_fields[] = [
				'checkbox'  => [
					'block_sub_field' => $row['checkbox'] ? 'Yes' : 'No',
					'block_sub_value' => $row['checkbox'] ? '1' : '',
				],
				'image'     => [
					'block_sub_field' => isset( $image[0] ) ? $image[0] : '',
					'block_sub_value' => $row['image'],
				],
				'rich-text' => [
					'block_sub_field' => $row['rich-text'],
					'block_sub_value' => $row['rich-text'],
				],
				'toggle'    => [
					'block_sub_field' => $row['toggle'] ? 'Yes' : 'No',
					'block_sub_value' => $row['toggle'] ? '1' : '',
				],
			];
		}
	}

	/**
	 * Gets the block config.
	 *
	 * @return array The config for the block.
	 */
	public function get_block_config() {
		$block_post = new Post_Types\Block_Post();
		$all_fields = array_merge(
			$this->string_fields,
			$this->object_fields,
			$this->special_case_field_names
		);

		$sub_fields = [];
		foreach ( $all_fields as $field_name ) {
			$control_name              = str_replace( '-', '_', $field_name );
			$control                   = $block_post->get_control( $control_name );
			$sub_fields[ $field_name ] = [
				'control' => str_replace( '-', '_', $field_name ),
				'name'    => $control_name,
				'type'    => $control->type,
				'parent'  => self::REPEATER_FIELD_NAME,
			];
		}

		$fields = [
			self::REPEATER_FIELD_NAME => [
				'control'    => 'repeater',
				'name'       => 'repeater',
				'type'       => 'object',
				'sub_fields' => $sub_fields,
			],
		];

		return [
			'category' => [
				'icon'  => null,
				'slug'  => '',
				'title' => '',
			],
			'excluded' => [],
			'fields'   => $fields,
			'icon'     => 'block_lab',
			'keywords' => [ 'Repeater' ],
			'name'     => $this->block_name,
			'title'    => 'Repeater With All Fields',
		];
	}

	/**
	 * Tests whether the repeater template has the expected values.
	 *
	 * This has a repeater with 2 rows, and tests every possible field.
	 * It sets mock block attributes, like those that would be saved from a block.
	 * Then, it loads the mock template in the theme's blocks/ directory and asserts the values.
	 *
	 * @covers \block_rows()
	 * @covers \block_row()
	 * @covers \reset_block_row()
	 * @covers \block_row_field()
	 * @covers \block_row_value()
	 * @covers \block_row_index()
	 * @covers \block_row_count()
	 */
	public function test_repeater_template() {
		$block = new Blocks\Block();
		$block->from_array( $this->get_block_config() );
		$rendered_template = $this->invoke_protected_method( block_lab()->loader, 'render_block_template', [ $block, $this->attributes ] );
		$actual_template   = str_replace( [ "\t", "\n" ], '', $rendered_template );
		$rows              = $this->attributes[ self::REPEATER_FIELD_NAME ]['rows'];

		$this->assertContains(
			sprintf(
				'block_row_count() returns %d',
				count( $rows )
			),
			$actual_template
		);

		// The 'className' should be present.
		$this->assertContains(
			sprintf( '<div class="%s">', $this->class_name ),
			$actual_template
		);

		// Test that block_row_index() returns the right row index.
		foreach ( $rows as $row_number => $row ) {
			$this->assertContains(
				sprintf(
					'In row %d, the result of block_row_index() is %d',
					$row_number,
					$row_number
				),
				$actual_template
			);
		}

		// Test the fields that return a string for block_sub_value().
		foreach ( $rows as $row_number => $row ) {
			foreach ( $this->string_fields as $field ) {
				$this->assertContains(
					sprintf(
						'And in row %d, here is the result of calling block_sub_value() for %s: %s',
						$row_number,
						$field,
						$row[ $field ]
					),
					$actual_template
				);

				$this->assertContains(
					sprintf(
						'In row %d, here is the result of block_sub_field() for %s: %s',
						$row_number,
						$field,
						$row[ $field ]
					),
					$actual_template
				);
			}
		}

		$object_fields = [];

		foreach ( $rows as $row ) {
			$object_fields[] = [
				'post'     => [
					'object'     => get_post( $row['post']['id'] ),
					'properties' => [ 'ID', 'post_name' ],
				],
				'taxonomy' => [
					'object'     => get_term( $row['taxonomy']['id'] ),
					'properties' => [ 'term_id', 'name' ],
				],
				'user'     => [
					'object'     => get_user_by( 'id', $row['user']['id'] ),
					'properties' => [ 'ID', 'first_name' ],
				],
			];
		}

		/*
		 * The fields here return objects for block_sub_value(), so test that some of the properties are correct.
		 * For example, block_sub_value( 'post' )->ID.
		 */
		foreach ( $object_fields as $row_number => $fields ) {
			foreach ( $fields as $name => $field ) {
				foreach ( $field['properties'] as $property ) {
					$this->assertContains(
						sprintf(
							'In row %d, here is the result of passing %s to block_sub_value() with the property %s: %s',
							$row_number,
							$name,
							$property,
							$field['object']->$property
						),
						$actual_template
					);
				}
			}
		}

		// Test the fields that don't fit well into the tests above.
		foreach ( $this->special_case_fields as $row_number => $special_case_field_row ) {
			foreach ( $special_case_field_row as $field_name => $expected ) {
				$this->assertContains(
					sprintf(
						'In row %d, here is the result of block_sub_field() for %s: %s',
						$row_number,
						$field_name,
						$expected['block_sub_field']
					),
					$actual_template
				);

				$this->assertContains(
					sprintf(
						'And in row %d, here is the result of calling block_sub_value() for %s: %s',
						$row_number,
						$field_name,
						$expected['block_sub_value']
					),
					$actual_template
				);
			}
		}
	}
}
