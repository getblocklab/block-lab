<?php
/**
 * Post_Type.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Migration;

use WP_Post;
use WP_Query;

/**
 * Class Post_Type
 */
final class Post_Type {

	/**
	 * The previous slug of the custom post type (in Block Lab).
	 *
	 * @var string
	 */
	private $previous_post_type_slug;

	/**
	 * The new slug of the custom post type (not in Block Lab).
	 *
	 * @var string
	 */
	private $new_post_type_slug;

	/**
	 * The previous namespace of the block.
	 *
	 * @var string
	 */
	private $previous_block_namespace;

	/**
	 * The new namespace of the block.
	 *
	 * @var string
	 */
	private $new_block_namespace;

	/**
	 * The batch size (posts_per_page) for the post queries.
	 *
	 * @var int
	 */
	private $query_batch_size = 10;

	/**
	 * Post_Type constructor.
	 *
	 * @param string $new_post_type_slug  The new slug of the custom post type.
	 * @param string $new_block_namespace The new namespace of the block.
	 */
	public function __construct( $new_post_type_slug, $new_block_namespace ) {
		$this->previous_post_type_slug  = block_lab()->get_post_type_slug();
		$this->new_post_type_slug       = $new_post_type_slug;
		$this->previous_block_namespace = 'block-lab';
		$this->new_block_namespace      = $new_block_namespace;
	}

	/**
	 * Migrates all of the custom post type posts to the new slug and block namespace.
	 *
	 * These each store a config for a custom block,
	 * they aren't actual posts as entered into the block editor.
	 */
	public function migrate_all() {
		$offset = 0;
		$posts  = $this->query_for_posts( $offset );

		while ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$this->migrate_single( $post );
			}

			$offset += $this->query_batch_size;
			$posts   = $this->query_for_posts( $offset );
		}
	}

	/**
	 * Gets the posts at the given offset.
	 *
	 * @param int $offset The offset at which to query the posts.
	 * @return array Posts found starting at that offset.
	 */
	public function query_for_posts( $offset ) {
		$query = new WP_Query(
			[
				'post_type'      => $this->previous_post_type_slug,
				'posts_per_page' => $this->query_batch_size,
				'offset'         => $offset,
			]
		);

		return $query->posts;
	}

	/**
	 * Migrates the custom post type post to the new slug and block namespace.
	 *
	 * Inspired by the work of Weston Ruter: https://github.com/ampproject/amp-wp/blob/4880f0f58daaf07685854be8574ff25d76ff583e/includes/validation/class-amp-validated-url-post-type.php#L165-L170
	 *
	 * @param WP_Post $post The post to convert.
	 * @return bool Whether migrating the post was successful.
	 */
	public function migrate_single( WP_Post $post ) {
		global $wpdb;

		$block = json_decode( $post->post_content, true );
		if ( JSON_ERROR_NONE !== json_last_error() || empty( $block ) ) {
			return false;
		}

		$block_keys     = array_keys( $block );
		$old_block_name = reset( $block_keys );
		if ( empty( $block[ $old_block_name ] ) ) {
			return false;
		}

		$block_contents = $block[ $old_block_name ];
		$new_block_name = preg_replace( '#^' . $this->previous_block_namespace . '(?=/)#', $this->new_block_namespace, $old_block_name );
		$new_block      = [ $new_block_name => $block_contents ];

		$rows_updated = $wpdb->update(
			$wpdb->posts,
			[
				'post_type'    => $this->new_post_type_slug,
				'post_content' => wp_json_encode( $new_block ),
			],
			[
				'ID' => $post->ID,
			]
		);
		clean_post_cache( $post->ID );

		return ! empty( $rows_updated );
	}
}
