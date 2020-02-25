<?php
/**
 * Loader initiates the loading of new blocks.
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
	protected $assets = [];

	/**
	 * An associative array of block config data for the blocks that will be registered.
	 *
	 * The key of each item in the array is the block name.
	 *
	 * @var array
	 */
	protected $blocks = [];

	/**
	 * A data store for sharing data to helper functions.
	 *
	 * @var array
	 */
	protected $data = [];

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

		return $this;
	}

	/**
	 * Register all the hooks.
	 */
	public function register_hooks() {
		/**
		 * Gutenberg JS block loading.
		 */
		add_action( 'enqueue_block_editor_assets', $this->get_callback( 'editor_assets' ) );

		/**
		 * Gutenberg custom categories.
		 */
		add_filter( 'block_categories', $this->get_callback( 'register_categories' ) );

		/**
		 * Block retrieval, must run before dynamic_block_loader().
		 */
		add_action( 'init', $this->get_callback( 'retrieve_blocks' ) );

		/**
		 * PHP block loading.
		 */
		add_action( 'init', $this->get_callback( 'dynamic_block_loader' ) );
	}

	/**
	 * Retrieve data from the Loader's data store.
	 *
	 * @param string $key The data key to retrieve.
	 * @return mixed
	 */
	public function get_data( $key ) {
		$data = false;

		if ( isset( $this->data[ $key ] ) ) {
			$data = $this->data[ $key ];
		}

		/**
		 * Filters the data that gets returned.
		 *
		 * @param mixed  $data The data from the Loader's data store.
		 * @param string $key  The key for the data being retreived.
		 */
		$data = apply_filters( 'block_lab_data', $data, $key );

		/**
		 * Filters the data that gets returned, specifically for a single key.
		 *
		 * @param mixed  $data The data from the Loader's data store.
		 */
		$data = apply_filters( "block_lab_data_{$key}", $data );

		return $data;
	}

	/**
	 * Gets the callback for an action or filter.
	 *
	 * Enables keeping these methods protected,
	 * while allowing actions and filters to call them.
	 *
	 * @param string $method_name The name of the method to get the callback for.
	 * @return callable An enclosure that calls the function.
	 */
	protected function get_callback( $method_name ) {
		return function( $arg ) use ( $method_name ) {
			return call_user_func( [ $this, $method_name ], $arg );
		};
	}

	/**
	 * Launch the blocks inside Gutenberg.
	 */
	protected function editor_assets() {
		$asset_config = require $this->plugin->get_path( 'js/editor.blocks.asset.php' );

		wp_enqueue_script(
			'block-lab-blocks',
			$this->assets['url']['entry'],
			$asset_config['dependencies'],
			$asset_config['version'],
			true
		);

		// Add dynamic Gutenberg blocks.
		wp_add_inline_script(
			'block-lab-blocks',
			'const blockLabBlocks = ' . wp_json_encode( $this->blocks ),
			'before'
		);

		// Used to conditionally show notices for blocks belonging to an author.
		$author_blocks = get_posts(
			[
				'author'         => get_current_user_id(),
				'post_type'      => 'block_lab',
				// We could use -1 here, but that could be dangerous. 99 is more than enough.
				'posts_per_page' => 99,
			]
		);

		$author_block_slugs = wp_list_pluck( $author_blocks, 'post_name' );
		wp_localize_script(
			'block-lab-blocks',
			'blockLab',
			[
				'authorBlocks' => $author_block_slugs,
				'postType'     => get_post_type(), // To conditionally exclude blocks from certain post types.
			]
		);

		// Enqueue optional editor only styles.
		wp_enqueue_style(
			'block-lab-editor-css',
			$this->assets['url']['editor_style'],
			[],
			$this->plugin->get_version()
		);

		$block_names = wp_list_pluck( $this->blocks, 'name' );

		foreach ( $block_names as $block_name ) {
			$this->enqueue_block_styles( $block_name, [ 'preview', 'block' ] );
		}

		$this->enqueue_global_styles();
	}

	/**
	 * Loads dynamic blocks via render_callback for each block.
	 */
	protected function dynamic_block_loader() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		foreach ( $this->blocks as $block_name => $block_config ) {
			$block = new Block();
			$block->from_array( $block_config );
			$this->register_block( $block_name, $block );
		}
	}

	/**
	 * Registers a block.
	 *
	 * @param string $block_name The name of the block, including namespace.
	 * @param Block  $block      The block to register.
	 */
	protected function register_block( $block_name, $block ) {
		$attributes = $this->get_block_attributes( $block );

		// sanitize_title() allows underscores, but register_block_type doesn't.
		$block_name = str_replace( '_', '-', $block_name );

		// register_block_type doesn't allow slugs starting with a number.
		if ( is_numeric( $block_name[0] ) ) {
			$block_name = 'block-' . $block_name;
		}

		register_block_type(
			$block_name,
			[
				'attributes'      => $attributes,
				// @see https://github.com/WordPress/gutenberg/issues/4671
				'render_callback' => function ( $attributes ) use ( $block ) {
					return $this->render_block_template( $block, $attributes );
				},
			]
		);
	}

	/**
	 * Register custom block categories.
	 *
	 * @param array $categories Array of block categories.
	 *
	 * @return array
	 */
	protected function register_categories( $categories ) {
		foreach ( $this->blocks as $block_config ) {
			if ( ! isset( $block_config['category'] ) ) {
				continue;
			}

			/*
			 * This is a backwards compatibility fix.
			 *
			 * Block categories used to be saved as strings, but were always included in
			 * the default list of categories, so it's safe to skip them.
			 */
			if ( ! is_array( $block_config['category'] ) || empty( $block_config['category'] ) ) {
				continue;
			}

			if ( ! in_array( $block_config['category'], $categories, true ) ) {
				$categories[] = $block_config['category'];
			}
		}

		return $categories;
	}

	/**
	 * Gets block attributes.
	 *
	 * @param Block $block The block to get attributes from.
	 *
	 * @return array
	 */
	protected function get_block_attributes( $block ) {
		$attributes = [];

		// Default Editor attributes (applied to all blocks).
		$attributes['className'] = [ 'type' => 'string' ];

		foreach ( $block->fields as $field_name => $field ) {
			$attributes = $this->get_attributes_from_field( $attributes, $field_name, $field );
		}

		/**
		 * Filters a given block's attributes.
		 *
		 * These are later passed to register_block_type() in $args['attributes'].
		 * Removing attributes here can cause 'Error loading block...' in the editor.
		 *
		 * @param array[] $attributes The attributes for a block.
		 * @param array   $block      Block data, including its name at $block['name'].
		 */
		return apply_filters( 'block_lab_get_block_attributes', $attributes, $block );
	}

	/**
	 * Sets the field values in the attributes, enabling them to appear in the block.
	 *
	 * @param array  $attributes The attributes in which to store the field value.
	 * @param string $field_name The name of the field, like 'home-hero'.
	 * @param Field  $field      The Field to set the attributes from.
	 * @return array $attributes The attributes, with the new field value set.
	 */
	protected function get_attributes_from_field( $attributes, $field_name, $field ) {
		$attributes[ $field_name ] = [
			'type' => $field->type,
		];

		if ( ! empty( $field->settings['default'] ) ) {
			$attributes[ $field_name ]['default'] = $field->settings['default'];
		}

		if ( 'array' === $field->type ) {
			/**
			 * This is a workaround to allow empty array values. We unset the default value before registering the
			 * block so that the default isn't used to auto-correct empty arrays. This allows the default to be
			 * used only when creating the form.
			 */
			unset( $attributes[ $field_name ]['default'] );
			$items_type                         = 'repeater' === $field->control ? 'object' : 'string';
			$attributes[ $field_name ]['items'] = [ 'type' => $items_type ];
		}

		return $attributes;
	}

	/**
	 * Renders the block provided a template is provided.
	 *
	 * @param Block $block The block to render.
	 * @param array $attributes Attributes to render.
	 *
	 * @return mixed
	 */
	protected function render_block_template( $block, $attributes ) {
		$type = 'block';

		// This is hacky, but the editor doesn't send the original request along.
		$context = filter_input( INPUT_GET, 'context', FILTER_SANITIZE_STRING );

		if ( 'edit' === $context ) {
			$type = [ 'preview', 'block' ];
		}

		if ( ! is_admin() ) {
			/**
			 * The block has been added, but its values weren't saved (not even the defaults). This is a phenomenon
			 * unique to frontend output, as the editor fetches its attributes from the form fields themselves.
			 */
			$missing_schema_attributes = array_diff_key( $block->fields, $attributes );
			foreach ( $missing_schema_attributes as $attribute_name => $schema ) {
				if ( isset( $schema->settings['default'] ) ) {
					$attributes[ $attribute_name ] = $schema->settings['default'];
				}
			}

			// Similar to the logic above, populate the Repeater control's sub-fields with default values.
			foreach ( $block->fields as $field ) {
				if ( isset( $field->settings['sub_fields'] ) && isset( $attributes[ $field->name ]['rows'] ) ) {
					$sub_field_settings = $field->settings['sub_fields'];
					$rows               = $attributes[ $field->name ]['rows'];

					// In each row, apply a field's default value if a value doesn't exist in the attributes.
					foreach ( $rows as $row_index => $row ) {
						foreach ( $sub_field_settings as $sub_field_name => $sub_field ) {
							if ( ! isset( $row[ $sub_field_name ] ) && isset( $sub_field_settings[ $sub_field_name ]->settings['default'] ) ) {
								$rows[ $row_index ][ $sub_field_name ] = $sub_field_settings[ $sub_field_name ]->settings['default'];
							}
						}
					}

					$attributes[ $field->name ]['rows'] = $rows;
				}
			}

			$this->enqueue_block_styles( $block->name, 'block' );

			/**
			 * The wp_enqueue_style function handles duplicates, so we don't need to worry about multiple blocks
			 * loading the global styles more than once.
			 */
			$this->enqueue_global_styles();
		}

		$this->data['attributes'] = $attributes;
		$this->data['config']     = $block;

		if ( ! is_admin() && ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) && ! wp_doing_ajax() ) {

			/**
			 * Runs in the 'render_callback' of the block, and only on the front-end, not in the editor.
			 *
			 * The block's name (slug) is in $block->name.
			 * If a block depends on a JavaScript file,
			 * this action is a good place to call wp_enqueue_script().
			 * In that case, pass true as the 5th argument ($in_footer) to wp_enqueue_script().
			 *
			 * @param Block $block The block that is rendered.
			 * @param array $attributes The block attributes.
			 */
			do_action( 'block_lab_render_template', $block, $attributes );

			/**
			 * Runs in a block's 'render_callback', and only on the front-end.
			 *
			 * Same as the action above, but with a dynamic action name that has the block name.
			 *
			 * @param Block $block The block that is rendered.
			 * @param array $attributes The block attributes.
			 */
			do_action( "block_lab_render_template_{$block->name}", $block, $attributes );
		}

		ob_start();
		$this->block_template( $block->name, $type );
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Enqueues styles for the block.
	 *
	 * @param string       $name The name of the block (slug as defined in UI).
	 * @param string|array $type The type of template to load.
	 */
	protected function enqueue_block_styles( $name, $type = 'block' ) {
		$locations = [];
		$types     = (array) $type;

		foreach ( $types as $type ) {
			$locations = array_merge(
				$locations,
				block_lab()->get_stylesheet_locations( $name, $type )
			);
		}

		$stylesheet_path = block_lab()->locate_template( $locations );
		$stylesheet_url  = block_lab()->get_url_from_path( $stylesheet_path );

		/**
		 * Enqueue the stylesheet, if it exists. The wp_enqueue_style function handles duplicates, so we don't need
		 * to worry about the same block loading its stylesheets more than once.
		 */
		if ( ! empty( $stylesheet_url ) ) {
			wp_enqueue_style(
				"block-lab__block-{$name}",
				$stylesheet_url,
				[],
				wp_get_theme()->get( 'Version' )
			);
		}
	}

	/**
	 * Enqueues global block styles.
	 */
	protected function enqueue_global_styles() {
		$locations = [
			'blocks/css/blocks.css',
			'blocks/blocks.css',
		];

		$stylesheet_path = block_lab()->locate_template( $locations );
		$stylesheet_url  = block_lab()->get_url_from_path( $stylesheet_path );

		/**
		 * Enqueue the stylesheet, if it exists.
		 */
		if ( ! empty( $stylesheet_url ) ) {
			wp_enqueue_style(
				'block-lab__global-styles',
				$stylesheet_url,
				[],
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
	protected function block_template( $name, $type = 'block' ) {
		// Loading async it might not come from a query, this breaks load_template().
		global $wp_query;

		// So lets fix it.
		if ( empty( $wp_query ) ) {
			$wp_query = new \WP_Query(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}

		$types   = (array) $type;
		$located = '';

		foreach ( $types as $type ) {
			$templates = block_lab()->get_template_locations( $name, $type );
			$located   = block_lab()->locate_template( $templates );

			if ( ! empty( $located ) ) {
				break;
			}
		}

		if ( ! empty( $located ) ) {
			$theme_template = apply_filters( 'block_lab_override_theme_template', $located );

			// This is not a load once template, so require_once is false.
			load_template( $theme_template, false );
		} else {
			if ( ! current_user_can( 'edit_posts' ) || ! isset( $templates[0] ) ) {
				return;
			}
			// Hide the template not found notice on the frontend, unless WP_DEBUG is enabled.
			if ( ! is_admin() && ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
				return;
			}
			printf(
				'<div class="notice notice-warning">%s</div>',
				wp_kses_post(
					// Translators: Placeholder is a file path.
					sprintf( __( 'Template file %s not found.', 'block-lab' ), '<code>' . esc_html( $templates[0] ) . '</code>' )
				)
			);
		}
	}

	/**
	 * Load all the published blocks and blocks/block.json files.
	 */
	protected function retrieve_blocks() {
		/**
		 * Retrieve blocks from blocks.json.
		 * Reverse to preserve order of preference when using array_merge.
		 */
		$blocks_files = array_reverse( (array) block_lab()->locate_template( 'blocks/blocks.json', '', false ) );
		foreach ( $blocks_files as $blocks_file ) {
			// This is expected to be on the local filesystem, so file_get_contents() is ok to use here.
			$json       = file_get_contents( $blocks_file ); // @codingStandardsIgnoreLine
			$block_data = json_decode( $json, true );

			// Merge if no json_decode error occurred.
			if ( json_last_error() == JSON_ERROR_NONE ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$this->blocks = array_merge( $this->blocks, $block_data );
			}
		}

		/**
		 * Retrieve blocks stored as posts in the WordPress database.
		 */
		$block_posts = new \WP_Query(
			[
				'post_type'      => block_lab()->get_post_type_slug(),
				'post_status'    => 'publish',
				'posts_per_page' => 100, // This has to have a limit for this plugin to be scalable.
			]
		);

		if ( 0 < $block_posts->post_count ) {
			/** The WordPress Post object. @var \WP_Post $post */
			foreach ( $block_posts->posts as $post ) {
				$block_data = json_decode( $post->post_content, true );

				// Merge if no json_decode error occurred.
				if ( json_last_error() == JSON_ERROR_NONE ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					$this->blocks = array_merge( $this->blocks, $block_data );
				}
			}
		}

		/**
		 * Use this action to add new blocks and fields with the block_lab_add_block and block_lab_add_field helper functions.
		 */
		do_action( 'block_lab_add_blocks' );

		/**
		 * Filter the available blocks.
		 *
		 * This is used internally by the block_lab_add_block and block_lab_add_field helper functions,
		 * but it can also be used to hide certain blocks if desired.
		 *
		 * @param array $blocks An associative array of blocks.
		 */
		$this->blocks = apply_filters( 'block_lab_blocks', $this->blocks );
	}

	/**
	 * Add a new block.
	 *
	 * This method should be called during the block_lab_add_blocks action, to ensure
	 * that the block isn't added too late.
	 *
	 * @param array $block_config The config of the block to add.
	 */
	public function add_block( $block_config ) {
		if ( ! isset( $block_config['name'] ) ) {
			return;
		}

		$this->blocks[ "block-lab/{$block_config['name']}" ] = $block_config;
	}

	/**
	 * Add a new field to an existing block.
	 *
	 * This method should be called during the block_lab_add_blocks action, to ensure
	 * that the block isn't added too late.
	 *
	 * @param string $block_name   The name of the block that the field is added to.
	 * @param array  $field_config The config of the field to add.
	 */
	public function add_field( $block_name, $field_config ) {
		if ( ! isset( $this->blocks[ "block-lab/{$block_name}" ] ) ) {
			return;
		}
		if ( ! isset( $field_config['name'] ) ) {
			return;
		}

		$this->blocks[ "block-lab/{$block_name}" ]['fields'][ $field_config['name'] ] = $field_config;
	}
}
