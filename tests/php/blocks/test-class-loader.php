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
			mkdir( $stylesheet_path . '/blocks/css/' );
		}

		// In order of reverse priority.
		$files = array(
			"{$stylesheet_path}/blocks/block-{$block_name}.css",
			"{$stylesheet_path}/blocks/css/block-{$block_name}.css",
			"{$stylesheet_path}/blocks/preview-{$block_name}.css",
			"{$stylesheet_path}/blocks/css/preview-{$block_name}.css",
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
}
