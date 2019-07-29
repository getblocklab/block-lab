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
		add_action( 'current_screen', array( $this, 'admin_notices' ) );
	}

	/**
	 * Runs during plugin activation.
	 */
	public function plugin_activation() {
		$this->add_dummy_data();
		$this->prepare_welcome_notice();
	}

	/**
	 * Prepare onboarding notices.
	 */
	public function admin_notices() {
		$example_post_id = get_option( 'block_lab_example_post_id' );

		if ( ! $example_post_id ) {
			return;
		}

		if ( 'draft' !== get_post_status( $example_post_id ) ) {
			return;
		}

		$screen = get_current_screen();
		$slug   = $this->plugin->block_post->slug;

		if ( 'plugins' === $screen->id && 'true' === get_transient( 'block_lab_show_welcome' ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_notices', array( $this, 'show_welcome_notice' ) );
		}

		if ( "edit-$slug" === $screen->id ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_notices', array( $this, 'show_edit_block_notice' ) );
		}
	}

	/**
	 * Prepare the welcome notice on plugin activation.
	 *
	 * We can't hook into admin_notices at this point, so instead we set a short
	 * transient, and check that transient during the next page load.
	 */
	public function prepare_welcome_notice() {
		set_transient( 'block_lab_show_welcome', 'true', 1 );
	}

	/**
	 * Enqueue scripts and styles used by the onboarding screens.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_style(
			'block-lab-onboarding-css',
			$this->plugin->get_url( 'css/admin.onboarding.css' ),
			array(),
			$this->plugin->get_version()
		);
	}

	/**
	 * Render the welcome message.
	 */
	public function show_welcome_notice() {
		$example_post_id = get_option( 'block_lab_example_post_id' );
		if ( ! $example_post_id ) {
			return;
		}
		?>
		<div class="block-lab-welcome block-lab-notice notice is-dismissible">
			<h2><?php esc_html_e( '🖖 Welcome, traveller!', 'block-lab' ); ?></h2>
			<p class="intro"><?php esc_html_e( 'Block Lab makes it super easy to build custom blocks for the WordPress editor.', 'block-lab' ); ?></p>
			<p><strong><?php esc_html_e( 'Want to see how it\'s done?', 'block-lab' ); ?></strong> <?php esc_html_e( 'Here\'s one I prepared earlier.', 'block-lab' ); ?></p>
			<?php
			edit_post_link(
				__( 'Let\'s get started!', 'block-lab' ),
				'',
				'',
				$example_post_id,
				'button button--white button_cta'
			);
			?>
			<p class="ps"><?php esc_html_e( 'P.S. We don\'t like to nag. This message won\'t be shown again.', 'block-lab' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render the edit your first block message.
	 */
	public function show_edit_block_notice() {
		$example_post_id = get_option( 'block_lab_example_post_id' );
		if ( ! $example_post_id ) {
			return;
		}
		?>
		<div class="block-lab-edit-block block-lab-notice notice notice-large">
			<h2>Ready to begin?</h2>
			<p class="intro">We created this <strong>Example Block</strong> to show you just how easy it is to get started.</p>
			<p>You can <strong>Edit</strong> the block to learn more, or just <a href="<?php echo get_delete_post_link( $example_post_id ); ?>" class="trash">Trash</a> it to dismiss this message.</p>
			<span class="pointer">☝️</span>
		</div>
		<?php
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

		$categories = get_block_categories( the_post() );

		$example_post_id = wp_insert_post(
			array(
				'post_title'   => __( 'Example Block', 'block-lab' ),
				'post_name'    => 'example-block',
				'post_status'  => 'draft',
				'post_type'    => block_lab()->block_post->slug,
				'post_content' => wp_json_encode(
					array(
						'block-lab\/example-block' => array(
							'name'     => 'example-block',
							'title'    => __( 'Example Block', 'block-lab' ),
							'icon'     => 'block_lab',
							'category' => isset( $categories[0] ) ? $categories[0] : array(),
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

		update_option( 'block_lab_example_post_id', $example_post_id );
	}
}
