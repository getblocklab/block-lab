<?php
/**
 * WP Admin resources.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;
use Block_Lab\Admin\Migration\Api;
use Block_Lab\Admin\Migration\Subscription_Api;
use Block_Lab\Admin\Migration\Notice;
use Block_Lab\Admin\Migration\Submenu;

/**
 * Class Admin
 */
class Admin extends Component_Abstract {

	/**
	 * JSON import.
	 *
	 * @var Import
	 */
	public $import;

	/**
	 * Plugin license.
	 *
	 * @var License
	 */
	public $license;

	/**
	 * User onboarding.
	 *
	 * @var Onboarding
	 */
	public $onboarding;

	/**
	 * Plugin settings.
	 *
	 * @var Settings
	 */
	public $settings;

	/**
	 * Plugin upgrade.
	 *
	 * @var Upgrade
	 */
	public $upgrade;

	/**
	 * The migration API.
	 *
	 * @var Api
	 */
	private $api;

	/**
	 * THe subscription API for the migration.
	 *
	 * @var Subscription_Api
	 */
	private $subscription_api;

	/**
	 * The migration notice.
	 *
	 * @var Notice
	 */
	private $notice;

	/**
	 * The migration submenu under the Block Lab menu item.
	 *
	 * @var Submenu
	 */
	private $submenu;

	/**
	 * Initialise the Admin component.
	 */
	public function init() {
		$this->settings = new Settings();
		block_lab()->register_component( $this->settings );

		$this->license = new License();
		block_lab()->register_component( $this->license );

		$this->onboarding = new Onboarding();
		block_lab()->register_component( $this->onboarding );

		$this->api = new Api();
		block_lab()->register_component( $this->api );

		$this->subscription_api = new Subscription_Api();
		block_lab()->register_component( $this->subscription_api );

		$this->notice = new Notice();
		block_lab()->register_component( $this->notice );

		$this->submenu = new Submenu();
		block_lab()->register_component( $this->submenu );

		$show_pro_nag = apply_filters( 'block_lab_show_pro_nag', false );
		if ( $show_pro_nag && ! block_lab()->is_pro() ) {
			$this->upgrade = new Upgrade();
			block_lab()->register_component( $this->upgrade );
		} else {
			$this->maybe_settings_redirect();
		}

		if ( defined( 'WP_LOAD_IMPORTERS' ) && WP_LOAD_IMPORTERS ) {
			$this->import = new Import();
			block_lab()->register_component( $this->import );
		}
	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueue scripts and styles used globally in the WP Admin.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_style(
			'block-lab',
			$this->plugin->get_url( 'css/admin.css' ),
			[],
			$this->plugin->get_version()
		);
	}

	/**
	 * Redirect to the Settings screen if the license is being saved.
	 */
	public function maybe_settings_redirect() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		if ( 'block-lab-pro' === $page ) {
			wp_safe_redirect(
				add_query_arg(
					[
						'post_type' => 'block_lab',
						'page'      => 'block-lab-settings',
						'tab'       => 'license',
					],
					admin_url( 'edit.php' )
				)
			);

			die();
		}
	}
}
