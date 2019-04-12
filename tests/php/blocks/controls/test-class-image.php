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
	 * Instance of the extending class Image.
	 *
	 * @var Controls\Image
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
	}

	/**
	 * Test validate.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Image::validate()
	 */
	public function test_validate() {
		$image_file             = 'bar.jpeg';
		$expected_attachment_id = $this->factory()->attachment->create_object(
			$image_file,
			0,
			array(
				'post_mime_type' => 'image/jpeg',
			)
		);

		// This is needed because attachments seem to usually have this kind of metadata.
		wp_update_attachment_metadata( $expected_attachment_id, array( 'file' => $image_file ) );
		$valid_attachment_url   = wp_get_attachment_url( $expected_attachment_id );
		$wp_upload              = wp_get_upload_dir();
		$invalid_attachment_url = $wp_upload['url'] . '/invalid.jpeg';

		$this->assertEquals( 0, $this->instance->validate( $invalid_attachment_url, false ) );
		$this->assertEquals( $expected_attachment_id, $this->instance->validate( $valid_attachment_url, false ) );
		$this->assertEquals( $expected_attachment_id, $this->instance->validate( $expected_attachment_id, false ) );
		$this->assertContains( $valid_attachment_url, $this->instance->validate( $valid_attachment_url, true ) );

		// This should still return an external URL, though the ID will be 0.
		$external_url = 'https://example.com/baz.jpeg';
		$this->assertEquals( 0, $this->instance->validate( $external_url, false ) );
		$this->assertContains( $external_url, $this->instance->validate( $external_url, true ) );
	}
}
