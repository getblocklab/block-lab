<?php
/**
 * Post control.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks\Controls;

/**
 * Class Post
 */
class Post extends Control_Abstract {

	/**
	 * Control name.
	 *
	 * @var string
	 */
	public $name = 'post';

	/**
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'object';

	/**
	 * Post constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->label = __( 'Post', 'block-lab' );
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
				'label'    => __( 'Post Type', 'block-lab' ),
				'type'     => 'post_type_rest_slug',
				'default'  => 'posts',
				'sanitize' => [ $this, 'sanitize_post_type_rest_slug' ],
			]
		);
	}

	/**
	 * Render a <select> of public post types.
	 *
	 * @param Control_Setting $setting The Control_Setting being rendered.
	 * @param string          $name    The name attribute of the option.
	 * @param string          $id      The id attribute of the option.
	 *
	 * @return void
	 */
	public function render_settings_post_type_rest_slug( $setting, $name, $id ) {
		$post_type_slugs = $this->get_post_type_rest_slugs();
		$this->render_select( $setting, $name, $id, $post_type_slugs );
	}

	/**
	 * Gets the REST slugs of public post types, other than 'attachment'.
	 *
	 * @return array {
	 *     An associative array of the post type REST slugs.
	 *
	 *     @type string $rest_slug The REST slug of the post type.
	 *     @type string $name      The name of the post type.n
	 * }
	 */
	public function get_post_type_rest_slugs() {
		$post_type_rest_slugs = [];
		foreach ( get_post_types( [ 'public' => true ] ) as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			if ( ! $post_type_object || empty( $post_type_object->show_in_rest ) ) {
				continue;
			}
			if ( 'attachment' === $post_type ) {
				continue;
			}
			$rest_slug                          = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type;
			$labels                             = get_post_type_labels( $post_type_object );
			$post_type_name                     = isset( $labels->name ) ? $labels->name : $post_type;
			$post_type_rest_slugs[ $rest_slug ] = $post_type_name;
		}
		return $post_type_rest_slugs;
	}

	/**
	 * Sanitize the post type REST slug, to ensure that it's a public post type.
	 *
	 * This expects the rest_base of the post type, as it's easier to pass that to apiFetch in the Post control.
	 * So this iterates through the public post types, to find if one has the rest_base equal to $value.
	 *
	 * @param string $value The rest_base of the post type to sanitize.
	 * @return string|null The sanitized rest_base of the post type, or null.
	 */
	public function sanitize_post_type_rest_slug( $value ) {
		if ( array_key_exists( $value, $this->get_post_type_rest_slugs() ) ) {
			return $value;
		}
		return null;
	}

	/**
	 * Validates the value to be made available to the front-end template.
	 *
	 * @param mixed $value The value to either make available as a variable or echoed on the front-end template.
	 * @param bool  $echo  Whether this will be echoed.
	 * @return string|WP_Post|null $value The value to be made available or echoed on the front-end template.
	 */
	public function validate( $value, $echo ) {
		$post = isset( $value['id'] ) ? get_post( $value['id'] ) : null;
		if ( $echo ) {
			return $post ? get_the_title( $post ) : '';
		} else {
			return $post;
		}
	}
}
