<?php
/**
 * Loader initiates the loading of new Gutenberg blocks for the AdvancedCustomBlocks plugin.
 *
 * @package AdvancedCustomBlocks
 */

namespace AdvancedCustomBlocks\Blocks;

use AdvancedCustomBlocks\ComponentAbstract;

/**
 * Class Loader
 */
class Loader extends ComponentAbstract {

	/**
	 * Asset paths and urls for blocks.
	 *
	 * @var array
	 */
	public $assets = [];

	/**
	 * JSON representing last loaded blocks.
	 *
	 * @var string
	 */
	public $blocks = '';

	public function init() {
		$this->assets = [
			'path' => [
				'entry'        => $this->plugin->get_path( 'js/editor.blocks.js' ),
				'editor_style' => $this->plugin->get_path( 'css/blocks.editor.css' ),
			],
			'url'  => [
				'entry'        => $this->plugin->get_url( 'js/editor.blocks.js' ),
				'editor_style' => $this->plugin->get_url( 'css/blocks.editor.css' ),
			],
		];

		$this->retrieve_blocks();

		return $this;
	}

	/**
	 * Register all the hooks.
	 */
	public function register_hooks() {
		/**
		 * Gutenberg JS block loading.
		 */
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );

		/**
		 * PHP block loading.
		 */
		add_action( 'plugins_loaded', array( $this, 'dynamic_block_loader' ) );
	}


	/**
	 * Launch the blocks inside Gutenberg.
	 */
	public function editor_assets() {

		wp_enqueue_script(
			'acb-blocks-js',
			$this->assets['url']['entry'],
			[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api-fetch' ],
			filemtime( $this->assets['path']['entry'] ),
			true
		);

		// Add dynamic Gutenberg blocks.
		wp_add_inline_script( 'acb-blocks-js', '
				const acbBlocks = ' . $this->blocks . ' 
			', 'before' );

		// Enqueue optional editor only styles
		wp_enqueue_style(
			'acb-blocks-editor-css',
			$this->assets['url']['editor_style'],
			[ 'wp-blocks' ],
			filemtime( $this->assets['path']['editor_style'] )
		);
	}

	/**
	 * Loads dynamic blocks via render_callback for each block.
	 */
	public function dynamic_block_loader() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Get blocks.
		$blocks = json_decode( $this->blocks, true );
		foreach ( $blocks as $block_name => $block ) {

			$attributes                   = $this->get_block_attributes( $block );
			$attributes['acb_block_name'] = $block_name;

			register_block_type( $block_name, [
				'attributes'      => $attributes,
				// @see https://github.com/WordPress/gutenberg/issues/4671
				'render_callback' => function ( $attributes ) use ( $block ) {
					return $this->render_block_template( $block, $attributes );
				},
			] );
		}
	}

	/**
	 * Gets block attributes.
	 *
	 * @param $block
	 *
	 * @return array
	 */
	public function get_block_attributes( $block ) {
		$attributes = [];

		foreach ( $block['fields'] as $field_name => $field ) {
			$attributes[ $field_name ] = [
				'type' => 'string',
			];

			if ( ! empty( $field['type'] ) ) {
				$attributes[ $field_name ]['type'] = $field['type'];
			}

			if ( ! empty( $field['source'] ) ) {
				$attributes[ $field_name ]['source'] = $field['source'];
			}

			if ( ! empty( $field['meta'] ) ) {
				$attributes[ $field_name ]['meta'] = $field['meta'];
			}

			if ( ! empty( $field['default'] ) ) {
				$attributes[ $field_name ]['default'] = $field['default'];
			}

			if ( ! empty( $field['selector'] ) ) {
				$attributes[ $field_name ]['selector'] = $field['selector'];
			}

			if ( ! empty( $field['query'] ) ) {
				$attributes[ $field_name ]['query'] = $field['query'];
			}
		}

		return $attributes;
	}

	/**
	 * Renders the block provided a template is provided.
	 *
	 * @param array        $block      The block to render.
	 * @param array        $attributes Attributes to render.
	 * @param string|array $type       The type of template to render.
	 *
	 * @return mixed
	 */
	public function render_block_template( $block, $attributes, $type = 'block' ) {
		global $acb_block_attributes;
		$acb_block_attributes = $attributes;

		ob_start();
		acb_template_part( $block['name'], $type );
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Load all the published blocks.
	 */
	public function retrieve_blocks() {

		$slug = 'acb_block';

		$this->blocks = '';
		$blocks       = [];

		$block_posts = new \WP_Query( [
			'post_type'      => $slug,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		] );

		if ( 0 < $block_posts->post_count ) {
			/** @var \WP_Post $post */
			foreach ( $block_posts->posts as $post ) {
				$blocks = array_merge( $blocks, json_decode( $post->post_content, true ) );
			}
		}
		$this->blocks = wp_json_encode( $blocks );
	}
}
