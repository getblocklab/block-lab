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
		$this->assertEquals( 'placeholder', $post_setting->name );
		$this->assertEquals( 'Placeholder Text', $post_setting->label );
		$this->assertEquals( 'text', $post_setting->type );
		$this->assertEquals( '', $post_setting->default );
		$this->assertEquals( 'sanitize_text_field', $post_setting->sanitize );
	}

	/**
	 * Test validate.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Post::validate()
	 */
	public function test_validate() {
		$expected_wp_post = $this->factory()->post->create_and_get();
		$valid_id         = $expected_wp_post->ID;
		$invalid_id       = 10000000;

		$this->assertEquals( false, $this->instance->validate( $invalid_id, false ) );
		$this->assertEquals( $expected_wp_post, $this->instance->validate( $valid_id, false ) );
		$this->assertEquals( '', $this->instance->validate( $invalid_id, true ) );
		$this->assertEquals( get_the_title( $expected_wp_post), $this->instance->validate( $valid_id, true ) );
	}
}
