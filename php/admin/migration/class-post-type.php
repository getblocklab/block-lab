<?php
/**
 * Post_Type.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin\Migration;

use WP_Error;
use WP_Post;
use WP_Query;

/**
 * Class Post_Type
 */
class Post_Type {

	/**
	 * The previous slug of the custom post type (in Block Lab).
	 *
	 * @var string
	 */
	private $previous_post_type_slug;

	/**
	 * The previous namespace of the block.
	 *
	 * @var string
	 */
	private $previous_block_namespace;

	/**
	 * The previous default block icon.
	 *
	 * @var string
	 */
	private $previous_default_icon;

	/**
	 * The new namespace of the block.
	 *
	 * @var string
	 */
	private $new_block_namespace;

	/**
	 * The new slug of the custom post type (not in Block Lab).
	 *
	 * @var string
	 */
	private $new_post_type_slug;

	/**
	 * The new default block icon.
	 *
	 * @var string
	 */
	private $new_default_icon;

	/**
	 * Post_Type constructor.
	 *
	 * @param string $previous_post_type_slug  Previous slug of the custom post type.
	 * @param string $previous_block_namespace Previous block namespace.
	 * @param string $previous_default_icon    Previous default block icon.
	 * @param string $new_post_type_slug       New slug of the custom post type.
	 * @param string $new_block_namespace      New namespace of the block.
	 * @param string $new_default_icon         New default block icon.
	 */
	public function __construct( $previous_post_type_slug, $previous_block_namespace, $previous_default_icon, $new_post_type_slug, $new_block_namespace, $new_default_icon ) {
		$this->previous_post_type_slug  = $previous_post_type_slug;
		$this->previous_block_namespace = $previous_block_namespace;
		$this->previous_default_icon    = $previous_default_icon;
		$this->new_post_type_slug       = $new_post_type_slug;
		$this->new_block_namespace      = $new_block_namespace;
		$this->new_default_icon         = $new_default_icon;
	}

	/**
	 * Migrates all of the custom post type posts to the new post_type slug and block namespace.
	 *
	 * These each store a config for a custom block,
	 * they aren't blocks that users entered into the block editor.
	 *
	 * @return array|WP_Error An array on success, a WP_Error on failure.
	 */
	public function migrate_all() {
		$posts              = $this->query_for_posts();
		$success_count      = 0;
		$error_count        = 0;
		$max_allowed_errors = 20;
		$errors             = new WP_Error();

		while ( ! empty( $posts ) && $error_count < $max_allowed_errors ) {
			foreach ( $posts as $post ) {
				$migration_result = $this->migrate_single( $post );
				if ( is_wp_error( $migration_result ) ) {
					$error_count++;
					$errors->add( $migration_result->get_error_code(), $migration_result->get_error_message() );
				} else {
					$success_count++;
				}
			}

			$posts = $this->query_for_posts();
		}

		$is_success = ! empty( $success_count ) || ( empty( $success_count ) && empty( $error_count ) );

		if ( ! $is_success ) {
			return $errors;
		}

		return [
			'successCount' => $success_count,
			'errorCount'   => $error_count,
		];
	}

	/**
	 * Migrates the custom post type post to the new post_type slug and block namespace.
	 *
	 * Inspired by the work of Weston Ruter: https://github.com/ampproject/amp-wp/blob/4880f0f58daaf07685854be8574ff25d76ff583e/includes/validation/class-amp-validated-url-post-type.php#L165-L170
	 * The post_content of the CPT has a configuration for a block like:
	 * '{"block-lab\/test-image":{"name":"test-image","title":"Test Image","excluded":[],"icon":"block_lab","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"image":{"name":"image","label":"Image","control":"image","type":"integer","order":0,"location":"editor","width":"50","help":"Here is some help text"}}}}'
	 * The beginning of this has the 'block-lab' namespace, which this changes to the new namespace.
	 *
	 * @param WP_Post $post The post to convert.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function migrate_single( WP_Post $post ) {
		global $wpdb;

		$block = json_decode( $post->post_content, true );
		if ( JSON_ERROR_NONE !== json_last_error() || empty( $block ) ) {
			return new WP_Error(
				'block_invalid_json',
				__( 'The block looks to be invalid JSON', 'block-lab' )
			);
		}

		$block_keys     = array_keys( $block );
		$old_block_name = reset( $block_keys );
		if ( empty( $block[ $old_block_name ] ) ) {
			return new WP_Error(
				'invalid_block_name',
				__( 'The block name looks to be invalid', 'block-lab' )
			);
		}

		$block_contents = $block[ $old_block_name ];
		if ( isset( $block_contents['icon'] ) && $this->previous_default_icon === $block_contents['icon'] ) {
			$block_contents['icon'] = $this->new_default_icon;
		}

		if ( empty( $block_contents['icon'] ) ) {
			$block_contents['icon'] = $this->new_default_icon;
		}

		$new_block_name = preg_replace( '#^' . $this->previous_block_namespace . '(?=/)#', $this->new_block_namespace, $old_block_name );
		$new_block      = [ $new_block_name => $block_contents ];

		$rows_updated = $wpdb->update(
			$wpdb->posts,
			[
				'post_type'    => sanitize_key( $this->new_post_type_slug ),
				'post_content' => wp_json_encode( $new_block ),
			],
			[
				'ID' => $post->ID,
			]
		);
		clean_post_cache( $post->ID );

		if ( empty( $rows_updated ) ) {
			return new WP_Error(
				'post_not_updated',
				__( 'The post was not updated', 'block-lab' )
			);
		}

		return true;
	}

	/**
	 * Gets the posts of the previous post_type.
	 *
	 * This query won't find posts that were already migrated, as the migration changes the 'post_type'.
	 * So this doesn't need an 'offset' parameter.
	 *
	 * @return WP_Post[] The posts that were found.
	 */
	private function query_for_posts() {
		$query = new WP_Query(
			[
				'post_type'      => $this->previous_post_type_slug,
				'posts_per_page' => 10,
				'post_status'    => 'any',
			]
		);

		return $query->posts;
	}
}
