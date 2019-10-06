<?php
/**
 * Abstract_Attribute
 *
 * @package Block_Lab
 */

/**
 * Class Abstract_Attribute
 *
 * @package Block_Lab
 */
abstract class Abstract_Attribute extends \WP_UnitTestCase {

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
	 * All fields that return either an object or ID for block_value().
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
	 * This also controls the template that will be used.
	 * For example, if $block_name is 'your-example-block',
	 * the template will be at fixtures/your-example-block.php.
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
	 * The block class name.
	 *
	 * @var string
	 */
	public $class_name = 'example-name';

	/**
	 * The names of the fields that don't fall into the string or object field classifications.
	 *
	 * @var string[]
	 */
	public $special_case_field_names = array(
		'checkbox',
		'image',
		'rich-text',
		'toggle',
	);

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
	 * Invokes a protected method.
	 *
	 * @param object $instance The instance to invoke the method on.
	 * @param string $method_name The name of the method.
	 * @param array  $args The arguments.
	 * @return mixed The result of invoking the method.
	 * @throws ReflectionException If invoking this fails.
	 */
	public function invoke_protected_method( $instance, $method_name, $args = array() ) {
		$method = new ReflectionMethod( $instance, $method_name );
		$method->setAccessible( true );
		return $method->invokeArgs( $instance, $args );
	}

	/**
	 * Sets class properties.
	 */
	public function set_properties() {}

	/**
	 * Creates the block template.
	 *
	 * Instead of copying the fixture entirely into the theme directory,
	 * this puts an include statement in it, pointing to the fixture.
	 */
	public function create_block_template() {
		$this->prefixed_block_name = "block-lab/{$this->block_name}";
		$theme_directory           = get_template_directory();
		$template_path_in_fixtures = dirname( __DIR__ ) . "/fixtures/{$this->block_name}.php";
		$this->blocks_directory    = "{$theme_directory}/blocks";
		$this->template_location   = "{$this->blocks_directory}/block-{$this->block_name}.php";

		if ( ! is_dir( $this->blocks_directory ) ) {
			mkdir( $this->blocks_directory );
		}
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
	 * Gets the image attribute.
	 *
	 * @return int The image's ID.
	 */
	public function get_image_attribute() {
		return $this->factory()->attachment->create_object(
			array( 'file' => 'baz.jpeg' ),
			0,
			array( 'post_mime_type' => 'image/jpeg' )
		);
	}
}
