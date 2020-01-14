<?php
/**
 * Taxonomy control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Taxonomy
 */
class Taxonomy extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'taxonomy';

	/**
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'object';

	/**
	 * Taxonomy constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Taxonomy', 'block-lab' );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		$this->settings[] = new Control_Setting( $this->settings_config['location'] );
		$this->settings[] = new Control_Setting( $this->settings_config['width'] );
		$this->settings[] = new Control_Setting( $this->settings_config['help'] );
		$this->settings[] = new Control_Setting(
			[
				'name'     => 'post_type_rest_slug',
				'label'    => __( 'Taxonomy Type', 'block-lab' ),
				'type'     => 'taxonomy_type_rest_slug',
				'default'  => 'posts',
				'sanitize' => [ $this, 'sanitize_taxonomy_type_rest_slug' ],
			]
		);
	}

	/**
	 * Renders a <select> of public taxonomy types.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_taxonomy_type_rest_slug( $setting, $name, $id ) {
		$taxonomy_slugs = $this->get_taxonomy_type_rest_slugs();
		$this->render_select( $setting, $name, $id, $taxonomy_slugs );
	}

	/**
	 * Gets the REST slugs of public taxonomy types.
	 *
	 * @return array {
	 *     An associative array of the post type REST slugs.
	 *
	 *     @type string $rest_slug The REST slug of the post type.
	 *     @type string $name      The name of the post type.
	 * }
	 */
	public function get_taxonomy_type_rest_slugs() {
		$taxonomy_rest_slugs = [];
		foreach ( get_taxonomies( [ 'show_in_rest' => true ] ) as $taxonomy_slug ) {
			$taxonomy_object                   = get_taxonomy( $taxonomy_slug );
			$rest_slug                         = ! empty( $taxonomy_object->rest_base ) ? $taxonomy_object->rest_base : $taxonomy_slug;
			$taxonomy_rest_slugs[ $rest_slug ] = $taxonomy_object->label;
		}
		return $taxonomy_rest_slugs;
	}

	/**
	 * Sanitize the taxonomy type REST slug, to ensure that it's registered and public.
	 *
	 * @param string $value The rest_base of the post type to sanitize.
	 * @return string|null The sanitized rest_base of the post type, or null.
	 */
	public function sanitize_taxonomy_type_rest_slug( $value ) {
		if ( array_key_exists( $value, $this->get_taxonomy_type_rest_slugs() ) ) {
			return $value;
		}
		return null;
	}

	/**
	 * Validates the value to be made available to the front-end template.
	 *
	 * @param mixed $value The value to either make available as a variable or echoed on the front-end template.
	 * @param bool  $echo  Whether this will be echoed.
	 * @return string|WP_Term|null $value The value to be made available or echoed on the front-end template.
	 */
	public function validate( $value, $echo ) {
		$term = isset( $value['id'] ) ? get_term( $value['id'] ) : null;

		if ( $echo ) {
			return $term ? $term->name : '';
		} else {
			return $term;
		}
	}
}
