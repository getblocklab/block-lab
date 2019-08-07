<?php
/**
 * Tests for class Utils.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Tests for class Utils.
 */
class Test_Utils extends Base_Template {

	use Testing_Helper;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->instance = new Blocks\Utils();
		block_lab()->register_component( $this->instance );
	}

	/**
	 * Test is_pro.
	 *
	 * @covers \Block_Lab\Blocks\Utils::is_pro()
	 */
	public function test_is_pro() {
		$this->instance = new Block_Lab\Plugin();
		$this->instance->plugin_loaded();

		$this->instance->admin = new Block_Lab\Admin\Admin();
		$this->instance->admin->init();

		$this->set_license_validity( true );
		$this->assertTrue( $this->instance->is_pro() );

		$this->set_license_validity( false );
		$this->assertFalse( $this->instance->is_pro() );
	}

	/**
	 * Test get_template_locations.
	 *
	 * @covers \Block_Lab\Blocks\Utils::get_template_locations()
	 */
	public function test_get_template_locations() {
		$name = 'foo-baz';
		$this->assertEquals(
			array(
				"blocks/foo-baz/block.php",
				"blocks/block-foo-baz.php",
				"blocks/block.php",
			),
			$this->instance->get_template_locations( $name )
		);

		$name = 'example';
		$type = 'another-type';
		$this->assertEquals(
			array(
				"blocks/example/another-type.php",
				"blocks/another-type-example.php",
				"blocks/another-type.php",
			),
			$this->instance->get_template_locations( $name, $type )
		);
	}

	/**
	 * Test get_stylesheet_locations.
	 *
	 * @covers \Block_Lab\Blocks\Utils::get_stylesheet_locations()
	 */
	public function test_get_stylesheet_locations() {
		$name = 'foo-baz';
		$this->assertEquals(
			array(
				"blocks/foo-baz/block.css",
				"blocks/css/block-foo-baz.css",
				"blocks/block-foo-baz.css",
			),
			$this->instance->get_stylesheet_locations( $name )
		);

		$name = 'example';
		$type = 'another-type';
		$this->assertEquals(
			array(
				"blocks/example/another-type.css",
				"blocks/css/another-type-example.css",
				"blocks/another-type-example.css",
			),
			$this->instance->get_stylesheet_locations( $name, $type )
		);
	}

	/**
	 * Test locate_template.
	 *
	 * @covers \Block_Lab\Blocks\Utils::locate_template()
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
			$this->assertEquals( $template_location, $this->instance->locate_template( $templates, $non_existent_alternate_path ));

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
}
