<?php
/**
 * Post_Content.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Migration;

use WP_Error;

/**
 * Class Post_Content
 */
class Post_Content {

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
	 * Post_Content constructor.
	 *
	 * @param string $previous_block_namespace Previous namespace of the blocks.
	 * @param string $new_block_namespace      New namespace of the blocks.
	 */
	public function __construct( $previous_block_namespace, $new_block_namespace ) {
		$this->previous_block_namespace = $previous_block_namespace;
		$this->new_block_namespace      = $new_block_namespace;
	}

	/**
	 * Migrates all of the block namespaces in all of the posts that have Block Lab blocks.
	 */
	public function migrate_all() {
		$posts = $this->query_for_posts();

		while ( $posts ) {
			foreach ( $posts as $post ) {
				if ( isset( $post->ID ) ) {
					$this->migrate_single( $post->ID );
				}
			}

			$posts = $this->query_for_posts();
		}
	}

	/**
	 * Migrates the block namespaces in post_content.
	 *
	 * Blocks are stored in the post_content of a post with a namespace,
	 * like '<!-- wp:block-lab/test-image {"example-image":8} /-->'.
	 * In that case, 'block-lab' needs to be changed to the new namespace.
	 * But nothing else in the block should be changed.
	 * The block pattern is mainly taken from Gutenberg.
	 *
	 * @see https://github.com/WordPress/wordpress-develop/blob/78d1ab2ed40093a5bd2a75b01ceea37811739f55/src/wp-includes/class-wp-block-parser.php#L413
	 *
	 * @param int $post_id The ID of the post to convert.
	 * @return int|WP_Error The post ID that was changed, or a WP_Error on failure.
	 */
	public function migrate_single( $post_id ) {
		$post = get_post( $post_id );
		if ( ! isset( $post->ID ) ) {
			return new WP_Error( 'Invalid post ID' );
		}

		$replacement_count = 0;
		$new_post_content  = preg_replace(
			'#(<!--\s+wp:)(' . sanitize_key( $this->previous_block_namespace ) . ')(/[a-z][a-z0-9_-]*)#s',
			'$1' . sanitize_key( $this->new_block_namespace ) . '$3',
			$post->post_content,
			-1,
			$replacement_count
		);

		if ( 0 === $replacement_count ) {
			return new WP_Error( 'Post content did not have blocks with the namespace' );
		}

		return wp_update_post(
			[
				'ID'           => $post->ID,
				'post_content' => wp_slash( $new_post_content ),
			],
			true
		);
	}

	/**
	 * Gets posts that have Block Lab blocks in their post_content.
	 *
	 * Queries for the posts that have wp:block-lab/ in the post content,
	 * meaning they probably have a Block Lab block.
	 *
	 * @return array The posts that were found.
	 */
	private function query_for_posts() {
		global $wpdb;

		$query_limit = 10;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->posts} WHERE post_content LIKE %s LIMIT %d",
				'%' . $wpdb->esc_like( 'wp:' . $this->previous_block_namespace . '/' ) . '%',
				absint( $query_limit )
			)
		);
	}
}
