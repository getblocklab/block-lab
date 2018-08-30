<?php
/**
 * Block.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Advanced_Custom_Blocks\Blocks;

/**
 * Class Block
 */
class Block {

	/**
	 * Block name (slug).
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Block title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Category name.
	 *
	 * @var string
	 */
	public $category = '';

	/**
	 * Icon.
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Block keywords.
	 *
	 * @var string[]
	 */
	public $keywords = array();

	/**
	 * Block description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Block fields.
	 *
	 * @var Field[]
	 */
	public $fields = array();

	/**
	 * Block constructor.
	 *
	 * @param int|bool $post_id Post ID.
	 *
	 * @return void
	 */
	public function __construct( $post_id = false ) {
		if ( ! $post_id ) {
			return;
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post ) {
			return;
		}
		$this->from_json( $post->post_content, $post->post_name );
	}

	/**
	 * Construct the Block from a JSON blob
	 *
	 * @param string $json JSON blob.
	 * @param string $name Block name.
	 *
	 * @return void
	 */
	public function from_json( $json, $name ) {
		$json = json_decode( $json, true );

		if ( ! isset( $json[ 'advanced-custom-blocks/' . $name ] ) ) {
			return;
		}

		$config = $json[ 'advanced-custom-blocks/' . $name ];

		if ( isset( $config['name'] ) ) {
			$this->name = $config['name'];
		}

		if ( isset( $config['title'] ) ) {
			$this->name = $config['title'];
		}

		if ( isset( $config['category'] ) ) {
			$this->category = $config['category'];
		}

		if ( isset( $config['icon'] ) ) {
			$this->icon = $config['icon'];
		}

		if ( isset( $config['keywords'] ) ) {
			$this->keywords = $config['keywords'];
		}

		if ( isset( $config['description'] ) ) {
			$this->description = $config['description'];
		}

		if ( isset( $config['fields'] ) ) {
			foreach ( $config['fields'] as $field ) {
				$field_defaults = array( 'name', 'label', 'control', 'location', 'order' );
				$field_settings = array_diff( array_keys( $field ), $field_defaults );
				foreach ( $field_settings as $setting ) {
					$field['settings'][ $setting ] = $field[ $setting ];
				}
				$this->fields[] = new Field( $field );
			}
		}
	}

	/**
	 * Get the Block as a JSON blob
	 *
	 * @return string
	 */
	public function to_json() {
		$config['name']        = $this->name;
		$config['title']       = $this->title;
		$config['category']    = $this->category;
		$config['icon']        = $this->icon;
		$config['keywords']    = $this->keywords;
		$config['description'] = $this->description;

		foreach ( $this->fields as $key => $field ) {
			$config['fields'][ $key ]['name']     = $field->name;
			$config['fields'][ $key ]['label']    = $field->label;
			$config['fields'][ $key ]['control']  = $field->control;
			$config['fields'][ $key ]['location'] = $field->location;
			$config['fields'][ $key ]['order']    = $field->order;

			foreach ( $field->settings as $setting => $value ) {
				$config['fields'][ $key ][ $setting ] = $value;
			}
		}

		return wp_json_encode( array( 'advanced-custom-blocks/' . $this->name => $config ) );
	}
}
