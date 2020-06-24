<?php
/**
 * Migrates the entire site's post content to the new namespace.
 *
 * For testing, do not use in production.
 *
 * @package Block_Lab\Cli
 */

namespace Block_Lab\Cli;

use WP_CLI;
use Exception;
use Block_Lab\Blocks\Migration\Post_Content;

/**
 * Migrates all of the post content to the new namespace.
 *
 * Only for testing, not production.
 * Formats the results in a WP-CLI table.
 */
function migrate_all_post_content() {
	WP_CLI::confirm( 'Are you sure you want to migrate all of your site content to the new block namespace?' );

	$results       = ( new Post_Content( 'block-lab', 'genesis-custom-blocks' ) )->migrate_all();
	$success_posts = array_filter(
		$results,
		static function( $result ) {
			return is_int( $result );
		}
	);
	$error_posts   = array_filter(
		$results,
		static function( $result ) {
			return is_wp_error( $result );
		}
	);

	if ( empty( $error_posts ) ) {
		WP_CLI::success(
			sprintf(
				'%d posts were migrated successfully',
				count( $success_posts )
			)
		);
	} else {
		WP_CLI::warning(
			sprintf(
				'%d posts were migrated successfully, and %d had errors',
				count( $success_posts ),
				count( $error_posts )
			)
		);

		WP_CLI::line( 'Error messages:' );
		foreach ( $error_posts as $error_post ) {
			WP_CLI::line( $error_post->get_error_code() );
		}
	}

	$key_post_id   = 'Post ID';
	$key_post_type = 'Post type';
	$key_revision  = 'Revision';

	$table_results = [];
	foreach ( $success_posts as $post_id ) {
		$table_results[] = [
			$key_post_id   => $post_id,
			$key_post_type => get_post_type( $post_id ),
			$key_revision  => get_revision_link( $post_id ),
		];
	}

	WP_CLI::line( "\nMigrated successfully: \n" );
	WP_CLI\Utils\format_items(
		'table',
		$table_results,
		[ $key_post_id, $key_post_type, $key_revision ]
	);
}

/**
 * Gets the link to the WP revision UI.
 *
 * Mainly taken from includes/meta-box.php
 *
 * @param int $post_id The ID of the post.
 * @return string|false The link to the revision UI, or false if there is no revision available.
 */
function get_revision_link( $post_id ) {
	$revisions = wp_get_post_revisions( $post_id );
	if ( empty( $revisions ) ) {
		return false;
	}

	reset( $revisions ); // Reset the pointer.

	return add_query_arg(
		[
			'revision' => key( $revisions ),
		],
		admin_url( 'revision.php' )
	);
}

if ( ! defined( 'WP_CLI' ) ) {
	echo "Please run this with WP-CLI via: wp eval-file tests/bin/migrate-post-content.php\n";
	exit( 1 );
}

// Run the script.
try {
	migrate_all_post_content();
} catch ( Exception $e ) {
	WP_CLI::error( $e->getMessage() );
}
