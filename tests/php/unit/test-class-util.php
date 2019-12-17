<?php
/**
 * Tests for class Util.
 *
 * @package Block_Lab
 */

/**
 * Tests for class Util.
 */
class Test_Util extends Abstract_Template {

	use Testing_Helper;

	/**
	 * The instance to test.
	 *
	 * @var Block_Lab\Util
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->instance = new Block_Lab\Util();
		$this->instance->set_plugin( block_lab() );
	}

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		remove_all_filters( 'block_lab_template_path' );
		remove_all_filters( 'block_lab_icons' );
		remove_all_filters( 'block_lab_allowed_svg_tags' );
		parent::tearDown();
	}

	/**
	 * Test is_pro.
	 *
	 * This method is normally accessed via a Plugin::__call(), as a magic method.
	 * Like block_lab->is_pro().
	 *
	 * @covers \Block_Lab\Util::is_pro()
	 */
	public function test_is_pro() {
		$plugin_instance = new Block_Lab\Plugin();
		$plugin_instance->init();
		$plugin_instance->plugin_loaded();

		$plugin_instance->admin = new Block_Lab\Admin\Admin();
		$plugin_instance->admin->init();

		$this->set_license_validity( true );
		$this->assertTrue( $plugin_instance->is_pro() );
		$this->assertTrue( block_lab()->is_pro() );

		$this->set_license_validity( false );
		$this->assertFalse( $plugin_instance->is_pro() );
		$this->assertFalse( block_lab()->is_pro() );
	}

	/**
	 * Test loop.
	 *
	 * @covers \Block_Lab\Util::loop()
	 */
	public function test_loop() {
		$this->assertEquals( 'Block_Lab\\Blocks\Loop', get_class( $this->instance->loop() ) );

		// Calling this singleton function repeatedly should return the same instance of the Loop.
		$this->assertEquals( $this->instance->loop(), $this->instance->loop() );
	}

	/**
	 * Test get_template_locations.
	 *
	 * @covers \Block_Lab\Util::get_template_locations()
	 */
	public function test_get_template_locations() {
		$name = 'foo-baz';
		$this->assertEquals(
			[
				'blocks/foo-baz/block.php',
				'blocks/block-foo-baz.php',
				'blocks/block.php',
			],
			$this->instance->get_template_locations( $name )
		);

		$name = 'example';
		$type = 'another-type';
		$this->assertEquals(
			[
				'blocks/example/another-type.php',
				'blocks/another-type-example.php',
				'blocks/another-type.php',
			],
			$this->instance->get_template_locations( $name, $type )
		);
	}

	/**
	 * Test get_stylesheet_locations.
	 *
	 * @covers \Block_Lab\Util::get_stylesheet_locations()
	 */
	public function test_get_stylesheet_locations() {
		$name = 'foo-baz';
		$this->assertEquals(
			[
				'blocks/foo-baz/block.css',
				'blocks/css/block-foo-baz.css',
				'blocks/block-foo-baz.css',
			],
			$this->instance->get_stylesheet_locations( $name )
		);

		$name = 'example';
		$type = 'another-type';
		$this->assertEquals(
			[
				'blocks/example/another-type.css',
				'blocks/css/another-type-example.css',
				'blocks/another-type-example.css',
			],
			$this->instance->get_stylesheet_locations( $name, $type )
		);
	}

	/**
	 * Test locate_template.
	 *
	 * @covers \Block_Lab\Util::locate_template()
	 */
	public function test_locate_template() {
		$templates                   = $this->instance->get_template_locations( $this->mock_block_name );
		$template_content            = 'This is content in the template';
		$non_existent_alternate_path = "{$this->theme_directory}/non-existent-path";

		/*
		 * In each iteration, the $template_location should have a higher priority than the last.
		 * So when locate_template() doesn't have the $single argument of false,
		 * it should return the current $template_location value.
		 */
		$templates_in_parent_theme = array_reverse( $this->get_template_paths_in_theme() );
		foreach ( $templates_in_parent_theme as $template_location ) {
			$this->file_put_contents( $template_location, $template_content );

			// Without the $single argument of false, this should return only one template.
			$this->assertEquals( $template_location, $this->instance->locate_template( $templates ) );

			// When passing a second argument of a path that doesn't exist, the result should be the same.
			$this->assertEquals( $template_location, $this->instance->locate_template( $templates, $non_existent_alternate_path ) );

			// With the $single argument of false, this should return an array() that contains this $template_location.
			$this->assertTrue( in_array( $template_location, $this->instance->locate_template( $templates, '', false ), true ) );
		}

		$base_alternate_block_directory = "{$this->theme_directory}/alternate-blocks-dir";

		$this->mkdir( $base_alternate_block_directory );
		$this->mkdir( "{$base_alternate_block_directory}/blocks" );
		$this->mkdir( "{$base_alternate_block_directory}/blocks/{$this->mock_block_name}" );

		$full_alternate_block_path = "{$base_alternate_block_directory}/blocks/{$this->mock_block_name}/block.php";
		$this->file_put_contents( $full_alternate_block_path, $template_content );

		// Now that this passes a second argument with a path, this should return the block in that path.
		$this->assertEquals( $full_alternate_block_path, $this->instance->locate_template( $templates, $base_alternate_block_directory ) );

		// Similar to the test above, but this should return an array that has the block.
		$this->assertTrue( in_array( $full_alternate_block_path, $this->instance->locate_template( $templates, $base_alternate_block_directory, false ), true ) );

		add_filter(
			'block_lab_template_path',
			function( $path ) use ( $base_alternate_block_directory ) {
				unset( $path );
				return $base_alternate_block_directory;
			}
		);

		// The filter above should have the same effect as simply passing a 2nd argument of the $path.
		$this->assertEquals( $full_alternate_block_path, $this->instance->locate_template( $templates ) );

		// Similarly, the filter above should cause this to include the template in the alternate path, among other templates.
		$this->assertTrue( in_array( $full_alternate_block_path, $this->instance->locate_template( $templates, '', false ), true ) );
	}

	/**
	 * Test get_icons.
	 *
	 * @covers \Block_Lab\Util::get_icons()
	 */
	public function test_get_icons() {
		$icons = $this->instance->get_icons();
		$this->assertEquals(
			'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M6.5 10h-2v7h2v-7zm6 0h-2v7h2v-7zm8.5 9H2v2h19v-2zm-2.5-9h-2v7h2v-7zm-7-6.74L16.71 6H6.29l5.21-2.74m0-2.26L2 6v2h19V6l-9.5-5z"/></svg>',
			$icons['account_balance']
		);
		$this->assertEquals(
			'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M18 9v4H6V9H4v6h16V9h-2z"/></svg>',
			$icons['space_bar']
		);

		$additional_icon = '<svg></svg>';
		$icon_name       = 'additional_icon';

		add_filter(
			'block_lab_icons',
			function( $icons ) use ( $additional_icon, $icon_name ) {
				$icons[ $icon_name ] = $additional_icon;
				return $icons;
			}
		);

		// The filter should add the additional icon.
		$icons = $this->instance->get_icons();
		$this->assertEquals( $additional_icon, $icons[ $icon_name ] );
	}

	/**
	 * Test allowed_svg_tags.
	 *
	 * @covers \Block_Lab\Util::allowed_svg_tags()
	 */
	public function test_allowed_svg_tags() {
		$this->assertEquals(
			[
				'svg'    => [
					'xmlns'   => true,
					'width'   => true,
					'height'  => true,
					'viewbox' => true,
				],
				'g'      => [ 'fill' => true ],
				'title'  => [ 'title' => true ],
				'path'   => [
					'd'       => true,
					'fill'    => true,
					'opacity' => true,
				],
				'circle' => [
					'cx'   => true,
					'cy'   => true,
					'r'    => true,
					'fill' => true,
				],
			],
			$this->instance->allowed_svg_tags()
		);

		$additional_tag_name       = 'example';
		$additional_tag_attributes = [ 'bax' => true ];

		add_filter(
			'block_lab_allowed_svg_tags',
			function( $allowed_tags ) use ( $additional_tag_name, $additional_tag_attributes ) {
				$allowed_tags[ $additional_tag_name ] = $additional_tag_attributes;
				return $allowed_tags;
			}
		);

		// The filter should add the additional tag and attributes.
		$svg_tags = $this->instance->allowed_svg_tags();
		$this->assertEquals( $additional_tag_attributes, $svg_tags[ $additional_tag_name ] );
	}

	/**
	 * Test get_post_type_slug.
	 *
	 * @covers \Block_Lab\Util::get_post_type_slug()
	 */
	public function test_get_post_type_slug() {
		$this->assertEquals( 'block_lab', $this->instance->get_post_type_slug() );

		// It should also be possible to call this via a magic method of the Plugin class.
		$this->assertEquals( 'block_lab', block_lab()->get_post_type_slug() );
	}

	/**
	 * Test get_url_from_path.
	 *
	 * @covers \Block_Lab\Util::get_url_from_path()
	 */
	public function test_get_url_from_path() {
		$subdirectory_path = 'wp-content/theme/blocks/test-block-here.css';
		$path              = ABSPATH . $subdirectory_path;
		$this->assertEquals( '/' . $subdirectory_path, $this->instance->get_url_from_path( $path ) );
	}
}
