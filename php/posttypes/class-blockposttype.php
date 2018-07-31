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
					array( 'jquery', 'wp-util' ),
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
			'default'
		);

		add_meta_box(
			'acb_block_fields',
			__( 'Block Fields', 'advanced-custom-blocks' ),
			array( $this, 'render_fields_meta_box' ),
			$this->slug,
			'normal',
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
					<label for="block-category">
						<?php esc_html_e( 'Category', 'advanced-custom-blocks' ); ?>
					</label>
				</th>
				<td>
					<select name="block-category" id="block-category">
						<option value="__custom">
							<?php esc_html_e( '+ New Category', 'advanced-custom-blocks' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="block-description">
						<?php esc_html_e( 'Description', 'advanced-custom-blocks' ); ?>
					</label>
				</th>
				<td>
					<textarea name="block-description" id="block-description" class="large-text" rows="3"></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="block-keywords">
						<?php esc_html_e( 'Keywords', 'advanced-custom-blocks' ); ?>
					</label>
				</th>
				<td>
					<input name="block-keywords" type="text" id="block-keywords" value="" class="regular-text">
					<p class="description" id="block-keywords-description">
						<?php esc_html_e( 'A comma separated list of keywords, used when searching.', 'advanced-custom-blocks' ); ?>
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
		<div class="acb-fields-rows">
			<table class="widefat">
				<thead>
					<tr>
						<th class="acb-fields-label"><?php esc_html_e( 'Field Label', 'advanced-custom-blocks' ); ?></th>
						<th class="acb-fields-name"><?php esc_html_e( 'Field Name', 'advanced-custom-blocks' ); ?></th>
						<th class="acb-fields-type"><?php esc_html_e( 'Field Type', 'advanced-custom-blocks' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach( $fields as $index => $field ) {
						$this->render_fields_meta_box_row( $field );
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="acb-fields-actions">
			<input name="add-field" type="button" class="button button-primary button-large" id="acb-add-field" value="<?php esc_attr_e( '+ Add Field', 'advanced-custom-blocks' ); ?>">

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
	 * @param int $index
	 *
	 * @return void
	 */
	public function render_fields_meta_box_row( $field ) {
		?>
		<tr class="acb-fields-row">
			<td class="acb-fields-label">
				<a class="row-title" href="javascript:;"><?php echo esc_html( $field['label'] ); ?></a>
				<div class="acb-fields-options">
					<a class="acb-fields-edit" href="javascript:;"><?php esc_html_e( 'Edit', 'advanced-custom-blocks'); ?></a>&nbsp;|&nbsp;
					<a class="acb-fields-delete" href="javascript:;"><?php esc_html_e( 'Delete', 'advanced-custom-blocks'); ?></a>
				</div>
			</td>
			<td class="acb-fields-name">
				<?php echo esc_html( $field['name'] ); ?>
			</td>
			<td class="acb-fields-type">
				<?php echo esc_html( $field['type'] ); ?>
			</td>
		</tr>
		<tr class="acb-fields-new acb-fields-new-label">
			<th scope="row">
				<label for="block-field-label">
					<?php esc_html_e( 'Field Label', 'advanced-custom-blocks' ); ?>
				</label>
				<p class="description" id="block-field-label-description">
					<?php esc_html_e( 'A label describing your block\'s custom field.', 'advanced-custom-blocks' ); ?>
				</p>
			</th>
			<td colspan="2">
				<input name="block-field-label[]" type="text" id="block-field-label" value="<?php echo esc_attr( $field['label'] ); ?>" class="regular-text">
			</td>
		</tr>
		<tr class="acb-fields-new acb-fields-new-name">
			<th scope="row">
				<label for="block-field-name">
					<?php esc_html_e( 'Field Name', 'advanced-custom-blocks' ); ?>
				</label>
				<p class="description" id="block-field-name-description">
					<?php esc_html_e( 'Single word, no spaces.', 'advanced-custom-blocks' ); ?>
				</p>
			</th>
			<td colspan="2">
				<input name="block-field-name[]" type="text" id="block-field-name" value="<?php echo esc_attr( $field['name'] ); ?>" class="regular-text">
			</td>
		</tr>
		<tr class="acb-fields-new acb-fields-new-type">
			<th scope="row">
				<label for="block-field-type">
					<?php esc_html_e( 'Field Type', 'advanced-custom-blocks' ); ?>
				</label>
			</th>
			<td colspan="2">
				<select name="block-field-type[]" id="block-field-type">
					<option value="text" <?php selected( 'text', $field['type'] ); ?>>
						<?php esc_html_e( 'Text', 'advanced-custom-blocks' ); ?>
					</option>
					<option value="textarea" <?php selected( 'text', $field['type'] ); ?>>
						<?php esc_html_e( 'Text Area', 'advanced-custom-blocks' ); ?>
					</option>
				</select>
			</td>
		</tr>
		<tr class="acb-fields-new acb-fields-new-close">
			<th scope="row">
			</th>
			<td colspan="2">
				<a class="button" title="Close Field" href="javascript:;">Close Field</a>
			</td>
		</tr>
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
}
