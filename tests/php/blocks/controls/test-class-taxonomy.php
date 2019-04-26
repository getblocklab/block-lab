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
		$this->assertEquals( 'rest_slug', $post_setting->name );
		$this->assertEquals( 'Taxonomy Type', $post_setting->label );
		$this->assertEquals( 'taxonomy_type_rest_slug', $post_setting->type );
		$this->assertEquals( 'posts', $post_setting->default );
		$this->assertEquals( array( $this->instance, 'sanitize_taxonomy_type_rest_slug' ), $post_setting->sanitize );
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
