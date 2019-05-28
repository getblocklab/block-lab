<?php
/**
 * Tests for class Post.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Post.
 */
class Test_Post extends \WP_UnitTestCase {

	/**
	 * Instance of the extending class Number.
	 *
	 * @var Controls\Number
	 */
	public $instance;

	/**
	 * Instance of the setting.
	 *
	 * @var Controls\Control_Setting
	 */
	public $setting;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Post();
		$this->setting  = new Controls\Control_Setting();
	}
	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Post::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'post', $this->instance->name );
		$this->assertEquals( 'Post', $this->instance->label );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Post::register_settings()
	 */
	public function test_register_settings() {
		$this->instance->register_settings();

		$first_setting = reset( $this->instance->settings );
		$this->assertEquals( 'location', $first_setting->name );
		$this->assertEquals( 'Location', $first_setting->label );
		$this->assertEquals( 'location', $first_setting->type );
		$this->assertEquals( 'editor', $first_setting->default );
		$this->assertEquals( array( $this->instance, 'sanitize_location' ), $first_setting->sanitize );

		$post_setting = end( $this->instance->settings );
		$this->assertEquals( 'post_type_rest_slug', $post_setting->name );
		$this->assertEquals( 'Post Type', $post_setting->label );
		$this->assertEquals( 'post_type_rest_slug', $post_setting->type );
		$this->assertEquals( 'posts', $post_setting->default );
		$this->assertEquals( array( $this->instance, 'sanitize_post_type_rest_slug' ), $post_setting->sanitize );
	}

	/**
	 * Test render_settings_post_type_rest_slug.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Post::render_settings_post_type_rest_slug()
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_select()
	 */
	public function test_render_settings_post_type_rest_slug() {
		$name = 'post_type';
		$id   = 'bl_post_type';

		ob_start();
		$this->instance->render_settings_post_type_rest_slug( $this->setting, $name, $id );
		$output = ob_get_clean();
		$this->assertContains( $name, $output );
		$this->assertContains( $id, $output );
		foreach( array( 'post', 'page' ) as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$this->assertContains( $post_type_object->rest_base, $output );
		}
	}

	/**
	 * Test get_post_type_rest_slugs.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Post::get_post_type_rest_slugs()
	 */
	public function test_get_post_type_rest_slugs() {
		$this->assertEquals(
			array(
				'posts' => 'Posts',
				'pages' => 'Pages',
			),
			$this->instance->get_post_type_rest_slugs()
		);
	}

	/**
	 * Test sanitize_post_type_rest_slug.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Post::sanitize_post_type_rest_slug()
	 */
	public function test_sanitize_post_type_rest_slug() {
		$invalid_post_type = 'foo_invalid_type';
		$valid_post_type   = 'posts';
		$this->assertEmpty( $this->instance->sanitize_post_type_rest_slug( $invalid_post_type ) );
		$this->assertEquals( $valid_post_type, $this->instance->sanitize_post_type_rest_slug( $valid_post_type ) );

		// When passed 'media' for the 'attachment' post type, this should not return anything.
		$this->assertNull( $this->instance->sanitize_post_type_rest_slug( 'media' ) );

		$testimonial_post_type_slug = 'testimonials';
		$rest_base                  = 'testimonial';
		register_post_type(
			$testimonial_post_type_slug,
			array(
				'public'       => true,
				'show_in_rest' => true,
				'label'        => 'Testimonials',
				'rest_base'    => $rest_base,
			)
		);

		// This should recognize the rest_base of the testimonial post type, even though it's different from its slug.
		$this->assertEquals( $rest_base, $this->instance->sanitize_post_type_rest_slug( $rest_base ) );
	}

	/**
	 * Test validate.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Post::validate()
	 */
	public function test_validate() {
		$post_title       = 'Example Post';
		$expected_wp_post = $this->factory()->post->create_and_get( array( 'post_title' => $post_title ) );
		$valid_id         = $expected_wp_post->ID;
		$invalid_id       = 10000000;

		// When there's an invalid post ID and the second argument is false, this should return null.
		$this->assertEquals( null, $this->instance->validate( array( 'id' => $invalid_id ), false ) );
		$this->assertEquals( $expected_wp_post, $this->instance->validate( array( 'id' => $valid_id ), false ) );

		// If the post ID is invalid and the second argument is true (echo), this should return an empty string.
		$this->assertEquals( '', $this->instance->validate( array( 'id' => $invalid_id ), true ) );
		$this->assertEquals( $post_title, $this->instance->validate( array( 'id' => $valid_id ), true ) );

		// If the 'post_title' is later changed, this block should output the new post title for block_field().
		$updated_title = 'New Example Title';
		wp_update_post( array(
			'ID'         => $valid_id,
			'post_title' => $updated_title,
		) );
		$this->assertEquals( $updated_title, $this->instance->validate( array( 'id' => $valid_id ), true ) );
	}
}
