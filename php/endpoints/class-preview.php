<?php
/**
 * Preview retrieves sanitized HTML to send to the Gutenberg preview.
 *
 * @package Block_Lab
 */

namespace Block_Lab\Endpoints;

use Block_Lab\Blocks\Loader;
use Block_Lab\Component_Abstract;

/**
 * Class Preview
 */
class Preview extends Component_Abstract {

	const BASE = 'block-lab/v1';

	/**
	 * Array of available blocks.
	 *
	 * @var array
	 */
	public $blocks = [];

	/**
	 * Used to load blocks.
	 *
	 * @var Loader
	 */
	public $block_loader;

	/**
	 * Initialise the block loader to get blocks.
	 *
	 * @return $this
	 */
	public function init() {
		$this->block_loader = new Loader();
		$this->block_loader->set_plugin( $this->plugin );
		$this->block_loader->init();
		$this->blocks = json_decode( $this->block_loader->blocks, true );

		return $this;
	}

	/**
	 * Register all the hooks.
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Register endpoint.
	 */
	public function rest_api_init() {
		register_rest_route(
			static::BASE, 'block-preview', array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'handle_preview_request' ),
				'args'                => array(),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Return a block preview.
	 *
	 * @param \WP_REST_Request $r REST request object.
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function handle_preview_request( \WP_REST_Request $r ) {
		$content = '';

		$slug = $r->get_param( 'slug' );

		if ( ! empty( $slug ) && array_key_exists( 'block-lab/' . $slug, $this->blocks ) ) {
			$block      = $this->blocks[ 'block-lab/' . $slug ];
			$attributes = array();

			foreach ( $block['fields'] as $field ) {

				switch ( $field['type'] ) {
					case 'boolean':
						$attributes[ $field['name'] ] = 'true' === $r->get_param( $field['name'] ) ? true : false;
						break;
					case 'integer':
						$attributes[ $field['name'] ] = (int) $r->get_param( $field['name'] );
						break;
					case 'array':
						$attributes[ $field['name'] ] = explode( ',', $r->get_param( $field['name'] ) );
						break;
					default:
						$attributes[ $field['name'] ] = $r->get_param( $field['name'] );
						break;
				}
			}

			$content = $this->block_loader->render_block_template(
				$block,
				$attributes,
				array( 'preview', 'block' )
			);
		}

		return rest_ensure_response( $content );
	}

}
