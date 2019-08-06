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
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Blocks\Loader();
		$this->instance->set_plugin( block_lab() );
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

		$block_name      = 'mock-block';
		$block_handle    = "block-lab__block-{$block_name}";
		$stylesheet_path = get_template_directory();

		if ( ! file_exists( $stylesheet_path . '/blocks/' ) ) {
			mkdir( $stylesheet_path . '/blocks/' );
		}
		if ( ! file_exists( $stylesheet_path . '/blocks/css/' ) ) {
			mkdir( $stylesheet_path . '/blocks/css/' );
		}
		if ( ! file_exists( $stylesheet_path . "/blocks/{$block_name}/" ) ) {
			mkdir( $stylesheet_path . "/blocks/{$block_name}/" );
		}

		// In order of reverse priority.
		$files = array(
			"{$stylesheet_path}/blocks/block-{$block_name}.css",
			"{$stylesheet_path}/blocks/css/block-{$block_name}.css",
			"{$stylesheet_path}/blocks/{$block_name}/block.css",
			"{$stylesheet_path}/blocks/preview-{$block_name}.css",
			"{$stylesheet_path}/blocks/css/preview-{$block_name}.css",
			"{$stylesheet_path}/blocks/{$block_name}/preview.css",
		);

		// Remove previous template files so that we can correctly check load order.
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				unlink( $file );
			}
		}

		// Check that the correct stylesheet is enqueued.
		foreach ( $files as $key => $file ) {
			file_put_contents( $file, '' ); // @codingStandardsIgnoreLine
			$file_url = str_replace( untrailingslashit( ABSPATH ), '', $file );

			$this->instance->enqueue_block_styles( $block_name, array( 'preview', 'block' ) );
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
		$stylesheet_path = get_template_directory();

		if ( ! file_exists( $stylesheet_path . '/blocks/' ) ) {
			mkdir( $stylesheet_path . '/blocks/' );
			mkdir( $stylesheet_path . '/blocks/css/' );
		}

		// In order of reverse priority.
		$files = array(
			"{$stylesheet_path}/blocks/blocks.css",
			"{$stylesheet_path}/blocks/css/blocks.css",
		);

		// Check that the correct stylesheet is enqueued.
		foreach ( $files as $key => $file ) {
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
}
