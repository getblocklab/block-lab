<?php
/**
 * Loader initiates the loading of new Gutenberg blocks for the Block_Lab plugin.
 *
 * @package Block_Lab
 */

namespace Block_Lab\Blocks;

use Block_Lab\Component_Abstract;

/**
 * Class Loader
 */
class Loader extends Component_Abstract {

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

	/**
	 * Load the Loader.
	 *
	 * @return $this
	 */
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
			'block-lab-blocks',
			$this->assets['url']['entry'],
			array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api-fetch' ),
			$this->plugin->get_version(),
			true
		);

		// Add dynamic Gutenberg blocks.
		wp_add_inline_script(
			'block-lab-blocks',
			'const blockLabBlocks = ' . $this->blocks,
			'before'
		);

		// Enqueue optional editor only styles.
		wp_enqueue_style(
			'block-lab-editor-css',
			$this->assets['url']['editor_style'],
			array(),
			$this->plugin->get_version()
		);

		$blocks = json_decode( $this->blocks, true );

		if ( ! empty( $blocks ) ) {
			foreach ( $blocks as $block_name => $block ) {
				$this->enqueue_block_styles( $block['name'], array( 'preview', 'block' ) );
			}
		}
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
			$attributes = $this->get_block_attributes( $block );

			// sanitize_title() allows underscores, but register_block_type doesn't.
			$block_name = str_replace( '_', '-', $block_name );

			// register_block_type doesn't allow slugs starting with a number.
			if ( is_numeric( $block_name[0] ) ) {
				$block_name = 'block-' . $block_name;
			}

			register_block_type(
				$block_name,
				array(
					'attributes'      => $attributes,
					// @see https://github.com/WordPress/gutenberg/issues/4671
					'render_callback' => function ( $attributes ) use ( $block ) {
						return $this->render_block_template( $block, $attributes );
					},
				)
			);
		}
	}

	/**
	 * Gets block attributes.
	 *
	 * @param array $block An array containing block data.
	 *
	 * @return array
	 */
	public function get_block_attributes( $block ) {
		$attributes = [];

		if ( ! isset( $block['fields'] ) ) {
			return $attributes;
		}

		foreach ( $block['fields'] as $field_name => $field ) {
			$attributes[ $field_name ] = [];

			if ( ! empty( $field['type'] ) ) {
				$attributes[ $field_name ]['type'] = $field['type'];
			} else {
				$attributes[ $field_name ]['type'] = 'string';
			}

			if ( ! empty( $field['default'] ) ) {
				$attributes[ $field_name ]['default'] = $field['default'];
			}

			if ( 'array' === $field['type'] ) {
				/**
				 * This is a workaround to allow empty array values. We unset the default value before registering the
				 * block so that the default isn't used to auto-correct empty arrays. This allows the default to be
				 * used only when creating the form.
				 */
				unset( $attributes[ $field_name ]['default'] );
				$attributes[ $field_name ]['items'] = array( 'type' => 'string' );
			}

			if ( ! empty( $field['source'] ) ) {
				$attributes[ $field_name ]['source'] = $field['source'];
			}

			if ( ! empty( $field['meta'] ) ) {
				$attributes[ $field_name ]['meta'] = $field['meta'];
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
	 * @param array $block      The block to render.
	 * @param array $attributes Attributes to render.
	 *
	 * @return mixed
	 */
	public function render_block_template( $block, $attributes ) {
		global $block_lab_attributes, $block_lab_config;

		$type = 'block';

		// This is hacky, but the editor doesn't send the original request along.
		$context = filter_input( INPUT_GET, 'context', FILTER_SANITIZE_STRING );

		if ( 'edit' === $context ) {
			$type = array( 'preview', 'block' );
		}

		if ( ! is_admin() ) {
			/**
			 * The block has been added, but its values weren't saved (not even the defaults). This is a phenomenon
			 * unique to frontend output, as the editor fetches its attributes from the form fields themselves.
			 */
			$missing_schema_attributes = array_diff_key( $block['fields'], $attributes );
			foreach ( $missing_schema_attributes as $attribute_name => $schema ) {
				if ( isset( $schema['default'] ) ) {
					$attributes[ $attribute_name ] = $schema['default'];
				}
			}

			$this->enqueue_block_styles( $block['name'], 'block' );
		}

		$block_lab_attributes = $attributes;
		$block_lab_config     = $block;

		ob_start();
		$this->block_template( $block['name'], $type );
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Enqueues styles for the block.
	 *
	 * @param string       $name The name of the block (slug as defined in UI).
	 * @param string|array $type The type of template to load.
	 */
	public function enqueue_block_styles( $name, $type = 'block' ) {
		$locations = array();
		$types     = (array) $type;

		foreach ( $types as $type ) {
			$locations = array_merge(
				$locations,
				array(
					"blocks/css/{$type}-{$name}.css",
					"blocks/{$type}-{$name}.css",
				)
			);
		}

		$stylesheet_path = block_lab_locate_template( $locations );
		$stylesheet_url  = str_replace( untrailingslashit( ABSPATH ), '', $stylesheet_path );

		/**
		 * Enqueue the stylesheet, if it exists. The wp_enqueue_style function handles duplicates, so we don't need
		 * to worry about the same block loading its stylesheets more than once.
		 */
		if ( ! empty( $stylesheet_url ) ) {
			wp_enqueue_style(
				"block-lab__block-{$name}",
				$stylesheet_url,
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}

	/**
	 * Loads a block template to render the block.
	 *
	 * @param string       $name The name of the block (slug as defined in UI).
	 * @param string|array $type The type of template to load.
	 */
	public function block_template( $name, $type = 'block' ) {
		// Loading async it might not come from a query, this breaks load_template().
		global $wp_query;

		// So lets fix it.
		if ( empty( $wp_query ) ) {
			$wp_query = new \WP_Query(); // Override okay.
		}

		$types         = (array) $type;
		$located       = '';
		$template_file = '';

		foreach ( $types as $type ) {

			if ( ! empty( $located ) ) {
				continue;
			}

			$template_file = "blocks/{$type}-{$name}.php";
			$generic_file  = "blocks/{$type}.php";
			$templates     = [
				$generic_file,
				$template_file,
			];

			$located = block_lab_locate_template( $templates );
		}

		if ( ! empty( $located ) ) {
			$theme_template = apply_filters( 'block_lab_override_theme_template', $located );

			// This is not a load once template, so require_once is false.
			load_template( $theme_template, false );
		} else {
			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}
			printf(
				'<div class="notice notice-warning">%s</div>',
				wp_kses_post(
					// Translators: Placeholder is a file path.
					sprintf( __( 'Template file %s not found.' ), '<code>' . esc_html( $template_file ) . '</code>' )
				)
			);
		}
	}

	/**
	 * Load all the published blocks and blocks/block.json files.
	 */
	public function retrieve_blocks() {
		$slug = 'block_lab';

		$this->blocks = '';
		$blocks       = [];

		// Retrieve blocks from blocks.json.
		// Reverse to preserve order of preference when using array_merge.
		$blocks_files = array_reverse( (array) block_lab_locate_template( 'blocks/blocks.json', '', false ) );
		foreach ( $blocks_files as $blocks_file ) {
			// This is expected to be on the local filesystem, so file_get_contents() is ok to use here.
			$json       = file_get_contents( $blocks_file ); // @codingStandardsIgnoreLine
			$block_data = json_decode( $json, true );

			// Merge if no json_decode error occurred.
			if ( json_last_error() == JSON_ERROR_NONE ) { // Loose comparison okay.
				$blocks = array_merge( $blocks, $block_data );
			}
		}

		$block_posts = new \WP_Query(
			[
				'post_type'      => $slug,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			]
		);

		if ( 0 < $block_posts->post_count ) {
			/** The WordPress Post object. @var \WP_Post $post */
			foreach ( $block_posts->posts as $post ) {
				$block_data = json_decode( $post->post_content, true );

				// Merge if no json_decode error occurred.
				if ( json_last_error() == JSON_ERROR_NONE ) { // Loose comparison okay.
					$blocks = array_merge( $blocks, $block_data );
				}
			}
		}

		$this->blocks = wp_json_encode( $blocks );
	}
}
