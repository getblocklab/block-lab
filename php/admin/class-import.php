<?php
/**
 * Block Lab Importer.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;

/**
 * Class Import
 */
class Import extends Component_Abstract {

	/**
	 * Importer slug.
	 *
	 * @var string
	 */
	public $slug = 'block-lab';

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'register_importer' ] );
	}

	/**
	 * Register the importer for the Tools > Import admin screen
	 */
	public function register_importer() {
		register_importer(
			$this->slug,
			__( 'Block Lab', 'block-lab' ),
			__( 'Import custom blocks created with Block Lab.', 'block-lab' ),
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Render the import page. Manages the three separate stages of the JSON import process.
	 */
	public function render_page() {
		$step = filter_input( INPUT_GET, 'step', FILTER_SANITIZE_NUMBER_INT );

		ob_start();

		$this->render_page_header();

		switch ( $step ) {
			case 0:
			default:
				$this->render_welcome();
				break;
			case 1:
				check_admin_referer( 'import-upload' );

				$upload_dir = wp_get_upload_dir();

				if ( ! isset( $upload_dir['basedir'] ) ) {
					$this->render_import_error(
						__( 'Sorry, there was an error uploading the file.', 'block-lab' ),
						__( 'Upload base directory not set.', 'block-lab' )
					);
				}

				$cache_dir = $upload_dir['basedir'] . '/block-lab';
				$file      = wp_import_handle_upload();

				if ( $this->validate_upload( $file ) ) {
					if ( ! file_exists( $cache_dir ) ) {
						mkdir( $cache_dir, 0777, true );
					}

					// This is on the local filesystem, so file_get_contents() is ok to use here.
					file_put_contents( $cache_dir . '/import.json', file_get_contents( $file['file'] ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions

					$json   = file_get_contents( $file['file'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions
					$blocks = json_decode( $json, true );

					$this->render_choose_blocks( $blocks );
				}
				break;
			case 2:
				$cache_dir = wp_get_upload_dir()['basedir'] . '/block-lab';
				$file      = [ 'file' => $cache_dir . '/import.json' ];

				if ( $this->validate_upload( $file ) ) {
					// This is on the local filesystem, so file_get_contents() is ok to use here.
					$json   = file_get_contents( $file['file'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions
					$blocks = json_decode( $json, true );

					$import_blocks = [];
					foreach ( $blocks as $block_namespace => $block ) {
						if ( 'on' === filter_input( INPUT_GET, $block_namespace, FILTER_SANITIZE_STRING ) ) {
							$import_blocks[ $block_namespace ] = $block;
						}
					}

					$this->import_blocks( $import_blocks );
				}

				break;
		}

		$html = ob_get_clean();
		echo '<div class="wrap block-lab-import">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render the Import page header.
	 */
	public function render_page_header() {
		?>
		<h2><?php esc_html_e( 'Import Block Lab Content Blocks', 'block-lab' ); ?></h2>
		<?php
	}

	/**
	 * Render the welcome message.
	 */
	public function render_welcome() {
		?>
		<p><?php esc_html_e( 'Welcome! This importer processes Block Lab JSON files, adding custom blocks to this site.', 'block-lab' ); ?></p>
		<p><?php esc_html_e( 'Choose a JSON (.json) file to upload, then click Upload file and import.', 'block-lab' ); ?></p>
		<p>
			<?php
			echo wp_kses(
				sprintf(
					/* translators: %1$s: an opening anchor tag, %2$s: a closing anchor tag */
					__( 'This JSON file should come from the export link or bulk action in the %1$sContent Blocks screen%2$s, not from the main Export tool.', 'block-lab' ),
					sprintf(
						'<a href="%1$s">',
						esc_url(
							admin_url(
								add_query_arg(
									[ 'post_type' => block_lab()->get_post_type_slug() ],
									'edit.php'
								)
							)
						)
					),
					'</a>'
				),
				[ 'a' => [ 'href' => [] ] ]
			);
			?>
		</p>

		<?php
		wp_import_upload_form(
			add_query_arg(
				[
					'import' => $this->slug,
					'step'   => 1,
				]
			)
		);
	}

	/**
	 * Render the currently importing block title.
	 *
	 * @param string $title The title of the block.
	 */
	public function render_import_success( $title ) {
		echo wp_kses_post(
			sprintf(
				'<p>%s</p>',
				sprintf(
					// translators: placeholder refers to title of custom block.
					__( 'Successfully imported %1$s.', 'block-lab' ),
					'<strong>' . esc_html( $title ) . '</strong>'
				)
			)
		);
	}

	/**
	 * Render the currently importing block title.
	 *
	 * @param string $title The title of the block.
	 * @param string $error The error being reported.
	 */
	public function render_import_error( $title, $error ) {
		echo wp_kses_post(
			sprintf( '<p><strong>%s</strong></p><p>%s</p>', $title, $error )
		);
	}

	/**
	 * Render the successful import message.
	 */
	public function render_done() {
		?>
		<p><?php esc_html_e( 'All done!', 'block-lab' ); ?></p>
		<?php
	}

	/**
	 * Render the interface for choosing blocks to update.
	 *
	 * @param array $blocks An array of block names to choose from.
	 */
	public function render_choose_blocks( $blocks ) {
		?>
		<p><?php esc_html_e( 'Please select the blocks to import:', 'block-lab' ); ?></p>
		<form>
			<?php
			foreach ( $blocks as $block_namespace => $block ) {
				$action = __( 'Import', 'block-lab' );
				if ( $this->block_exists( $block_namespace ) ) {
					$action = __( 'Replace', 'block-lab' );
				}
				?>
				<p>
					<input type="checkbox" name="<?php echo esc_attr( $block_namespace ); ?>" id="<?php echo esc_attr( $block_namespace ); ?>" checked>
					<label for="<?php echo esc_attr( $block_namespace ); ?>">
						<?php echo esc_html( $action ); ?> <strong><?php echo esc_attr( $block['title'] ); ?></strong>
					</label>
				</p>
				<?php
			}
			wp_nonce_field();
			?>
			<input type="hidden" name="import" value="block-lab">
			<input type="hidden" name="step" value="2">
			<p class="submit"><input type="submit" value="<?php esc_attr_e( 'Import Selected', 'block-lab' ); ?>" class="button button-primary"></p>
		</form>
		<?php
	}

	/**
	 * Handles the JSON upload and initial parsing of the file.
	 *
	 * @param array $file The file.
	 * @return bool False if error uploading or invalid file, true otherwise.
	 */
	public function validate_upload( $file ) {
		if ( isset( $file['error'] ) ) {
			$this->render_import_error(
				__( 'Sorry, there was an error uploading the file.', 'block-lab' ),
				$file['error']
			);
			return false;
		} elseif ( ! file_exists( $file['file'] ) ) {
			$this->render_import_error(
				__( 'Sorry, there was an error uploading the file.', 'block-lab' ),
				sprintf(
					// translators: placeholder refers to a file directory.
					__( 'The export file could not be found at %1$s. It is likely that this was caused by a permissions problem.', 'block-lab' ),
					'<code>' . esc_html( $file['file'] ) . '</code>'
				)
			);
			return false;
		}

		// This is on the local filesystem, so file_get_contents() is ok to use here.
		$json = file_get_contents( $file['file'] ); // @codingStandardsIgnoreLine
		$data = json_decode( $json, true );

		if ( ! is_array( $data ) ) {
			$this->render_import_error(
				__( 'Sorry, there was an error processing the file.', 'block-lab' ),
				__( 'Invalid JSON.', 'block-lab' )
			);
			return false;
		}

		return true;
	}

	/**
	 * Import data into new Block Lab posts.
	 *
	 * @param array $blocks An array of Block Lab content blocks.
	 */
	public function import_blocks( $blocks ) {
		foreach ( $blocks as $block_namespace => $block ) {
			if ( ! isset( $block['title'] ) || ! isset( $block['name'] ) ) {
				continue;
			}

			$post_id = false;

			if ( $this->block_exists( $block_namespace ) ) {
				$post = get_page_by_path( $block['name'], OBJECT, block_lab()->get_post_type_slug() );
				if ( $post ) {
					$post_id = $post->ID;
				}
			}

			$json = wp_json_encode( [ $block_namespace => $block ], JSON_UNESCAPED_UNICODE );

			$post_data = [
				'post_title'   => $block['title'],
				'post_name'    => $block['name'],
				'post_content' => wp_slash( $json ),
				'post_status'  => 'publish',
				'post_type'    => block_lab()->get_post_type_slug(),
			];

			if ( $post_id ) {
				$post_data['ID'] = $post_id;
			}
			$post = wp_insert_post( $post_data );

			if ( is_wp_error( $post ) ) {
				$this->render_import_error(
					sprintf(
						// translators: placeholder refers to title of custom block.
						__( 'Error importing %s.', 'block-lab' ),
						$block['title']
					),
					$post->get_error_message()
				);
			} else {
				$this->render_import_success( $block['title'] );
			}
		}

		$this->render_done();
	}

	/**
	 * Check if block already exists.
	 *
	 * @param string $block_namespace The JSON key for the block. e.g. block-lab/foo.
	 *
	 * @return bool
	 */
	private function block_exists( $block_namespace ) {
		$registered_blocks = get_dynamic_block_names();
		if ( in_array( $block_namespace, $registered_blocks, true ) ) {
			return true;
		}

		return false;
	}
}
