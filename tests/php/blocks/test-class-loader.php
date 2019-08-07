<?php
/**
 * Tests for class Loader.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Tests for class Loader.
 */
class Test_Loader extends \WP_UnitTestCase {

	/**
	 * The name of a testing block.
	 *
	 * @var string
	 */
	public $mock_block_name = 'test-block';

	/**
	 * The path of the parent theme.
	 *
	 * @var string
	 */
	public $theme_directory;

	/**
	 * The template locations.
	 *
	 * @var array
	 */
	public $template_locations;

	/**
	 * The full path of the overridden theme template, used in a filter to override the normal template.
	 *
	 * @var string
	 */
	public $overridden_theme_template_path;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Blocks\Loader();
		$this->instance->set_plugin( block_lab() );
		$this->theme_directory                = get_template_directory();
		$this->template_locations             = block_lab()->get_block_lab_template_locations( $this->mock_block_name );
		$this->overridden_theme_template_path = "{$this->theme_directory}/example-overridden-template.php";

		foreach ( $this->get_block_template_directories() as $template_directory ) {
			mkdir( $template_directory );
		}
	}

	/**
	 * Teardown.
	 *
	 * Deletes the mock templates and directories that were created.
	 * This is in tearDown(), as it runs even if a test fails.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		// Delete testing templates and CSS files.
		array_map(
			function( $template ) {
				if ( file_exists( $template ) ) {
					unlink( $template );
				}
			},
			array_merge(
				$this->get_template_paths_in_theme(),
				$this->get_global_style_paths(),
				$this->get_template_css_paths(),
				array( $this->overridden_theme_template_path )
			)
		);

		/*
		 * Remove testing directories that were created, in reverse order.
		 * There's a nested directory, so this has to remove the parent directory last.
		 */
		array_map(
			function( $directory ) {
				if ( is_dir( $directory ) ) {
					rmdir( $directory );
				}
			},
			array_reverse( $this->get_block_template_directories() )
		);

		parent::tearDown();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers \Block_Lab\Blocks\Loader::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_action( 'enqueue_block_editor_assets', array( $this->instance, 'editor_assets' ) ) );
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( $this->instance, 'dynamic_block_loader' ) ) );
	}

	/**
	 * Test render_block_template.
	 *
	 * @covers \Block_Lab\Blocks\Loader::render_block_template()
	 */
	public function test_render_block_template() {
		$slug       = 'bl-testing-slug';
		$script_url = 'https://example.com/script.js';
		$block_name = 'test-image';
		$block      = new Blocks\Block();

		$block->from_array( array( 'name' => $block_name ) );

		// Test that the do_action() call with this action runs, and that it allows enqueuing a script.
		add_action(
			'block_lab_render_template',
			function( $block ) use ( $block_name, $slug, $script_url ) {
				if ( $block_name === $block->name ) {
					wp_enqueue_script( $slug, $script_url, array(), '0.1', true );
				}
			}
		);

		$this->instance->render_block_template( $block, array() );
		$scripts = wp_scripts();
		$script  = $scripts->registered[ $slug ];

		$this->assertTrue( in_array( $slug, $scripts->queue, true ) );
		$this->assertEquals( $slug, $script->handle );
		$this->assertEquals( $script_url, $script->src );

		// Test that the do_action() call with the dynamic name runs, like 'block_lab_render_template_bl-dynamic-testing-slug'.
		$slug       = 'bl-dynamic-testing-slug';
		$script_url = 'https://example.com/another-script.js';

		add_action(
			"block_lab_render_template_{$block_name}",
			function( $block ) use ( $block_name, $slug, $script_url ) {
				wp_enqueue_script( $slug, $script_url, array(), '0.1', true );
			}
		);

		$this->instance->render_block_template( $block, array() );
		$scripts = wp_scripts();
		$script  = $scripts->registered[ $slug ];

		$this->assertTrue( in_array( $slug, $scripts->queue, true ) );
		$this->assertEquals( $slug, $script->handle );
		$this->assertEquals( $script_url, $script->src );
	}

	/**
	 * Test enqueue_block_styles.
	 *
	 * @covers \Block_Lab\Blocks\Loader::enqueue_block_styles()
	 */
	public function test_enqueue_block_styles() {
		$wp_styles = wp_styles();
		$block_handle    = "block-lab__block-{$this->mock_block_name}";

		// Check that the correct stylesheet is enqueued.
		foreach ( $this->get_template_css_paths() as $key => $file ) {
			file_put_contents( $file, '' ); // @codingStandardsIgnoreLine
			$file_url = str_replace( untrailingslashit( ABSPATH ), '', $file );

			$this->instance->enqueue_block_styles( $this->mock_block_name, array( 'preview', 'block' ) );
			$this->assertContains( $block_handle, $wp_styles->queue );
			$this->assertArrayHasKey( $block_handle, $wp_styles->registered );
			$this->assertSame( $wp_styles->registered[ $block_handle ]->src, $file_url, "Trying to enqueue file #{$key} ({$file_url})." );

			wp_deregister_style( $block_handle );
			wp_dequeue_style( $block_handle );
		}

		// Check that nothing is enqueued if the file doesn't exist.
		$this->instance->enqueue_block_styles( 'does-not-exist', 'block' );
		$this->assertNotContains( $block_handle, $wp_styles->queue );
		$this->assertArrayNotHasKey( $block_handle, $wp_styles->registered );
	}

	/**
	 * Test enqueue_global_styles.
	 *
	 * @covers \Block_Lab\Blocks\Loader::enqueue_global_styles()
	 */
	public function test_enqueue_global_styles() {
		$wp_styles       = wp_styles();
		$enqueue_handle  = 'block-lab__global-styles';

		// Check that the correct stylesheet is enqueued.
		foreach ( $this->get_global_style_paths() as $key => $file ) {
			file_put_contents( $file, '' ); // @codingStandardsIgnoreLine
			$file_url = str_replace( untrailingslashit( ABSPATH ), '', $file );

			$this->instance->enqueue_global_styles();

			$this->assertContains( $enqueue_handle, $wp_styles->queue );
			$this->assertArrayHasKey( $enqueue_handle, $wp_styles->registered );
			$this->assertSame( $wp_styles->registered[ $enqueue_handle ]->src, $file_url, "Trying to enqueue file #{$key} ({$file_url})." );

			wp_deregister_style( $enqueue_handle );
			wp_dequeue_style( $enqueue_handle );
			unlink( $file );
		}
	}

	/**
	 * Test block_template.
	 *
	 * @covers \Block_Lab\Blocks\Loader::block_template()
	 */
	public function test_block_template() {
		ob_start();
		$this->instance->block_template( $this->mock_block_name );

		// If there is no template and the user does not have 'edit_posts' permissions, this should not output anything.
		$this->assertEmpty( ob_get_clean() );

		wp_set_current_user( $this->factory()->user->create( array( 'role' => 'administrator' ) ) );
		ob_start();
		$this->instance->block_template( $this->mock_block_name );
		$output = ob_get_clean();

		// There is still no template, but the user has the correct permissions, so this should output a warning.
		$this->assertContains( '<div class="notice notice-warning">', $output );
		$this->assertContains( $this->mock_block_name, $output );
		$this->assertContains( 'not found', $output );

		/*
		 * Test that the templates are used in the proper priority.
		 * This reverses the order of the $this->get_template_paths_in_theme(),
		 * as they were originally in order of descending priority.
		 * So in each iteration, the template should have a higher priority than the last, and should be used as the template.
		 * The templates won't be deleted until the tearDown() method after this test.
		 */
		$templates_in_parent_theme = array_reverse( $this->get_template_paths_in_theme() );
		foreach ( $templates_in_parent_theme as $template_location ) {
			$expected_template_contents = "This is content in the template {$template_location}";
			file_put_contents( $template_location, $expected_template_contents );

			ob_start();
			$this->instance->block_template( $this->mock_block_name );
			$this->assertContains( $expected_template_contents, ob_get_clean() );
		}

		$expected_overriden_template_contents = "This is content in the template {$this->overridden_theme_template_path}";
		file_put_contents( $this->overridden_theme_template_path, $expected_overriden_template_contents );

		// Test that this filter changes the template used.
		add_filter(
			'block_lab_override_theme_template',
			function( $directory ) {
				unset( $directory );
				return $this->overridden_theme_template_path;
			}
		);

		ob_start();
		$this->instance->block_template( $this->mock_block_name );
		$this->assertContains( $expected_overriden_template_contents, ob_get_clean() );
	}

	/**
	 * Gets the template paths for the the mock block, in order of descending priority.
	 *
	 * @return array The template paths in the parent theme.
	 */
	public function get_template_paths_in_theme() {
		return array_map(
			function( $template_location ) {
				return "{$this->theme_directory}/{$template_location}";
			},
			$this->template_locations
		);
	}

	/**
	 * Gets the directories that block templates and CSS files could be in.
	 *
	 * @return array
	 */
	public function get_block_template_directories() {
		return array(
			"{$this->theme_directory}/blocks/",
			"{$this->theme_directory}/blocks/css/",
			"{$this->theme_directory}/blocks/{$this->mock_block_name}/",
		);
	}

	/**
	 * Gets the full paths of the template CSS files, in order of reverse priority.
	 *
	 * @return array The paths of the template CSS files.
	 */
	public function get_template_css_paths() {
		return array(
			"{$this->theme_directory}/blocks/block-{$this->mock_block_name}.css",
			"{$this->theme_directory}/blocks/css/block-{$this->mock_block_name}.css",
			"{$this->theme_directory}/blocks/{$this->mock_block_name}/block.css",
			"{$this->theme_directory}/blocks/preview-{$this->mock_block_name}.css",
			"{$this->theme_directory}/blocks/css/preview-{$this->mock_block_name}.css",
			"{$this->theme_directory}/blocks/{$this->mock_block_name}/preview.css",
		);
	}

	/**
	 * Gets the paths to the global stylesheets, in order of reverse priority.
	 *
	 * @return array The possible global stylesheet paths.
	 */
	public function get_global_style_paths() {
		return array(
			"{$this->theme_directory}/blocks/blocks.css",
			"{$this->theme_directory}/blocks/css/blocks.css",
		);
	}
}
