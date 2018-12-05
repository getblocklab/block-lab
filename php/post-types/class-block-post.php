<?php
/**
 * Block Post Type.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Post_Types;

use Block_Lab\Component_Abstract;
use Block_Lab\Blocks\Block;
use Block_Lab\Blocks\Field;
use Block_Lab\Blocks\Controls;

/**
 * Class Block
 */
class Block_Post extends Component_Abstract {

	/**
	 * Slug used for the custom post type.
	 *
	 * @var string
	 */
	public $slug = 'block_lab';

	/**
	 * Registered controls.
	 *
	 * @var Controls\Control_Abstract[]
	 */
	public $controls = array();

	/**
	 * Register any hooks that this component needs.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_init', array( $this, 'add_caps' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ) );
		add_action( 'post_submitbox_start', array( $this, 'save_draft_button' ) );
		add_filter( 'enter_title_here', array( $this, 'post_title_placeholder' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_insert_post_data', array( $this, 'save_block' ), 10, 2 );

		// Clean up the list table.
		add_filter( 'disable_months_dropdown', '__return_true', 10, $this->slug );
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ) );
		add_filter( 'bulk_actions-edit-' . $this->slug, array( $this, 'bulk_actions' ) );
		add_filter( 'manage_edit-' . $this->slug . '_columns', array( $this, 'list_table_columns' ) );
		add_action( 'manage_' . $this->slug . '_posts_custom_column', array( $this, 'list_table_content' ), 10, 2 );

		// AJAX Handlers.
		add_action( 'wp_ajax_fetch_field_settings', array( $this, 'ajax_field_settings' ) );
	}

	/**
	 * Initialise Block posts.
	 *
	 * @return void
	 */
	public function init() {
		$this->register_controls();
	}

	/**
	 * Register the controls.
	 *
	 * @return void
	 */
	public function register_controls() {
		$this->controls = apply_filters(
			'block_lab_controls', array(
				'text'     => new Controls\Text(),
				'textarea' => new Controls\Textarea(),
				'select'   => new Controls\Select(),
				'toggle'   => new Controls\Toggle(),
				'range'    => new Controls\Range(),
				'checkbox' => new Controls\Checkbox(),
				'radio'    => new Controls\Radio(),
			)
		);
	}

	/**
	 * Register the custom post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Content Blocks', 'post type general name', 'block-lab' ),
			'singular_name'      => _x( 'Content Block', 'post type singular name', 'block-lab' ),
			'menu_name'          => _x( 'Block Lab', 'admin menu', 'block-lab' ),
			'name_admin_bar'     => _x( 'Block', 'add new on admin bar', 'block-lab' ),
			'add_new'            => _x( 'Add New', 'block', 'block-lab' ),
			'add_new_item'       => __( 'Add New Block', 'block-lab' ),
			'new_item'           => __( 'New Block', 'block-lab' ),
			'edit_item'          => __( 'Edit Block', 'block-lab' ),
			'view_item'          => __( 'View Block', 'block-lab' ),
			'all_items'          => __( 'All Blocks', 'block-lab' ),
			'search_items'       => __( 'Search Blocks', 'block-lab' ),
			'parent_item_colon'  => __( 'Parent Blocks:', 'block-lab' ),
			'not_found'          => __( 'No blocks found.', 'block-lab' ),
			'not_found_in_trash' => __( 'No blocks found in Trash.', 'block-lab' ),
		);

		$args = array(
			'labels'        => $labels,
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'menu_position' => 100,
			// This SVG icon is being included from the plugin directory, so using file_get_contents is okay.
			// @codingStandardsIgnoreStart
			'menu_icon'     => 'data:image/svg+xml;base64,' . base64_encode(
				file_get_contents(
					$this->plugin->get_assets_path( 'images/admin-menu-icon.svg' )
				)
			),
			// @codingStandardsIgnoreEnd
			'query_var'     => true,
			'rewrite'       => array( 'slug' => $this->slug ),
			'hierarchical'  => true,
			'capabilities'  => array(
				'edit_post'          => 'block_lab_edit_block',
				'edit_posts'         => 'block_lab_edit_blocks',
				'edit_others_posts'  => 'block_lab_edit_others_blocks',
				'publish_posts'      => 'block_lab_publish_blocks',
				'read_post'          => 'block_lab_read_block',
				'read_private_posts' => 'block_lab_read_private_blocks',
				'delete_post'        => 'block_lab_delete_block',
			),
			'map_meta_cap'  => true,
			'supports'      => array( 'title' ),
		);

		register_post_type( $this->slug, $args );
	}

	/**
	 * Add custom capabilities
	 *
	 * @return void
	 */
	public function add_caps() {
		$admins = get_role( 'administrator' );

		$admins->add_cap( 'block_lab_edit_block' );
		$admins->add_cap( 'block_lab_edit_blocks' );
		$admins->add_cap( 'block_lab_edit_others_blocks' );
		$admins->add_cap( 'block_lab_publish_blocks' );
		$admins->add_cap( 'block_lab_read_block' );
		$admins->add_cap( 'block_lab_read_private_blocks' );
		$admins->add_cap( 'block_lab_delete_block' );
	}

	/**
	 * Enqueue scripts and styles used by the Block post type.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		global $post;

		$screen = get_current_screen();

		if ( ! is_object( $screen ) ) {
			return;
		}

		wp_register_style(
			'select2',
			$this->plugin->get_url( 'css/libs/select2.min.css' ),
			array(),
			'4.0.6-rc.1'
		);
		wp_register_script(
			'select2',
			$this->plugin->get_url( 'js/libs/select2.min.js' ),
			array(),
			'4.0.6-rc.1'
		);

		// Enqueue scripts and styles on the edit screen of the Block post type.
		if ( $this->slug === $screen->post_type && 'post' === $screen->base ) {
			wp_enqueue_style(
				'block-post',
				$this->plugin->get_url( 'css/admin.block-post.css' ),
				array( 'select2' ),
				$this->plugin->get_version()
			);
			if ( ! in_array( $post->post_status, array( 'publish', 'future', 'pending' ), true ) ) {
				wp_add_inline_style( 'block-post', '#delete-action { display: none; }' );
			}
			wp_enqueue_script(
				'block-post',
				$this->plugin->get_url( 'js/admin.block-post.js' ),
				array( 'jquery', 'jquery-ui-sortable', 'wp-util', 'wp-blocks', 'select2' ),
				$this->plugin->get_version(),
				false
			);
			wp_localize_script(
				'block-post',
				'blockLab',
				array(
					'fieldSettingsNonce' => wp_create_nonce( 'block_lab_field_settings_nonce' ),
				)
			);
		}
		if ( $this->slug === $screen->post_type && 'edit' === $screen->base ) {
			wp_enqueue_style(
				'block-edit',
				$this->plugin->get_url( 'css/admin.block-edit.css' ),
				array(),
				$this->plugin->get_version()
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
			'block_properties',
			__( 'Block Properties', 'block-lab' ),
			array( $this, 'render_properties_meta_box' ),
			$this->slug,
			'normal',
			'high'
		);

		add_meta_box(
			'block_fields',
			__( 'Block Fields', 'block-lab' ),
			array( $this, 'render_fields_meta_box' ),
			$this->slug,
			'normal',
			'high'
		);

		add_meta_box(
			'block_template',
			__( 'Template', 'block-lab' ),
			array( $this, 'render_template_meta_box' ),
			$this->slug,
			'side',
			'default'
		);
	}

	/**
	 * Removes unneeded meta boxes.
	 *
	 * @return void
	 */
	public function remove_meta_boxes() {
		$screen = get_current_screen();

		if ( ! is_object( $screen ) || $this->slug !== $screen->post_type ) {
			return;
		}

		remove_meta_box( 'slugdiv', $this->slug, 'normal' );
	}

	/**
	 * Adds a "Save Draft" button next to the "Publish" button
	 *
	 * @return void
	 */
	public function save_draft_button() {
		global $post;

		$screen = get_current_screen();

		if ( ! is_object( $screen ) || $this->slug !== $screen->post_type ) {
			return;
		}

		if ( ! in_array( $post->post_status, array( 'publish', 'future', 'pending' ), true ) ) {
			?>
			<input type="submit" name="save" value="<?php esc_attr_e( 'Save Draft', 'block-lab' ); ?>" class="button" />
			<?php
		}
	}

	/**
	 * Render the Block Fields meta box.
	 *
	 * @return void
	 */
	public function render_properties_meta_box() {
		global $post;
		$block = new Block( $post->ID );
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="block-properties-slug">
						<?php esc_html_e( 'Slug', 'block-lab' ); ?>
					</label>
					<p class="description" id="block-properties-slug-description">
						<?php
						esc_html_e(
							'Used to determine the location of the template file. Lowercase letters, numbers, and hyphens.',
							'block-lab'
						);
						?>
					</p>
				</th>
				<td>
					<p>
						<input
							name="post_name"
							type="text"
							id="block-properties-slug"
							value="<?php echo esc_attr( $post->post_name ); ?>"
							class="regular-text">
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="block-properties-icon">
						<?php esc_html_e( 'Icon', 'block-lab' ); ?>
					</label>
				</th>
				<td>
					<input
							name="block-properties-icon"
							type="hidden"
							id="block-properties-icon"
							value="<?php echo esc_attr( $block->icon ); ?>">
					<div class="block-properties-icons">
						<?php
						foreach ( block_lab_get_icons() as $icon => $svg ) {
							$selected = $icon === $block->icon ? 'selected' : '';
							printf(
								'<span class="icon %1$s" data-value="%2$s">%3$s</span>',
								esc_attr( $selected ),
								esc_attr( $icon ),
								wp_kses( $svg, block_lab_allowed_svg_tags() )
							);
						}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="block-properties-category">
						<?php esc_html_e( 'Category', 'block-lab' ); ?>
					</label>
				</th>
				<td>
					<p>
						<select name="block-properties-category" id="block-properties-category">
						</select>
						<input type="hidden" id="block-properties-category-saved" value="<?php echo esc_attr( $block->category ); ?>" />
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="block-properties-keywords">
						<?php esc_html_e( 'Keywords', 'block-lab' ); ?>
					</label>
					<p class="description" id="block-properties-keywords-description">
						<?php
						esc_html_e(
							'A comma separated list of keywords, used when searching. Maximum of 3 keywords.',
							'block-lab'
						);
						?>
					</p>
				</th>
				<td>
					<p>
						<input
							name="block-properties-keywords"
							type="text"
							id="block-properties-keywords"
							value="<?php echo esc_attr( implode( ', ', $block->keywords ) ); ?>"
							class="regular-text">
					</p>
				</td>
			</tr>
		</table>
		<?php
		wp_nonce_field( 'block_lab_save_properties', 'block_lab_properties_nonce' );
	}

	/**
	 * Render the Block Fields meta box.
	 *
	 * @return void
	 */
	public function render_fields_meta_box() {
		global $post;
		$block = new Block( $post->ID );
		?>
		<div class="block-fields-list">
			<table class="widefat">
				<thead>
					<tr>
						<th class="block-fields-sort"></th>
						<th class="block-fields-label">
							<?php esc_html_e( 'Field Label', 'block-lab' ); ?>
						</th>
						<th class="block-fields-name">
							<?php esc_html_e( 'Field Name', 'block-lab' ); ?>
						</th>
						<th class="block-fields-control">
							<?php esc_html_e( 'Field Type', 'block-lab' ); ?>
						</th>
						<th class="block-fields-location">
							<?php esc_html_e( 'Field Location', 'block-lab' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="5" class="block-fields-rows">
							<p class="block-no-fields">
								<?php
								echo wp_kses_post(
									sprintf(
										// Translators: Placeholders are for <strong> HTML tags.
										__( 'Click the %1$s+ Add Field%2$s button below to add your first field.' ),
										'<strong>',
										'</strong>'
									)
								);
								?>
							</p>
							<?php
							if ( count( $block->fields ) > 0 ) {
								foreach ( $block->fields as $field ) {
									$this->render_fields_meta_box_row( $field, uniqid() );
								}
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="block-fields-actions-add-field">
			<input
				name="add-field"
				type="button"
				class="button button-primary button-large"
				id="block-add-field"
				value="<?php esc_attr_e( '+ Add Field', 'block-lab' ); ?>" />

			<script type="text/html" id="tmpl-field-repeater">
				<?php
				$args = array(
					'name'  => 'new-field',
					'label' => __( 'New Field', 'block-lab' ),
				);
				$this->render_fields_meta_box_row( new Field( $args ) );
				?>
			</script>
		</div>
		<?php
		wp_nonce_field( 'block_lab_save_fields', 'block_lab_fields_nonce' );
	}

	/**
	 * Render a single Field as a row.
	 *
	 * @param Field $field The Field containing the options to render.
	 * @param mixed $uid   A unique ID to used to unify the HTML name, for, and id attributes.
	 *
	 * @return void
	 */
	public function render_fields_meta_box_row( $field, $uid = false ) {
		// Use a template placeholder if no UID provided.
		if ( ! $uid ) {
			$uid = '{{ data.uid }}';
		}
		?>
		<div class="block-fields-row" data-uid="<?php echo esc_attr( $uid ); ?>">
			<div class="block-fields-sort">
				<span class="block-fields-sort-handle"></span>
			</div>
			<div class="block-fields-label">
				<a class="row-title" href="javascript:" id="block-fields-label_<?php echo esc_attr( $uid ); ?>">
					<?php echo esc_html( $field->label ); ?>
				</a>
				<div class="block-fields-actions">
					<a class="block-fields-actions-edit" href="javascript:">
						<?php esc_html_e( 'Edit', 'block-lab' ); ?>
					</a>
					&nbsp;|&nbsp;
					<a class="block-fields-actions-delete" href="javascript:">
						<?php esc_html_e( 'Delete', 'block-lab' ); ?>
					</a>
				</div>
			</div>
			<div class="block-fields-name" id="block-fields-name_<?php echo esc_attr( $uid ); ?>">
				<code id="block-fields-name-code_<?php echo esc_attr( $uid ); ?>"><?php echo esc_html( $field->name ); ?></code>
			</div>
			<div class="block-fields-control" id="block-fields-control_<?php echo esc_attr( $uid ); ?>">
				<?php echo esc_html( $this->controls[ $field->control ]->label ); ?>
			</div>
			<div class="block-fields-location" id="block-fields-location_<?php echo esc_attr( $uid ); ?>">
				<?php
				if ( 'editor' === $field->location ) {
					esc_html_e( 'Editor', 'block-lab' );
				} elseif ( 'inspector' === $field->location ) {
					esc_html_e( 'Inspector', 'block-lab' );
				}
				?>
			</div>
			<div class="block-fields-edit">
				<table class="widefat">
					<tr class="block-fields-edit-label">
						<td class="spacer"></td>
						<th scope="row">
							<label for="block-fields-edit-label-input_<?php echo esc_attr( $uid ); ?>">
								<?php esc_html_e( 'Field Label', 'block-lab' ); ?>
							</label>
							<p class="description" id="block-fields-edit-label-description">
								<?php
								esc_html_e(
									'A label describing your block\'s custom field.',
									'block-lab'
								);
								?>
							</p>
						</th>
						<td>
							<input
								name="block-fields-label[<?php echo esc_attr( $uid ); ?>]"
								type="text"
								id="block-fields-edit-label-input_<?php echo esc_attr( $uid ); ?>"
								class="regular-text"
								value="<?php echo esc_attr( $field->label ); ?>"
								data-sync="block-fields-label_<?php echo esc_attr( $uid ); ?>" />
						</td>
					</tr>
					<tr class="block-fields-edit-name">
						<td class="spacer"></td>
						<th scope="row">
							<label for="block-fields-edit-name-input_<?php echo esc_attr( $uid ); ?>">
								<?php esc_html_e( 'Field Name', 'block-lab' ); ?>
							</label>
							<p class="description" id="block-fields-edit-name-description">
								<?php esc_html_e( 'Single word, no spaces.', 'block-lab' ); ?>
							</p>
						</th>
						<td>
							<input
								name="block-fields-name[<?php echo esc_attr( $uid ); ?>]"
								type="text"
								id="block-fields-edit-name-input_<?php echo esc_attr( $uid ); ?>"
								class="regular-text"
								value="<?php echo esc_attr( $field->name ); ?>"
								data-sync="block-fields-name-code_<?php echo esc_attr( $uid ); ?>" />
						</td>
					</tr>
					<tr class="block-fields-edit-control">
						<td class="spacer"></td>
						<th scope="row">
							<label for="block-fields-edit-control-input_<?php echo esc_attr( $uid ); ?>">
								<?php esc_html_e( 'Field Type', 'block-lab' ); ?>
							</label>
						</th>
						<td>
							<select
								name="block-fields-control[<?php echo esc_attr( $uid ); ?>]"
								id="block-fields-edit-control-input_<?php echo esc_attr( $uid ); ?>"
								data-sync="block-fields-control_<?php echo esc_attr( $uid ); ?>" >
								<?php foreach ( $this->controls as $control ) : ?>
									<option
										value="<?php echo esc_attr( $control->name ); ?>"
										<?php selected( $field->control, $control->name ); ?>>
										<?php echo esc_html( $control->label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr class="block-fields-edit-location">
						<td class="spacer"></td>
						<th scope="row">
							<label for="block-fields-edit-location-input_<?php echo esc_attr( $uid ); ?>">
								<?php esc_html_e( 'Field Location', 'block-lab' ); ?>
							</label>
						</th>
						<td>
							<select
								name="block-fields-location[<?php echo esc_attr( $uid ); ?>]"
								id="block-fields-edit-location-input_<?php echo esc_attr( $uid ); ?>"
								data-sync="block-fields-location_<?php echo esc_attr( $uid ); ?>" >
									<option
										value="editor"
										<?php selected( $field->location, 'editor' ); ?>>
										<?php esc_html_e( 'Editor', 'block-lab' ); ?>
									</option>
									<option
										value="inspector"
										<?php selected( $field->location, 'inspector' ); ?>>
										<?php esc_html_e( 'Inspector', 'block-lab' ); ?>
									</option>
							</select>
						</td>
					</tr>
					<?php $this->render_field_settings( $field, $uid ); ?>
					<tr class="block-fields-edit-actions-close">
						<td class="spacer"></td>
						<th scope="row">
						</th>
						<td>
							<a class="button" title="<?php esc_attr_e( 'Close Field', 'block-lab' ); ?>" href="javascript:">
								<?php esc_html_e( 'Close Field', 'block-lab' ); ?>
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
	 *
	 * @return void
	 */
	public function render_template_meta_box() {
		global $post;

		if ( ! isset( $post->post_name ) || empty( $post->post_name ) ) {
			?>
			<div class="template-notice template-warning">
				<p>
					<?php esc_html_e( 'The template path will be available after publishing this block.', 'block-lab' ); ?>
				</p>
			</div>
			<?php
			return;
		}

		$template = block_lab_locate_template( 'blocks/block-' . $post->post_name . '.php', '', true );

		if ( ! $template ) {
			?>
			<div class="template-notice template-warning">
				<p>
					<strong><?php esc_html_e( 'Template not found.', 'block-lab' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'To display this block, Block Lab will look for one of these templates:', 'block-lab' ); ?>
				</p>
				<?php
				// Formatting to make the template paths easier to understand.
				$child_template        = get_stylesheet_directory() . '/blocks/block-' . $post->post_name . '.php';
				$child_template_short  = str_replace( WP_CONTENT_DIR, '', $child_template );
				$child_template_parts  = explode( '/', $child_template_short );
				$child_template_breaks = implode( '/<wbr>', $child_template_parts );

				$parent_template        = get_template_directory() . '/blocks/block-' . $post->post_name . '.php';
				$parent_template_short  = str_replace( WP_CONTENT_DIR, '', $parent_template );
				$parent_template_parts  = explode( '/', $parent_template_short );
				$parent_template_breaks = implode( '/<wbr>', $parent_template_parts );

				if ( $child_template !== $parent_template ) {
					?>
					<p><code><?php echo wp_kses( $child_template_breaks, array( 'wbr' => array() ) ); ?></code></p>
					<?php
				}
				?>
				<p><code><?php echo wp_kses( $parent_template_breaks, array( 'wbr' => array() ) ); ?></code></p>
			</div>
			<?php
			return;
		}

		// Formatting to make the template path easier to understand.
		$template_short       = str_replace( WP_CONTENT_DIR, '', $template );
		$template_parts       = explode( '/', $template_short );
		$template_with_breaks = implode( '/<wbr>', $template_parts );
		?>
		<div class="template-notice template-success">
			<p>
				<strong><?php esc_html_e( 'Template found.', 'block-lab' ); ?></strong>
			</p>
			<p>
				<?php esc_html_e( 'This block uses the following template:', 'block-lab' ); ?>
			</p>
			<p><code><?php echo wp_kses( $template_with_breaks, array( 'wbr' => array() ) ); ?></code></p>
		</div>
		<?php
	}

	/**
	 * Render the Block Template meta box.
	 *
	 * @param Field  $field The Field containing the options to render.
	 * @param string $uid   A unique ID to used to unify the HTML name, for, and id attributes.
	 *
	 * @return void
	 */
	public function render_field_settings( $field, $uid ) {
		if ( isset( $this->controls[ $field->control ] ) ) {
			$this->controls[ $field->control ]->render_settings( $field, $uid );
		}
	}

	/**
	 * Ajax response for fetching field settings.
	 *
	 * @return void
	 */
	public function ajax_field_settings() {
		wp_verify_nonce( 'block_lab_field_options_nonce' );

		if ( ! isset( $_POST['control'] ) || ! isset( $_POST['uid'] ) ) {
			wp_send_json_error();
			return;
		}

		$control = sanitize_key( $_POST['control'] );
		$uid     = sanitize_key( $_POST['uid'] );

		ob_start();
		$field = new Field( array( 'control' => $control ) );
		$this->render_field_settings( $field, $uid );
		$data['html'] = ob_get_clean();

		if ( '' === $data['html'] ) {
			wp_send_json_error();
		}

		wp_send_json_success( $data );
	}

	/**
	 * Save block meta boxes as a json blob in post content.
	 *
	 * @param array $data An array of slashed post data.
	 *
	 * @return array
	 */
	public function save_block( $data ) {
		if ( ! isset( $_POST['post_ID'] ) ) {
			return $data;
		}

		$post_id = sanitize_key( $_POST['post_ID'] );

		// Exits script depending on save status.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return $data;
		}

		// Exits script if not the right post type.
		if ( $data['post_type'] !== $this->slug ) {
			return $data;
		}

		check_admin_referer( 'block_lab_save_fields', 'block_lab_fields_nonce' );
		check_admin_referer( 'block_lab_save_properties', 'block_lab_properties_nonce' );

		// Strip encoded special characters, like 🖖 (%f0%9f%96%96).
		$data['post_name'] = preg_replace( '/%[a-f|0-9][a-f|0-9]/', '', $data['post_name'] );

		// sanitize_title() allows underscores, but register_block_type doesn't.
		$data['post_name'] = str_replace( '_', '-', $data['post_name'] );

		// If only special characters were used, it's possible the post_name is now empty.
		if ( '' === $data['post_name'] ) {
			$data['post_name'] = $post_id;
		}

		// register_block_type doesn't allow slugs starting with a number.
		if ( is_numeric( $data['post_name'][0] ) ) {
			$data['post_name'] = 'block-' . $data['post_name'];
		}

		// Make sure the block slug is still unique.
		$data['post_name'] = wp_unique_post_slug(
			$data['post_name'],
			$post_id,
			$data['post_status'],
			$data['post_type'],
			$data['post_parent']
		);

		$block = new Block();

		// Block name.
		$block->name = sanitize_key( $data['post_name'] );
		if ( '' === $block->name ) {
			$block->name = $post_id;
		}

		// Block title.
		$block->title = sanitize_text_field(
			wp_unslash( $data['post_title'] )
		);
		if ( '' === $block->title ) {
			$block->title = $post_id;
		}

		// Block icon.
		if ( isset( $_POST['block-properties-icon'] ) ) {
			$block->icon = sanitize_key( $_POST['block-properties-icon'] );
		}

		// Block category.
		if ( isset( $_POST['block-properties-category'] ) ) {
			$block->category = sanitize_key( $_POST['block-properties-category'] );
		}

		// Block keywords.
		if ( isset( $_POST['block-properties-keywords'] ) ) {
			$keywords = sanitize_text_field(
				wp_unslash( $_POST['block-properties-keywords'] )
			);
			$keywords = explode( ',', $keywords );
			$keywords = array_map( 'trim', $keywords );
			$keywords = array_slice( $keywords, 0, 3 );

			$block->keywords = $keywords;
		}

		// Block fields.
		if ( isset( $_POST['block-fields-name'] ) && is_array( $_POST['block-fields-name'] ) ) {
			$order = 0;

			// We loop through this array and sanitize its content according to the content type.
			$fields = wp_unslash( $_POST['block-fields-name'] ); // Sanitization okay.
			foreach ( $fields as $key => $name ) {
				// Field name and order.
				$field_config = array(
					'name'  => sanitize_key( $name ),
					'order' => $order,
				);

				// Field label.
				if ( isset( $_POST['block-fields-label'][ $key ] ) ) {
					$field_config['label'] = sanitize_text_field(
						wp_unslash( $_POST['block-fields-label'][ $key ] )
					);
				}

				// Field control.
				if ( isset( $_POST['block-fields-control'][ $key ] ) ) {
					$field_config['control'] = sanitize_text_field(
						wp_unslash( $_POST['block-fields-control'][ $key ] )
					);
				}

				// Field type.
				if ( isset( $field_config['control'] ) && isset( $this->controls[ $field_config['control'] ] ) ) {
					$field_config['type'] = $this->controls[ $field_config['control'] ]->type;
				}

				// Field location.
				if ( isset( $_POST['block-fields-location'][ $key ] ) ) {
					$field_config['location'] = sanitize_text_field(
						wp_unslash( $_POST['block-fields-location'][ $key ] )
					);
				}

				// Field settings.
				if ( isset( $field_config['control'] ) && isset( $this->controls[ $field_config['control'] ] ) ) {
					$control = $this->controls[ $field_config['control'] ];
					foreach ( $control->settings as $setting ) {
						if ( isset( $_POST['block-fields-settings'][ $key ][ $setting->name ] ) ) {
							// Sanitize the field options according to their type.
							if ( is_callable( $setting->sanitize ) ) {
								$field_config['settings'][ $setting->name ] = call_user_func(
									$setting->sanitize,
									$_POST['block-fields-settings'][ $key ][ $setting->name ] // Sanitization okay.
								);
							}
						}
					}
				}

				$field = new Field( $field_config );

				$block->fields[ $name ] = $field;
				$order++;
			}
		}

		$data['post_content'] = wp_slash( $block->to_json() );
		return $data;
	}

	/**
	 * Change the default "Enter Title Here" placeholder on the edit post screen.
	 *
	 * @param string $title Placeholder text. Default 'Enter title here'.
	 *
	 * @return string
	 */
	public function post_title_placeholder( $title ) {
		$screen = get_current_screen();

		// Enqueue scripts and styles on the edit screen of the Block post type.
		if ( is_object( $screen ) && $this->slug === $screen->post_type ) {
			$title = __( 'Enter block name here', 'block-lab' );
		}

		return $title;
	}

	/**
	 * Change the columns in the Custom Blocks list table
	 *
	 * @param array $columns An array of column name ⇒ label. The name is passed to functions to identify the column.
	 *
	 * @return array
	 */
	public function list_table_columns( $columns ) {
		$new_columns = array(
			'cb'       => $columns['cb'],
			'title'    => $columns['title'],
			'icon'     => __( 'Icon', 'block-lab' ),
			'template' => __( 'Template', 'block-lab' ),
			'keywords' => __( 'Keywords', 'block-lab' ),
		);
		return $new_columns;
	}

	/**
	 * Output custom column data into the table
	 *
	 * @param string $column  The name of the column to display.
	 * @param int    $post_id The ID of the current post.
	 *
	 * @return void
	 */
	public function list_table_content( $column, $post_id ) {
		if ( 'icon' === $column ) {
			$block = new Block( $post_id );
			$icons = block_lab_get_icons();

			if ( isset( $icons[ $block->icon ] ) ) {
				printf(
					'<span class="icon %1$s">%2$s</span>',
					esc_attr( $block->icon ),
					wp_kses( $icons[ $block->icon ], block_lab_allowed_svg_tags() )
				);
			}
		}
		if ( 'template' === $column ) {
			$block    = new Block( $post_id );
			$template = block_lab_locate_template( 'blocks/block-' . $block->name . '.php', '', true );

			if ( ! $template ) {
				esc_html_e( 'No template found.', 'block-lab' );
			} else {
				// Formatting to make the template path easier to understand.
				$template_short  = str_replace( WP_CONTENT_DIR, '', $template );
				$template_parts  = explode( '/', $template_short );
				$template_breaks = implode( '/<wbr>', $template_parts );
				echo wp_kses(
					'<code>' . $template_breaks . '</code>',
					array(
						'code' => array(),
						'wbr'  => array(),
					)
				);
			}
		}
		if ( 'keywords' === $column ) {
			$block = new Block( $post_id );
			echo esc_html( implode( ', ', $block->keywords ) );
		}
		if ( 'fields' === $column ) {
			$block = new Block( $post_id );
			echo esc_html( count( $block->fields ) );
		}
	}

	/**
	 * Hide the Quick Edit row action.
	 *
	 * @param array $actions An array of row action links.
	 *
	 * @return array
	 */
	public function post_row_actions( $actions = array() ) {
		global $post;

		// Abort if the post type is incorrect.
		if ( $post->post_type !== $this->slug ) {
			return $actions;
		}

		// Remove the Quick Edit link.
		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		// Return the set of links without Quick Edit.
		return $actions;
	}

	/**
	 * Remove Edit from the Bulk Actions menu
	 *
	 * @param array $actions An array of bulk actions.
	 *
	 * @return array
	 */
	public function bulk_actions( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}
}
