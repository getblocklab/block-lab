<?php
/**
 * Primary plugin file.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab;

/**
 * Class Plugin
 */
class Plugin extends Plugin_Abstract {
	/**
	 * WP Admin resources.
	 *
	 * @var Admin\Admin
	 */
	public $admin;

	/**
	 * Block Post Type.
	 *
	 * @var Post_Types\Block_Post
	 */
	public $block_post;

	/**
	 * Initiate the loading of new blocks.
	 *
	 * @var Blocks\Loader
	 */
	public $loader;

	/**
	 * Execute this as early as possible.
	 */
	public function init() {
		$this->set_util();

		$this->block_post = new Post_Types\Block_Post();
		$this->register_component( $this->block_post );

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
