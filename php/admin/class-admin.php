<?php
/**
 * WP Admin resources.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;

/**
 * Class Admin
 */
class Admin extends Component_Abstract {

	/**
	 * Plugin settings.
	 *
	 * @var Settings
	 */
	public $settings;

	/**
	 * Plugin license.
	 *
	 * @var License
	 */
	public $license;

	/**
	 * Plugin upgrade.
	 *
	 * @var Upgrade
	 */
	public $upgrade;

	/**
	 * JSON import.
	 *
	 * @var Import
	 */
	public $import;

	/**
	 * Initialise the Admin component.
	 */
	public function init() {
		$this->settings = new Settings();
		block_lab()->register_component( $this->settings );

		$this->license = new License();
		block_lab()->register_component( $this->license );

		$show_pro_nag = apply_filters( 'block_lab_show_pro_nag', true );
		if ( $show_pro_nag && ! block_lab()->is_pro() ) {
			$this->upgrade = new Upgrade();
			block_lab()->register_component( $this->upgrade );
		}

		if ( block_lab()->is_pro() ) {
			if ( defined( 'WP_LOAD_IMPORTERS' ) && WP_LOAD_IMPORTERS ) {
				$this->import = new Import();
				block_lab()->register_component( $this->import );
			}
		}
	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
			array(),
			$this->plugin->get_version()
		);
	}
}
