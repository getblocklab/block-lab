<?php
/**
 * Tests for class Control_Abstract.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Control_Abstract.
 */
class Test_Control_Abstract extends \WP_UnitTestCase {

	/**
	 * A mock name of the control.
	 *
	 * @var string
	 */
	const NAME = 'block-fields-settings[5c6c6bcf03d2c][default]';

	/**
	 * A mock ID of the control.
	 *
	 * @var string
	 */
	const ID = 'block-fields-edit-settings-number-default_5c6c6bcf03d2c';

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
		$this->instance = new Controls\Number();
		$this->setting  = new Controls\Control_Setting();
	}

	/**
	 * Test render_settings_number.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_settings_number()
	 */
	public function test_render_settings_number() {
		ob_start();
		$this->instance->render_settings_number( $this->setting, self::NAME, self::ID );
		$output = ob_get_clean();

		// This should not have a min="0" attribute.
		$this->assertNotContains( 'min="0"', $output );
		$this->assertContains( self::NAME, $output );
		$this->assertContains( self::ID, $output );
	}

	/**
	 * Test render_settings_number_non_negative.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_settings_number_non_negative()
	 */
	public function test_render_settings_number_non_negative() {
		ob_start();
		$this->instance->render_settings_number_non_negative( $this->setting, self::NAME, self::ID );
		$output = ob_get_clean();

		// This should have a min="0" attribute.
		$this->assertContains( 'min="0"', $output );
		$this->assertContains( self::NAME, $output );
		$this->assertContains( self::ID, $output );
	}

	/**
	 * Test render_number.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_number()
	 */
	public function test_render_number() {
		$min_attribute = 'min="0"';
		ob_start();
		$this->instance->render_number( $this->setting, self::NAME, self::ID );
		$output = ob_get_clean();

		// This should not have a min="0" attribute, as there is no 4th argument.
		$this->assertNotContains( $min_attribute, $output );

		ob_start();
		$this->instance->render_number( $this->setting, self::NAME, self::ID, true );
		$output = ob_get_clean();

		// This should  have a min="0" attribute, as the 4th argument is true.
		$this->assertContains( $min_attribute, $output );
	}

	/**
	 * Test render_settings_post_type_rest_slug.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_settings_post_type_rest_slug()
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
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::get_post_type_rest_slugs()
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
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::sanitize_post_type_rest_slug()
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
}
