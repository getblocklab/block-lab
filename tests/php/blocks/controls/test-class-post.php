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
	 * @var Controls\Control_setting
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
		$this->assertEquals( 'help', $first_setting->name );
		$this->assertEquals( 'Help Text', $first_setting->label );
		$this->assertEquals( 'text', $first_setting->type );
		$this->assertEquals( '', $first_setting->default );
		$this->assertEquals( 'sanitize_text_field', $first_setting->sanitize );

		$post_setting = end( $this->instance->settings );
		$this->assertEquals( 'post_type_rest_slug', $post_setting->name );
		$this->assertEquals( 'Post Type', $post_setting->label );
		$this->assertEquals( 'post_type_rest_slug', $post_setting->type );
		$this->assertEquals( 'posts', $post_setting->default );
		$this->assertEquals( array( $this->instance, 'sanitize_post_type_rest_slug' ), $post_setting->sanitize );
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
