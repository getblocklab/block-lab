<?php
/**
 * Primary plugin file.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab;

/**
 * Class Plugin
 */
class Plugin extends Plugin_Abstract {

	/**
	 * Utility methods.
	 *
	 * @var Util
	 */
	protected $util;

	/**
	 * WP Admin resources.
	 *
	 * @var Admin\Admin
	 */
	public $admin;

	/**
	 * Block loader.
	 *
	 * @var Blocks\Loader
	 */
	public $loader;

	/**
	 * The slug of the post type that stores the blocks.
	 *
	 * @since 1.3.5
	 * @var string
	 */
	public $post_type_slug = 'block_lab';

	/**
	 * Execute this as early as possible.
	 */
	public function init() {
		$this->util = new Util();
		$this->register_component( $this->util );
		$this->register_component( new Post_Types\Block_Post() );

		$this->loader = new Blocks\Loader();
		$this->register_component( $this->loader );

		register_activation_hook(
			$this->get_file(),
			function() {
				$onboarding = new Admin\Onboarding();
				$onboarding->plugin_activation();
			}
		);
	}

	/**
	 * Execute this once plugins are loaded. (not the best place for all hooks)
	 */
	public function plugin_loaded() {
		$this->admin = new Admin\Admin();
		$this->register_component( $this->admin );
	}
}
