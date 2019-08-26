<?php
/**
 * Test_Template_Output
 *
 * @package Template_Output
 */

use Block_Lab\Post_Types;
use Block_Lab\Blocks;

/**
 * Class Test_Template_Output
 *
 * @package Template_Output
 */
class Test_Template_Output extends \WP_UnitTestCase {

	/**
	 * The block attributes.
	 *
	 * @var array
	 */
	public $attributes;

	/**
	 * All fields that return a string for block_value().
	 *
	 * @var array
	 */
	public $string_fields;

	/**
	 * All fields that don't return a string for block_value(), other than the repeater.
	 *
	 * @var array
	 */
	public $object_fields;

	/**
	 * The instance of Loader, to render the template.
	 *
	 * @var Blocks\Loader
	 */
	public $loader;

	/**
	 * The name of the block that tests all fields.
	 *
	 * @var string
	 */
	public $block_name;

	/**
	 * The name of the block with the prefix.
	 *
	 * @var string
	 */
	public $prefixed_block_name;

	/**
	 * The path to the blocks/ directory in the theme.
	 *
	 * @var string
	 */
	public $blocks_directory;

	/**
	 * The location of the block template.
	 *
	 * @var string
	 */
	public $template_location;

	/**
	 * The expected return value of the rich-text control.
	 *
	 * @var string
	 */
	public $rich_text_expected_return;

	/**
	 * The block class name.
	 *
	 * @var string
	 */
	public $class_name = 'example-name';

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->set_properties();
		$this->create_block_template();
	}

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		parent::setUp();

		if ( file_exists( $this->template_location ) ) {
			unlink( $this->template_location );
		}
		if ( is_dir( $this->blocks_directory ) ) {
			rmdir( $this->blocks_directory );
		}
	}

	/**
	 * Sets class properties.
	 */
	public function set_properties() {
		$this->loader = new Blocks\Loader();

		$this->attributes = array(
			'className'   => $this->class_name,
			'checkbox'    => true,
			'text'        => 'Here is a text field',
			'textarea'    => 'And here is something',
			'url'         => 'https://yourdomain.com/entered',
			'email'       => 'entered@emal.com',
			'number'      => 15134,
			'color'       => '#777444',
			'image'       => 614,
			'select'      => 'foo',
			'multiselect' => array( 'foo' ),
			'toggle'      => true,
			'range'       => 7,
			'radio'       => 'baz',
			'post'        => $this->get_post_attributes(),
			'rich-text'   => '<p>This is <strong>bold</strong> and this is <em>italic</em></p><p></p><p>Here is a new line with a space above</p>',
			'taxonomy'    => $this->get_taxonomy_attributes(),
			'user'        => $this->get_user_attributes(),
		);

		$this->string_fields = array(
			'text',
			'textarea',
			'url',
			'email',
			'number',
			'color',
			'select',
			'range',
			'radio',
		);

		$this->object_fields = array(
			'image',
			'multiselect',
			'post',
			'taxonomy',
			'user',
		);
	}

	/**
	 * Creates the block template.
	 *
	 * Instead of copying the fixture entirely into the theme directory,
	 * this puts an include statement in it, pointing to the fixture.
	 */
	public function create_block_template() {
		$this->rich_text_expected_return = '<p>This is <strong>bold</strong> and this is <em>italic</em></p></p><p>Here is a new line with a space above</p></p>';
		$this->block_name                = 'all-fields-except-repeater';
		$this->prefixed_block_name       = "block-lab/{$this->block_name}";
		$theme_directory                 = get_template_directory();
		$template_path_in_fixtures       = __DIR__ . "/fixtures/{$this->block_name}.php";
		$this->blocks_directory          = "{$theme_directory}/blocks";
		$this->template_location         = "{$this->blocks_directory}/block-{$this->block_name}.php";

		mkdir( $this->blocks_directory );
		$template_contents = sprintf( "<?php include '%s';", $template_path_in_fixtures );
		file_put_contents( $this->template_location, $template_contents ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
	}

	/**
	 * Gets the post attributes.
	 *
	 * @return array
	 */
	public function get_post_attributes() {
		$id = $this->factory()->post->create();

		return array(
			'id'   => $id,
			'name' => get_the_title( $id ),
		);
	}

	/**
	 * Gets the taxonomy attributes.
	 *
	 * @return array
	 */
	public function get_taxonomy_attributes() {
		$term = $this->factory()->tag->create_and_get();

		return array(
			'id'   => $term->term_id,
			'name' => $term->name,
		);
	}

	/**
	 * Gets the user attributes.
	 *
	 * @return array
	 */
	public function get_user_attributes() {
		$user = $this->factory()->user->create_and_get();

		return array(
			'id'       => $user->ID,
			'userName' => $user->display_name,
		);
	}

	/**
	 * Gets the block config.
	 *
	 * @return array The config for the block.
	 */
	public function get_block_config() {
		$block_post = new Post_Types\Block_Post();
		$fields     = array();

		$all_fields = array_merge(
			$this->string_fields,
			$this->object_fields,
			array_keys( $this->get_special_case_fields() )
		);

		foreach ( $all_fields as $field_name ) {
			$control_name          = str_replace( '-', '_', $field_name );
			$control               = $block_post->get_control( $control_name );
			$fields[ $field_name ] = array(
				'control' => str_replace( '-', '_', $field_name ),
				'name'    => $control_name,
				'type'    => $control->type,
			);
		}

		return array(
			'category' => array(
				'icon'  => null,
				'slug'  => '',
				'title' => '',
			),
			'excluded' => array(),
			'fields'   => $fields,
			'icon'     => 'block_lab',
			'keywords' => array( '' ),
			'name'     => $this->block_name,
			'title'    => 'All Fields',
		);
	}

	/**
	 * Gets the expected result of the template tags for special case fields.
	 *
	 * @return array
	 */
	public function get_special_case_fields() {
		return array(
			'checkbox'  => array(
				'block_field' => 'Yes',
				'block_value' => 1,
			),
			'rich-text' => array(
				'block_field' => $this->rich_text_expected_return,
				'block_value' => $this->rich_text_expected_return,
			),
			'toggle'    => array(
				'block_field' => 'Yes',
				'block_value' => 1,
			),
		);
	}

	/**
	 * Tests whether the rendered block template has the expected values.
	 */
	public function test_integration_render_block_template() {
		$block = new Blocks\Block();
		$block->from_array( $this->get_block_config() );
		$rendered_template = $this->loader->render_block_template( $block, $this->attributes );
		$actual_template   = str_replace( array( "\t", "\n" ), '', $rendered_template );

		// The 'className' should be present.
		$this->assertContains(
			sprintf( '<p class="%s">', $this->class_name ),
			$actual_template
		);

		// Test the fields that return a string for block_value().
		foreach ( $this->string_fields as $field ) {
			$this->assertContains(
				sprintf(
					esc_html( 'And here is the result of calling block_value() for %s: %s', 'bl-testing-templates' ),
					$field,
					$this->attributes[ $field ]
				),
				$actual_template
			);

			$this->assertContains(
				sprintf(
					esc_html( 'Here is the result of block_field() for %s: %s', 'bl-testing-templates' ),
					$field,
					$this->attributes[ $field ]
				),
				$actual_template
			);
		}

		$object_fields = array(
			'post'     => array(
				'object'     => get_post( $this->attributes['post']['id'] ),
				'properties' => array( 'ID', 'post_name' ),
			),
			'taxonomy' => array(
				'object'     => get_term( $this->attributes['taxonomy']['id'] ),
				'properties' => array( 'term_id', 'name' ),
			),
			'user'     => array(
				'object'     => get_user_by( 'id', $this->attributes['user']['id'] ),
				'properties' => array( 'ID', 'first_name' ),
			),
		);

		/*
		 * The fields here return objects for block_value(), so test that some of the properties are correct.
		 * For example, block_value( 'post' )->id.
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
		foreach ( $this->get_special_case_fields() as $field_name => $expected ) {
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
					'And here is the result of calling block_value() for %s: %s',
					$field_name,
					$expected['block_value']
				),
				$actual_template
			);
		}
	}
}
