<?php
/**
 * Block.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks;

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
	 * Icon.
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Category name.
	 *
	 * @var string
	 */
	public $category = '';

	/**
	 * Block keywords.
	 *
	 * @var string[]
	 */
	public $keywords = array();

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

		$this->name = $post->post_name;
		$this->from_json( $post->post_content );
	}

	/**
	 * Construct the Block from a JSON blob
	 *
	 * @param string $json JSON blob.
	 *
	 * @return void
	 */
	public function from_json( $json ) {
		$json = json_decode( $json, true );

		if ( ! isset( $json[ 'block-lab/' . $this->name ] ) ) {
			return;
		}

		$config = $json[ 'block-lab/' . $this->name ];

		if ( isset( $config['title'] ) ) {
			$this->title = $config['title'];
		}

		if ( isset( $config['icon'] ) ) {
			$this->icon = $config['icon'];
		}

		if ( isset( $config['category'] ) ) {
			$this->category = $config['category'];
		}

		if ( isset( $config['keywords'] ) ) {
			$this->keywords = $config['keywords'];
		}

		if ( isset( $config['fields'] ) ) {
			foreach ( $config['fields'] as $field ) {
				$field_defaults = array( 'name', 'label', 'control', 'type', 'location', 'order' );
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
		$config['name']     = $this->name;
		$config['title']    = $this->title;
		$config['icon']     = $this->icon;
		$config['category'] = $this->category;
		$config['keywords'] = $this->keywords;

		$config['fields'] = array();
		foreach ( $this->fields as $key => $field ) {
			$config['fields'][ $key ]['name']     = $field->name;
			$config['fields'][ $key ]['label']    = $field->label;
			$config['fields'][ $key ]['control']  = $field->control;
			$config['fields'][ $key ]['type']     = $field->type;
			$config['fields'][ $key ]['location'] = $field->location;
			$config['fields'][ $key ]['order']    = $field->order;

			foreach ( $field->settings as $setting => $value ) {
				$config['fields'][ $key ][ $setting ] = $value;
			}
		}

		return wp_json_encode( array( 'block-lab/' . $this->name => $config ), JSON_UNESCAPED_UNICODE );
	}
}
