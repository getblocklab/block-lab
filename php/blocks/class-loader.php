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

	/**
	 * Register all the hooks.
	 */
	public function register_hooks() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );
		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
		add_action( 'enqueue_block_assets', array( $this, 'frontend_assets' ) );
	}

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

	public function editor_assets() {

		wp_enqueue_script(
			'acb-blocks-js',
			$this->assets['url']['entry'],
			[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components' ],
			filemtime( $this->assets['path']['entry'] ),
			true
		);

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
}
