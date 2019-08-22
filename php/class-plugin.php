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
	 * Post types registered by this plugin.
	 *
	 * @var Post_Types\Block_Post
	 */
	public $block_post;

	/**
	 * Execute this as early as possible.
	 */
	public function init() {
		$this->util = new Util();
		$this->register_component( $this->util );

		$this->block_post = new Post_Types\Block_Post();
		$this->register_component( $this->block_post );

		$this->register_component( new Blocks\Loader() );

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
