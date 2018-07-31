<?php
/**
 * Block Post Type.
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace AdvancedCustomBlocks\PostTypes;

use AdvancedCustomBlocks\ComponentAbstract;

/**
 * Class Plugin
 */
class BlockPostType extends ComponentAbstract {

	/**
	 * Slug used for the custom post type.
	 *
	 * @var String
	 */
	public $slug = 'acb_block';

	/**
	 * Register any hooks that this component needs.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_filter( 'enter_title_here', array( $this, 'post_title_placeholder' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Clean up the list table
		add_filter( 'bulk_actions-edit-' . $this->slug, '__return_empty_array' );
		add_filter( 'disable_months_dropdown', '__return_true', 10, $this->slug );
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'list_tables_style' ) );
	}

	/**
	 * Register the custom post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Custom Blocks', 'post type general name', 'advanced-custom-blocks' ),
			'singular_name'      => _x( 'Custom Block', 'post type singular name', 'advanced-custom-blocks' ),
			'menu_name'          => _x( 'Custom Blocks', 'admin menu', 'advanced-custom-blocks' ),
			'name_admin_bar'     => _x( 'Custom Block', 'add new on admin bar', 'advanced-custom-blocks' ),
			'add_new'            => _x( 'Add New', 'book', 'advanced-custom-blocks' ),
			'add_new_item'       => __( 'Add New Custom Block', 'advanced-custom-blocks' ),
			'new_item'           => __( 'New Custom Block', 'advanced-custom-blocks' ),
			'edit_item'          => __( 'Edit Custom Block', 'advanced-custom-blocks' ),
			'view_item'          => __( 'View Custom Block', 'advanced-custom-blocks' ),
			'all_items'          => __( 'Custom Blocks', 'advanced-custom-blocks' ),
			'search_items'       => __( 'Search Custom Blocks', 'advanced-custom-blocks' ),
			'parent_item_colon'  => __( 'Parent Custom Blocks:', 'advanced-custom-blocks' ),
			'not_found'          => __( 'No custom blocks found.', 'advanced-custom-blocks' ),
			'not_found_in_trash' => __( 'No custom blocks found in Trash.', 'advanced-custom-blocks' )
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'acb',
			'query_var'       => true,
			'rewrite'         => array( 'slug' => 'acb_block' ),
			'capability_type' => 'post',
			'supports'        => array( 'title' )
		);

		register_post_type( $this->slug, $args );
	}

	/**
	 * Enqueue scripts and styles used by the Block post type.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( ! is_object( $screen ) ) {
			return;
		}

		// Enqueue scripts and styles on the edit screen of the Block post type.
		if ( $this->slug === $screen->post_type && 'post' === $screen->base ) {
			wp_enqueue_style(
					'block-post',
					$this->plugin->get_url( 'css/admin.block-post.css' ),
					array(),
					filemtime( $this->plugin->get_path( 'css/admin.block-post.css' ) )
			);
			wp_enqueue_script(
					'block-post',
					$this->plugin->get_url( 'js/admin.block-post.js' ),
					array( 'jquery', 'jquery-ui-sortable', 'wp-util', 'wp-blocks' ),
					filemtime( $this->plugin->get_path( 'js/admin.block-post.js' ) )
			);
		}
	}

	/**
	 * Add meta boxes.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'acb_block_properties',
			__( 'Block Properties', 'advanced-custom-blocks' ),
			array( $this, 'render_properties_meta_box' ),
			$this->slug,
			'normal',
			'high'
		);

		add_meta_box(
			'acb_block_fields',
			__( 'Block Fields', 'advanced-custom-blocks' ),
			array( $this, 'render_fields_meta_box' ),
			$this->slug,
			'normal',
			'high'
		);

		add_meta_box(
			'acb_block_template',
			__( 'Template', 'advanced-custom-blocks' ),
			array( $this, 'render_template_meta_box' ),
			$this->slug,
			'side',
			'default'
		);
	}

	/**
	 * Render the Block Fields meta box.
	 *
	 * @return void
	 */
	public function render_properties_meta_box() {
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="acb-properties-category">
						<?php esc_html_e( 'Category', 'advanced-custom-blocks' ); ?>
					</label>
				</th>
				<td>
					<p>
						<select name="acb-properties-category" id="acb-properties-category">
							<option value="__custom">
								<?php esc_html_e( '+ New Category', 'advanced-custom-blocks' ); ?>
							</option>
						</select>
					</p>
					<p>
						<input
							name="acb-properties-category-custom"
							title="<?php esc_html_e( 'Custom Category', 'advanced-custom-blocks' ); ?>"
							type="text"
							id="acb-properties-category-custom"
							value=""
							placeholder="<?php esc_html_e( 'Category Name', 'advanced-custom-blocks' ); ?>"
							class="regular-text hidden">
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="acb-properties-description">
						<?php esc_html_e( 'Description', 'advanced-custom-blocks' ); ?>
					</label>
				</th>
				<td>
					<p>
						<textarea
							name="acb-properties-description"
							id="acb-properties-description"
							class="large-text"
							rows="3"></textarea>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="acb-properties-keywords">
						<?php esc_html_e( 'Keywords', 'advanced-custom-blocks' ); ?>
					</label>
					<p class="description" id="acb-properties-keywords-description">
						<?php
						esc_html_e(
							'A comma separated list of keywords, used when searching.',
							'advanced-custom-blocks'
						);
						?>
					</p>
				</th>
				<td>
					<p>
						<input
							name="acb-properties-keywords"
							type="text"
							id="acb-properties-keywords"
							value=""
							class="regular-text">
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render the Block Fields meta box.
	 *
	 * @return void
	 */
	public function render_fields_meta_box() {
		$fields = array(
			array(
				'name' => 'foo',
				'label' => 'Foo',
				'type' => 'text',
			),
			array(
				'name' => 'bar',
				'label' => 'Bar',
				'type' => 'text',
			),
			array(
				'name' => 'baz',
				'label' => 'Baz',
				'type' => 'textarea',
			),
		);
		?>
		<div class="acb-fields-list">
			<table class="widefat">
				<thead>
					<tr>
						<th class="acb-fields-sort"></th>
						<th class="acb-fields-label">
							<?php esc_html_e( 'Field Label', 'advanced-custom-blocks' ); ?>
						</th>
						<th class="acb-fields-name">
							<?php esc_html_e( 'Field Name', 'advanced-custom-blocks' ); ?>
						</th>
						<th class="acb-fields-type">
							<?php esc_html_e( 'Field Type', 'advanced-custom-blocks' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="4" class="acb-fields-rows">
							<?php
							foreach( $fields as $index => $field ) {
								$this->render_fields_meta_box_row( $field, uniqid() );
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="acb-fields-actions">
			<input
				name="add-field"
				type="button"
				class="button button-primary button-large"
				id="acb-add-field"
				value="<?php esc_attr_e( '+ Add Field', 'advanced-custom-blocks' ); ?>" />

			<script type="text/html" id="tmpl-field-repeater">
				<?php
				$this->render_fields_meta_box_row( array(
					'name' => 'new_field',
					'label' => __( 'New Field', 'advanced-custom-blocks' ),
					'type' => 'text'
				) );
				?>
			</script>
		</div>
		<?php
	}

	/**
	 * Render a single Field as a row.
	 *
	 * @param array $field
	 * @param mixed $uid
	 *
	 * @return void
	 */
	public function render_fields_meta_box_row( $field, $uid = false ) {
		// Use a template placeholder if no UID provided.
		if ( ! $uid ) {
			$uid = '{{ data.uid }}';
		}
		?>
		<div class="acb-fields-row">
			<div class="acb-fields-sort">
				<span class="acb-fields-sort-handle"></span>
			</div>
			<div class="acb-fields-label">
				<a class="row-title" href="javascript:" id="acb-fields-label_<?php echo esc_attr( $uid ); ?>">
					<?php echo esc_html( $field['label'] ); ?>
				</a>
				<div class="acb-fields-options">
					<a class="acb-fields-options-edit" href="javascript:">
						<?php esc_html_e( 'Edit', 'advanced-custom-blocks'); ?>
					</a>
					&nbsp;|&nbsp;
					<a class="acb-fields-options-delete" href="javascript:">
						<?php esc_html_e( 'Delete', 'advanced-custom-blocks'); ?>
					</a>
				</div>
			</div>
			<div class="acb-fields-name" id="acb-fields-name_<?php echo esc_attr( $uid ); ?>">
				<?php echo esc_html( $field['name'] ); ?>
			</div>
			<div class="acb-fields-type" id="acb-fields-type_<?php echo esc_attr( $uid ); ?>">
				<?php echo esc_html( $field['type'] ); ?>
			</div>
			<div class="acb-fields-edit">
				<table class="widefat">
					<tr class="acb-fields-edit-label">
						<th scope="row">
							<label for="acb-fields-edit-label-input_<?php echo esc_attr( $uid ); ?>">
								<?php esc_html_e( 'Field Label', 'advanced-custom-blocks' ); ?>
							</label>
							<p class="description" id="acb-fields-edit-label-description">
								<?php
								esc_html_e(
									'A label describing your block\'s custom field.',
									'advanced-custom-blocks'
								);
								?>
							</p>
						</th>
						<td>
							<input
								name="acb-fields-label[]"
								type="text"
								id="acb-fields-edit-label-input_<?php echo esc_attr( $uid ); ?>"
								class="regular-text"
								value="<?php echo esc_attr( $field['label'] ); ?>"
								data-sync="acb-fields-label_<?php echo esc_attr( $uid ); ?>" />
						</td>
					</tr>
					<tr class="acb-fields-edit-name">
						<th scope="row">
							<label for="acb-fields-edit-name-input_<?php echo esc_attr( $uid ); ?>">
								<?php esc_html_e( 'Field Name', 'advanced-custom-blocks' ); ?>
							</label>
							<p class="description" id="acb-fields-edit-name-description">
								<?php esc_html_e( 'Single word, no spaces.', 'advanced-custom-blocks' ); ?>
							</p>
						</th>
						<td>
							<input
								name="acb-fields-name[]"
								type="text"
								id="acb-fields-edit-name-input_<?php echo esc_attr( $uid ); ?>"
								class="regular-text"
								value="<?php echo esc_attr( $field['name'] ); ?>"
								data-sync="acb-fields-name_<?php echo esc_attr( $uid ); ?>" />
						</td>
					</tr>
					<tr class="acb-fields-edit-type">
						<th scope="row">
							<label for="acb-fields-edit-type-input_<?php echo esc_attr( $uid ); ?>">
								<?php esc_html_e( 'Field Type', 'advanced-custom-blocks' ); ?>
							</label>
						</th>
						<td>
							<select
								name="acb-fields-type[]"
								id="acb-fields-edit-type-input_<?php echo esc_attr( $uid ); ?>"
								data-sync="acb-fields-type_<?php echo esc_attr( $uid ); ?>" >

								<option value="text" <?php selected( 'text', $field['type'] ); ?>>
									<?php esc_html_e( 'Text', 'advanced-custom-blocks' ); ?>
								</option>

								<option value="textarea" <?php selected( 'textarea', $field['type'] ); ?>>
									<?php esc_html_e( 'Text Area', 'advanced-custom-blocks' ); ?>
								</option>
							</select>
						</td>
					</tr>
					<tr class="acb-fields-edit-actions">
						<th scope="row">
						</th>
						<td>
							<a class="button acb-fields-edit-actions-close" title="Close Field" href="javascript:">
								Close Field
							</a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Block Template meta box.
	 * @TODO: Change this so that it uses a built-in template fallback method
	 *
	 * @return void
	 */
	public function render_template_meta_box() {
		global $post;

		if ( ! isset( $post->post_name ) || empty( $post->post_name ) ) {
			?>
			<p class="template-notice template-warning">
				<?php esc_html_e( 'The template path will be available after publishing this block.', 'advanced-custom-blocks' ); ?>
			</p>
			<?php
			return;
		}

		if ( ! file_exists( get_theme_file_path( 'blocks/block-' . $post->post_name . '.php' ) ) ) {
			?>
			<div class="template-notice template-warning">
				<p>
					<strong><?php esc_html_e( 'Template not found.', 'advanced-custom-blocks' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'To display this block, ACB will look for one of these templates:', 'advanced-custom-blocks' ); ?>
				</p>
				<?php
				$child_template = str_replace( get_theme_root(), '', get_stylesheet_directory() ) . '/blocks/block-' . $post->post_name . '.php';
				$parent_template = str_replace( get_theme_root(), '', get_template_directory() ) . '/blocks/block-' . $post->post_name . '.php';
				if ( $child_template !== $parent_template ) {
					?>
					<p><code><?php echo esc_html( $child_template ); ?></code></p>
					<?php
				}
				?>
				<p><code><?php echo esc_html( $parent_template ); ?></code></p>
			</div>
			<?php
			return;
		}

		?>
		<div class="template-notice template-success">
			<p>
				<strong><?php esc_html_e( 'Template found.', 'advanced-custom-blocks' ); ?></strong>
			</p>
			<p>
				<?php esc_html_e( 'This block uses the following template:', 'advanced-custom-blocks' ); ?>
			</p>
			<?php
			$child_template = str_replace( get_theme_root(), '', get_stylesheet_directory() ) . '/blocks/block-' . $post->post_name . '.php';
			$parent_template = str_replace( get_theme_root(), '', get_template_directory() ) . '/blocks/block-' . $post->post_name . '.php';
			if ( $child_template !== $parent_template ) {
				?>
				<p><code><?php echo esc_html( $child_template ); ?></code></p>
				<?php
			} else {
				?>
				<p><code><?php echo esc_html( $parent_template ); ?></code></p>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Change the default "Enter Title Here" placeholder on the edit post screen.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function post_title_placeholder( $title ) {
		$screen = get_current_screen();

		// Enqueue scripts and styles on the edit screen of the Block post type.
		if ( is_object( $screen ) && $this->slug === $screen->post_type ) {
			$title = __( 'Enter block name here', 'advanced-custom-blocks' );
		}

		return $title;
	}

	/**
	 * Hide the search box and top pagination.
	 *
	 * @return void
	 */
	public function list_tables_style() {
		$custom_css  = '.post-type-acb_block .tablenav.top { display: none; }';
		$custom_css .= '.post-type-acb_block .search-box { display: none; }';
		wp_add_inline_style( 'list-tables', $custom_css );
	}

	/**
	 * Hide the Quick Edit row action.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function post_row_actions( $actions = array() ) {
		global $post;

		// Abort if the post type is incorrect
		if ( $post->post_type !== $this->slug ) {
			return $actions;
		}

		// Remove the Quick Edit link
		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		// Return the set of links without Quick Edit
		return $actions;
	}
}
