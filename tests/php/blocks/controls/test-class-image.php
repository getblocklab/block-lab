<?php
/**
 * Tests for class Image.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Image.
 */
class Test_Image extends \WP_UnitTestCase {

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
		$this->instance = new Controls\Image();
		$this->setting  = new Controls\Control_Setting();
	}
	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Image::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'image', $this->instance->name );
		$this->assertEquals( 'Image', $this->instance->label );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Image::register_settings()
	 */
	public function test_register_settings() {
		$this->instance->register_settings();

		$first_setting = reset( $this->instance->settings );
		$this->assertEquals( 'help', $first_setting->name );
		$this->assertEquals( 'Help Text', $first_setting->label );
		$this->assertEquals( 'text', $first_setting->type );
		$this->assertEquals( '', $first_setting->default );
		$this->assertEquals( 'sanitize_text_field', $first_setting->sanitize );

		$second_setting = end( $this->instance->settings );
		$this->assertEquals( 'default', $second_setting->name );
		$this->assertEquals( 'Default Value', $second_setting->label );
		$this->assertEquals( 'url', $second_setting->type );
		$this->assertEquals( '', $second_setting->default );
		$this->assertEquals( 'esc_url_raw', $second_setting->sanitize );
	}

	/**
	 * Test validate.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Image::validate()
	 */
	public function test_validate() {
		$expected_url           = 'foo/bar.jpeg';
		$expected_attachment_id = $this->factory()->attachment->create_object(
			$expected_url,
			0,
			array(
				'post_mime_type' => 'image/jpeg',
			)
		);
		$valid_id               = $expected_attachment_id;
		$invalid_id             = 2000000;

		$this->assertEquals( false, $this->instance->validate( $invalid_id, false ) );
		$this->assertEquals( $expected_attachment_id, $this->instance->validate( $valid_id, false ) );
		$this->assertEquals( '', $this->instance->validate( $invalid_id, true ) );
		$this->assertContains( $expected_url, $this->instance->validate( $valid_id, true ) );

		$expected_url     = 'bar/baz.mp4';
		$invalid_video_id = $this->factory()->attachment->create_object(
			$expected_url,
			0,
			array(
				'post_mime_type' => 'video/mp4',
			)
		);

		// When the mime_type is that of a video, this should not return a URL or an id.
		$this->assertEquals( false, $this->instance->validate( $invalid_video_id, false ) );
		$this->assertEquals( '', $this->instance->validate( $invalid_video_id, true ) );

		// When this passes a WP_Post to validate(), it should also not return a URL or an id.
		$video_attachment_post = get_post( $invalid_video_id );
		$this->assertEquals( false, $this->instance->validate( $video_attachment_post, false ) );
		$this->assertEquals( '', $this->instance->validate( $video_attachment_post, true ) );
	}
}
