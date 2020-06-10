<?php
/**
 * Post_Content.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Migration;

use WP_Post;
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
	 * @param string $previous_block_namespace  Previous namespace of the blocks.
	 * @param string $new_block_namespace       New namespace of the blocks.
	 */
	public function __construct( $previous_block_namespace, $new_block_namespace ) {
		$this->previous_block_namespace = $previous_block_namespace;
		$this->new_block_namespace      = $new_block_namespace;
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
	 * @param WP_Post $post The post to convert.
	 * @return int|WP_Error The post ID that was changed, or a WP_Error on failure.
	 */
	public function migrate_single( WP_Post $post ) {
		$new_post_content = preg_replace(
			'#(<!--\s+wp:)(' . sanitize_key( $this->previous_block_namespace ) . ')(/[a-z][a-z0-9_-]*)#s',
			'$1' . sanitize_key( $this->new_block_namespace ) . '$3',
			$post->post_content
		);

		return wp_update_post(
			[
				'ID'           => $post->ID,
				'post_content' => wp_slash( $new_post_content ),
			],
			true
		);
	}
}
