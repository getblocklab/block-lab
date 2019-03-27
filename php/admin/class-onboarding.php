<?php
/**
 * User onboarding.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;

/**
 * Class Onboarding
 */
class Onboarding extends Component_Abstract {

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
	}

	/**
	 * Runs during plugin activation.
	 */
	public function plugin_activation() {
		$this->add_dummy_data();
	}

	/**
	 * Create a dummy starter block when the plugin is activated for the first time.
	 */
	public function add_dummy_data() {
		// Note: wp_count_posts() does not work here.
		$blocks = get_posts(
			array(
				'post_type'   => block_lab()->block_post->slug,
				'numberposts' => '-1',
				'post_status' => 'any',
				'fields'      => 'ids',
			)
		);

		if ( count( $blocks ) > 0 ) {
			return;
		}

		wp_insert_post(
			array(
				'post_title'   => __( 'Example Block', 'block-lab' ),
				'post_name'    => 'example-block',
				'post_status'  => 'draft',
				'post_type'    => block_lab()->block_post->slug,
				'post_content' => wp_json_encode(
					array(
						'block-lab/example-block' => array(
							'title'    => __( 'Example Block', 'block-lab' ),
							'name'     => 'example-block',
							'icon'     => 'block_lab',
							'category' => 'common',
							'keywords' => array(
								__( 'sample', 'block-lab' ), // translators: A keyword, used for search.
								__( 'tutorial', 'block-lab' ), // translators: A keyword, used for search.
								__( 'template', 'block-lab' ), // translators: A keyword, used for search.
							),
							'fields'   => array(
								'title'       => array(
									'name'        => 'title',
									'label'       => __( 'Title', 'block-lab' ),
									'control'     => 'text',
									'type'        => 'string',
									'location'    => 'editor',
									'order'       => 0,
									'help'        => __( 'The primary display text', 'block-lab' ),
									'default'     => '',
									'placeholder' => '',
									'maxlength'   => null,
								),
								'description' => array(
									'name'        => 'description',
									'label'       => __( 'Description', 'block-lab' ),
									'control'     => 'textarea',
									'type'        => 'string',
									'location'    => 'editor',
									'order'       => 1,
									'help'        => '',
									'default'     => '',
									'placeholder' => '',
									'maxlength'   => null,
									'number_rows' => 4,
								),
								'button-text' => array(
									'name'        => 'button-text',
									'label'       => __( 'Button Text', 'block-lab' ),
									'control'     => 'text',
									'type'        => 'string',
									'location'    => 'editor',
									'order'       => 2,
									'help'        => __( 'A Call-to-Action', 'block-lab' ),
									'default'     => '',
									'placeholder' => '',
									'maxlength'   => null,
								),
								'button-link' => array(
									'name'        => 'button-link',
									'label'       => __( 'Button Link', 'block-lab' ),
									'control'     => 'url',
									'type'        => 'string',
									'location'    => 'editor',
									'order'       => 3,
									'help'        => __( 'The destination URL', 'block-lab' ),
									'default'     => '',
									'placeholder' => '',
								),
							),
						),
					)
				),
			)
		);
	}
}
