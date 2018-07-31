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
	public $assets = array();

	public function init() {
		$this->assets = [
			'path' => [
				'entry'        => $this->plugin->get_path( 'js/editor.blocks.js' ),
				'editor_style' => $this->plugin->get_path( 'css/blocks.editor.css' ),
				// 'block_style'       => $this->plugin->get_path( 'css/blocks.editor.css' ),
				// 'block_front_style' => $this->plugin->get_path( 'css/blocks.editor.css' ),
			],
			'url'  => [
				'entry'        => $this->plugin->get_url( 'js/editor.blocks.js' ),
				'editor_style' => $this->plugin->get_url( 'css/blocks.editor.css' ),
				// 'block_style'       => $this->plugin->get_url( 'css/blocks.editor.css' ),
				// 'block_front_style' => $this->plugin->get_url( 'css/blocks.editor.css' ),
			],
		];

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
		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
		add_action( 'enqueue_block_assets', array( $this, 'frontend_assets' ) );

		/**
		 * PHP block loading.
		 */
		add_action( 'plugins_loaded', array( $this, 'dynamic_block_loader' ) );
	}


	public function editor_assets() {

		wp_enqueue_script(
			'acb-blocks-js',
			$this->assets['url']['entry'],
			[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components' ],
			filemtime( $this->assets['path']['entry'] ),
			true
		);

		// Add dynamic Gutenberg blocks.
		wp_add_inline_script( 'acb-blocks-js', '
				const acbBlocks = ' . $this->mock_blocks() . ' 
			', 'before' );

		// Enqueue optional editor only styles
		wp_enqueue_style(
			'acb-blocks-editor-css',
			$this->assets['url']['editor_style'],
			[ 'wp-blocks' ],
			filemtime( $this->assets['path']['editor_style'] )
		);
	}

	public function block_assets() {

	}

	public function frontend_assets() {

	}

	/**
	 * Loads dynamic blocks via render_callback for each block.
	 * 
	 * @return bool
	 */
	public function dynamic_block_loader() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return false;
		}

		// Get blocks.
		$blocks = json_decode( $this->mock_blocks(), true );

		foreach ( $blocks as $block_name => $block ) {
			register_block_type( $block_name, [
				'attributes' => $this->get_block_attributes( $block ),
				'render_callback' => [ $this, 'render_block_template' ],
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

		foreach( $block['fields'] as $field_name => $field ) {
			$attributes[ $field_name ] = [];

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
	 * @todo load templates for blocks.
	 *
	 * @param $attributes
	 *
	 * @return mixed
	 */
	public function render_block_template( $attributes ) {
		return print_r( $attributes, true );
	}

	/**
	 * @todo Replace this with actual blocks json.
	 * @return false|string
	 */
	public function mock_blocks() {
		return wp_json_encode( [
			'advanced-custom-blocks/block-one' => [
				'title'       => __( 'ACB: Block1', 'advanced-custom-blocks' ),
				'description' => __( 'This should come from the PHP backend.', 'advanced-custom-blocks' ),
				'category'    => 'common',
				'icon'        => 'default',
				'keywords'    => [
					__( 'ACB Block1', 'advanced-custom-blocks' ),
				],
				'groups'      => [],
				'fields'      => [
					'post_id'     => [],
					'field_one'   => [
						'label'    => 'Field One',
						'type'     => 'string',
						'control'  => 'text',
						'icon'     => 'admin-generic',
						'location' => [
							'inspector',
						],
						'order'    => 1,
					],
					'fieldTwo'   => [
						'label'    => 'Field Two',
						'type'     => 'string',
						'control'  => 'textarea',
						'icon'     => 'admin-generic',
						'location' => [
							'inspector',
						],
						'order'    => 2,
					],
					'fieldThree' => [
						'label'    => 'Field Three',
						'type'     => 'string',
						'control'  => 'radio',
						'options'  => [
							'one' => 'Option One',
							'two' => 'Option Two',
						],
						'default'  => 'one',
						'icon'     => 'admin-generic',
						'location' => [
							'inspector',
							'editor'
						],
						'order'    => 3,
					],
					'fieldFour'  => [
						'label'    => 'Field Four',
						'type'     => 'string',
						'control'  => 'checkbox',
						'options'  => [
							'one' => 'Option One',
							'two' => 'Option Two',
						],
						'default'  => [ 'two' ],
						'icon'     => 'admin-generic',
						'location' => [
							'inspector',
							'editor'
						],
						'order'    => 4,
					],
					'fieldFive'  => [
						'label'    => 'Field Five',
						'type'     => 'string',
						'control'  => 'toggle',
						'default'  => 'off',
						'icon'     => 'admin-generic',
						'iconOn'   => 'admin-generic',
						'iconOff'  => 'admin-generic',
						'location' => [
							'toolbar',
							'inspector'
						],
						'order'    => 5,
					],
				],
			],
		] );
	}
}
