<?php
/**
 * Block Lab Importer.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
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
		add_action( 'admin_init', array( $this, 'register_importer' ) );
	}

	/**
	 * Register the importer for the Tools > Import admin screen
	 */
	public function register_importer() {
		register_importer(
			$this->slug,
			__( 'Block Lab', 'block-lab' ),
			__( 'Import custom blocks created with Block Lab.', 'block-lab' ),
			array( $this, 'render_page' )
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

				$file = wp_import_handle_upload();

				if ( $this->validate_upload( $file ) ) {
					// This is on the local filesystem, so file_get_contents() is ok to use here.
					$json   = file_get_contents( $file['file'] ); // @codingStandardsIgnoreLine
					$blocks = json_decode( $json, true );

					$this->import_blocks( $blocks, $file );
				}
				break;
      case 2:
		    $cache_dir = wp_get_upload_dir()['basedir'] . '/block-lab';
        $file = array( 'file' => $cache_dir . '/cache.json');
          try {
          if ( $this->validate_upload( $file ) ) {
            // This is on the local filesystem, so file_get_contents() is ok to use here.
            $json   = file_get_contents( $file['file'] ); // @codingStandardsIgnoreLine
            $blocks = json_decode( $json, true );
            $update_blocks = array();
            foreach($blocks as $name => $config){
              if($_GET[$name] == 'on'){
                $update_blocks[] = $config;
              }
            }
            $this->import_blocks( $update_blocks, $file, true);
          }else{
            throw new Exception('No valid upload cache found during update.');
          }
        }catch(Exception $e){
          $this->render_block_import_error( 'Error updating blocks', $e->getMessage() );
		    }
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
		<?php
		wp_import_upload_form(
			add_query_arg(
				array(
					'import' => $this->slug,
					'step'   => 1,
				)
			)
		);
	}

	/**
	 * Render the currently importing block title.
	 *
	 * @param string $title The title of the block.
	 */
	public function render_block_import_success( $title ) {
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
	public function render_block_import_error( $title, $error ) {
		echo wp_kses_post(
			sprintf(
				'<p>%s %s</p>',
				sprintf(
					// translators: placeholder refers to title of custom block.
					__( 'Error importing %s.', 'block-lab' ),
					'<strong>' . esc_html( $title ) . '</strong>'
				),
				esc_html( $error )
			)
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
	 * Handles the JSON upload and initial parsing of the file.
	 *
	 * @param array $file The file.
	 * @return bool False if error uploading or invalid file, true otherwise.
	 */
	public function validate_upload( $file ) {
		if ( isset( $file['error'] ) ) {
			echo wp_kses_post(
				sprintf(
					'<p><strong>%1$s</strong></p><p>%2$s</p>',
					__( 'Sorry, there was an error uploading the file.', 'block-lab' ),
					esc_html( $file['error'] )
				)
			);
			return false;
		} elseif ( ! file_exists( $file['file'] ) ) {
			echo wp_kses_post(
				sprintf(
					'<p><strong>%1$s</strong></p><p>%2$s</p>',
					__( 'Sorry, there was an error uploading the file.', 'block-lab' ),
					sprintf(
						// translators: placeholder refers to a file directory.
						__( 'The export file could not be found at %1$s. It is likely that this was caused by a permissions problem.', 'block-lab' ),
						'<code>' . esc_html( $file['file'] ) . '</code>'
					)
				)
			);
			return false;
		}

		// This is on the local filesystem, so file_get_contents() is ok to use here.
		$json = file_get_contents( $file['file'] ); // @codingStandardsIgnoreLine
		$data = json_decode( $json, true );

		if ( ! is_array( $data ) ) {
			echo wp_kses_post(
				sprintf(
					'<p><strong>%1$s</strong></p><p>%2$s</p>',
					__( 'Sorry, there was an error processing the file.', 'block-lab' ),
					__( 'Invalid JSON.', 'block-lab' )
				)
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
	public function import_blocks( $blocks, $file, $update_existing=false ) {
	  $existing = array();
		foreach ( $blocks as $config ) {
			if ( ! isset( $config['title'] ) || ! isset( $config['name'] ) ) {
				continue;
			}

      $id = false;
			// Check if block already exists.
			$registered_blocks = get_dynamic_block_names();
			if ( in_array( 'block-lab/' . $config['name'], $registered_blocks, true ) ) {
				if(!$update_existing){
				  $existing[] = 'block-lab/' . $config['name'];
          continue;
				}else{
          if ( $post = get_page_by_path( $config['name'], OBJECT, block_lab()->get_post_type_slug() ) )
              $id = $post->ID;
        }
			}

			$json = wp_json_encode( array( 'block-lab/' . $config['name'] => $config ), JSON_UNESCAPED_UNICODE );

      $post_data = array(
        'post_title'   => $config['title'],
        'post_name'    => $config['name'],
        'post_content' => wp_slash( $json ),
        'post_status'  => 'publish',
        'post_type'    => block_lab()->get_post_type_slug(),
      );

      if( $id ){
        $post_data['ID']  = $id;
      }
			$post = wp_insert_post( $post_data );

			if ( is_wp_error( $post ) ) {
				$this->render_block_import_error( $config['title'], $post->get_error_message() );
			} else {
				$this->render_block_import_success( $config['title'] );
			}
		}
		if( sizeof($existing) > 0 ){
		  $cache_dir = wp_get_upload_dir()['basedir'] . '/block-lab';
		  try {
        if (!file_exists($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }
        file_put_contents($cache_dir . '/cache.json', file_get_contents($file['file']));
      } catch(Exception $e) {
				$this->render_block_import_error( $config['title'], 'Import of blocks failed. Existing block content could noe be cached. <br/>\n ' . $e->getMessage() );
      }
      $this->render_choose_blocks($existing);
		}

		$this->render_done();
	}

  /**
   * Render the interface for choosing blocks to update.
   */
  public function render_choose_blocks($existing_names) {
    $link = explode('?', $_SERVER['REQUEST_URI'])[0];
    $form = '<p>Please select the blocks to update with the matching data in your imported file</p><form action="'.$link.'">';
    foreach ( $existing_names as $name ) {
      $form .= '<input type="checkbox" name="'.$name.'" checked>'.$name.'<br>';
    }
    $form .= '<input type="submit" value="Update Selected">';
    $form .= wp_nonce_field();
    $form .= '<input type="hidden" name="import" value="block-lab">';
    $form .= '<input type="hidden" name="step" value="2">';
    $form .= '</form>';
    echo $form;
  }

}
