<?php
/**
 * Tests for class Taxonomy.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Taxonomy.
 */
class Test_Taxonomy extends \WP_UnitTestCase {

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
		$this->instance = new Controls\Taxonomy();
		$this->setting  = new Controls\Control_Setting();
	}
	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Taxonomy::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'taxonomy', $this->instance->name );
		$this->assertEquals( 'Taxonomy', $this->instance->label );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Taxonomy::register_settings()
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
		$this->assertEquals( 'Taxonomy Type', $post_setting->label );
		$this->assertEquals( 'taxonomy_type_rest_slug', $post_setting->type );
		$this->assertEquals( 'posts', $post_setting->default );
		$this->assertEquals( array( $this->instance, 'sanitize_taxonomy_type_rest_slug' ), $post_setting->sanitize );
	}

	/**
	 * Test render_settings_taxonomy_type_rest_slug.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Taxonomy::render_settings_taxonomy_type_rest_slug()
	 * @covers \Block_Lab\Blocks\Controls\Control_Abstract::render_select()
	 */
	public function render_settings_taxonomy_type_rest_slug() {
		$name = 'post_type';
		$id   = 'bl_post_type';

		ob_start();
		$this->instance->render_settings_taxonomy_type_rest_slug( $this->setting, $name, $id );
		$output = ob_get_clean();
		$this->assertContains( $name, $output );
		$this->assertContains( $id, $output );
		foreach( array( 'post_tag', 'category' ) as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$this->assertContains( $post_type_object->rest_base, $output );
		}
	}

	/**
	 * Test get_taxonomy_rest_slugs.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Taxonomy::get_taxonomy_rest_slugs()
	 */
	public function test_get_taxonomy_rest_slugs() {
		$new_tax_slug  = 'foo-new-tax';
		$new_tax_label = 'New Taxonomy';
		$rest_base     = 'foo-new-taxonomies';

		register_taxonomy(
			$new_tax_slug,
			'post',
			array(
				'show_in_rest' => true,
				'label'        => $new_tax_label,
				'rest_base'    => $rest_base,
			)
		);

		// If a registered taxonomy doesn't have a rest_base, this should use the slug instead.
		$new_tax_slug_without_rest_base  = 'baz-new-tax';
		$new_tax_label_without_rest_base = 'Baz New Taxonomy';
		register_taxonomy(
			$new_tax_slug_without_rest_base,
			'page',
			array(
				'show_in_rest' => true,
				'label'        => $new_tax_label_without_rest_base,
			)
		);

		$this->assertEquals(
			array(
				'categories'                     => 'Categories',
				'tags'                           => 'Tags',
				$rest_base                       => $new_tax_label,
				$new_tax_slug_without_rest_base  => $new_tax_label_without_rest_base,
			),
			$this->instance->get_taxonomy_type_rest_slugs()
		);
	}

	/**
	 * Test sanitize_taxonomy_type_rest_slug.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Taxonomy::sanitize_taxonomy_type_rest_slug()
	 */
	public function test_sanitize_taxonomy_type_rest_slug() {
		$invalid_taxonomy_type = 'baz_invalid_taxonomy';
		$valid_taxonomy_type   = 'categories';
		$this->assertEmpty( $this->instance->sanitize_taxonomy_type_rest_slug( $invalid_taxonomy_type ) );
		$this->assertEquals( $valid_taxonomy_type, $this->instance->sanitize_taxonomy_type_rest_slug( $valid_taxonomy_type ) );

		$location_taxonomy_type_slug = 'location';
		$rest_base                   = 'locations';
		register_taxonomy(
			$location_taxonomy_type_slug,
			'post',
			array(
				'public'       => true,
				'show_in_rest' => true,
				'rest_base'    => $rest_base,
			)
		);

		// This should recognize the rest_base of the testimonial taxonomy type, even though it's different from its slug.
		$this->assertEquals( $rest_base, $this->instance->sanitize_taxonomy_type_rest_slug( $rest_base ) );
	}

	/**
	 * Test validate.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Taxonomy::validate()
	 */
	public function test_validate() {
		$expected_term = $this->factory()->tag->create_and_get();
		$valid_id      = $expected_term->term_id;
		$invalid_id    = 10000000;
		$term_name     = $expected_term->name;

		// When there's an invalid term ID, this should return null.
		$this->assertEquals( null, $this->instance->validate( array( 'id' => $invalid_id ), false ) );
		$this->assertEquals( $expected_term, $this->instance->validate( array( 'id' => $valid_id ), false ) );

		// If the ID is invalid, this should return ''.
		$this->assertEquals( '', $this->instance->validate( array( 'id' => $invalid_id ), true ) );
		$this->assertEquals( $term_name, $this->instance->validate( array( 'id' => $valid_id ), true ) );
	}
}
