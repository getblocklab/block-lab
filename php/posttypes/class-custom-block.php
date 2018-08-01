<?php
/**
 * Custom Block.
 *
 * @package   AdvancedCustomBlocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace AdvancedCustomBlocks\PostTypes;

/**
 * Class CustomBlock
 */
class Custom_Block {

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
	 * Block description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Block keywords.
	 *
	 * @var string[]
	 */
	public $keywords = array();

	/**
	 * Block fields.
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * CustomBlock constructor.
	 *
	 * @param int|bool $post_id
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
	 * @param string $json
	 * @param string $name
	 *
	 * @return void
	 */
	public function from_json( $json, $name ) {
		$json   = json_decode( $json, true );

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

		if ( isset( $config['description'] ) ) {
			$this->description = $config['description'];
		}

		if ( isset( $config['keywords'] ) ) {
			$this->keywords = $config['keywords'];
		}

		if ( isset( $config['fields'] ) ) {
			foreach( $config['fields'] as $field ) {
				$this->fields[] = new Custom_Field( $field );
			}
		}
	}

	/**
	 * Get the Block as a JSON blob
	 *
	 * @return string
	 */
	public function to_json() {
		return wp_json_encode( array( 'advanced-custom-blocks/' . $this->name => $this ) );
	}
}