<?php
/**
 * Enable and validate Pro version licensing.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Admin;

use Block_Lab\Component_Abstract;

/**
 * Class Migration
 */
class Migration extends Component_Abstract {

	/**
	 * The action of the migration notice nonce.
	 *
	 * @var string
	 */
	const NOTICE_NONCE_ACTION = 'bl-migration-nonce';

	/**
	 * The name of the migration notice nonce.
	 *
	 * @var string
	 */
	const NOTICE_NONCE_NAME = 'bl-migration-nonce-name';

	/**
	 * The slug of the stylesheet for the migration notice.
	 *
	 * @var string
	 */
	const NOTICE_STYLE_SLUG = 'block-lab-migration-notice-style';

	/**
	 * The slug of the stylesheet for the migration notice.
	 *
	 * @var string
	 */
	const NOTICE_SCRIPT_SLUG = 'block-lab-migration-notice-script';

	/**
	 * Adds an action for the notice.
	 */
	public function register_hooks() {
		add_action( 'admin_notices', [ $this, 'render_migration_notice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Outputs the migration notice if this is on the right page and the user has the right permission.
	 */
	public function render_migration_notice() {
		if ( ! $this->should_display_migration_notice() ) {
			return;
		}

		$migration_url = add_query_arg(
			[
				'post_type' => block_lab()->get_slug(),
				'page'      => 'block-lab-migration',
			],
			admin_url( 'edit.php' )
		);

		?>
		<div class="bl-migration-notice notice notice-info">
			<?php wp_nonce_field( self::NOTICE_NONCE_ACTION, self::NOTICE_NONCE_NAME, false ); ?>
			<div class="bl-migration-copy">
				<p>
					<?php
					printf(
						/* translators: %1$s: the plugin name */
						esc_html__( 'The Block Lab team have moved. For future updates and improvements, migrate now to the new home of custom blocks: %1$s', 'block-lab' ),
						'<strong>Genesis Custom Blocks.</strong>'
					);
					?>
				</p>
				<p>
					<?php
					printf(
						'<a target="_blank" href="https://getblocklab.com/docs/genesis-custom-blocks">%1$s</a>',
						esc_html__( 'Learn more', 'block-lab' )
					);
					?>
				</p>
			</div>
			<a href="#" class="bl-notice-option">
				<?php esc_html_e( 'Not now', 'block-lab' ); ?>
			</a>
			<a href="<?php echo esc_url( $migration_url ); ?>" class="bl-notice-option">
				<?php esc_html_e( 'Migrate', 'block-lab' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Enqueues the migration notice assets.
	 */
	public function enqueue_assets() {
		if ( ! $this->should_display_migration_notice() ) {
			return;
		}

		wp_enqueue_style(
			self::NOTICE_STYLE_SLUG,
			$this->plugin->get_url( 'css/admin.migration-notice.css' ),
			[],
			$this->plugin->get_version()
		);

		wp_enqueue_script(
			self::NOTICE_SCRIPT_SLUG,
			$this->plugin->get_url( 'js/admin.migration-notice.js' ),
			[],
			$this->plugin->get_version(),
			true
		);
	}

	/**
	 * Gets whether the migration notice should display.
	 *
	 * This should display on Block Lab > Content Blocks,
	 * /wp-admin/plugins.php, the Dashboard, and Block Lab > Settings.
	 *
	 * @return bool Whether the migration notice should display.
	 */
	public function should_display_migration_notice() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		$screen = get_current_screen();
		return (
			( isset( $screen->base, $screen->post_type ) && 'edit' === $screen->base && 'block_lab' === $screen->post_type )
			||
			( isset( $screen->base ) && in_array( $screen->base, [ 'plugins', 'dashboard', 'block_lab_page_block-lab-settings' ], true ) )
		);
	}
}
