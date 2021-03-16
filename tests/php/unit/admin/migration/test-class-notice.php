<?php
/**
 * Tests for class Notice.
 *
 * @package Block_Lab
 */

use Block_Lab\Admin\Migration\Notice;
use Brain\Monkey;
use function Brain\Monkey\Functions\expect;

/**
 * Tests for class Notice.
 */
class Test_Notice extends \WP_UnitTestCase {

	use Testing_Helper;

	/**
	 * Instance of Notice.
	 *
	 * @var Notice
	 */
	public $instance;

	/**
	 * Set up each test.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();
		$this->instance = new Notice();
		$this->instance->set_plugin( block_lab() );
	}

	/**
	 * Tear down after each test.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test register_hooks.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::register_hooks()
	 */
	public function test_register_hooks() {
		$this->instance->register_hooks();
		$this->assertEquals( 10, has_action( 'admin_notices', [ $this->instance, 'render_migration_notice' ] ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $this->instance, 'enqueue_assets' ] ) );
		$this->assertEquals( 10, has_action( 'wp_ajax_bl_dismiss_migration_notice', [ $this->instance, 'ajax_handler_migration_notice' ] ) );
	}

	/**
	 * Test render_migration_notice.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::render_migration_notice()
	 */
	public function test_render_migration_notice() {
		$this->give_user_permissions();
		$mock_current_screen       = new stdClass();
		$mock_current_screen->base = 'block_lab_page_block-lab-settings';

		expect( 'get_current_screen' )
			->once()
			->andReturn( $mock_current_screen );

		ob_start();
		$this->instance->render_migration_notice();

		$this->assertContains(
			'For a much easier, completely compatible editor, migrate now to the new home of custom blocks: <strong>Genesis Custom Blocks</strong>.',
			ob_get_clean()
		);
	}

	/**
	 * Test enqueue_assets.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::enqueue_assets()
	 */
	public function test_enqueue_assets() {
		$this->give_user_permissions();
		$mock_current_screen       = new stdClass();
		$mock_current_screen->base = 'block_lab_page_block-lab-settings';

		expect( 'get_current_screen' )
			->once()
			->andReturn( $mock_current_screen );

		$this->instance->enqueue_assets();
		$this->assertTrue( wp_style_is( 'block-lab-migration-notice-style' ) );
		$this->assertTrue( wp_script_is( 'block-lab-migration-notice-script' ) );
	}

	/**
	 * Test ajax_handler_migration_notice.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::ajax_handler_migration_notice()
	 */
	public function test_ajax_handler_migration_notice() {
		$this->give_user_permissions();
		add_filter( 'wp_doing_ajax', '__return_true' );
		add_filter( 'wp_die_ajax_handler', '__return_false' );

		expect( 'check_ajax_referer' )
			->once()
			->with(
				'bl-migration-nonce',
				'bl-migration-nonce-name'
			)
			->andReturn( true );

		try {
			$this->instance->ajax_handler_migration_notice();
		} catch ( Exception $e ) {
			$exception = $e;
		}

		unset( $exception );
		$this->assertEquals( 'dismissed', get_user_meta( get_current_user_id(), 'block_lab_show_migration_notice_with_new_features', true ) );
	}

	/**
	 * Test migration_notice when on a page where it shouldn't appear.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::should_display_migration_notice()
	 */
	public function test_migration_notice_wrong_page() {
		$this->assertFalse( $this->instance->should_display_migration_notice() );
	}

	/**
	 * Test migration_notice when the user has dismissed it.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::render_migration_notice()
	 */
	public function test_migration_notice_dismissed() {
		$this->give_user_permissions();
		$mock_current_screen       = new stdClass();
		$mock_current_screen->base = 'block_lab_page_block-lab-settings';

		expect( 'get_current_screen' )
			->andReturn( $mock_current_screen );

		update_user_meta( get_current_user_id(), Notice::NOTICE_USER_META_KEY, Notice::NOTICE_DISMISSED_META_VALUE );

		$this->assertFalse( $this->instance->should_display_migration_notice() );
	}

	/**
	 * Test migration_notice on the Block Lab settings page.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::render_migration_notice()
	 */
	public function test_migration_notice_on_settings_page() {
		$this->give_user_permissions();
		$mock_current_screen       = new stdClass();
		$mock_current_screen->base = 'block_lab_page_block-lab-settings';

		expect( 'get_current_screen' )
			->once()
			->andReturn( $mock_current_screen );

		$this->assertTrue( $this->instance->should_display_migration_notice() );
	}

	/**
	 * Test migration_notice on the Content Blocks page.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::should_display_migration_notice()
	 */
	public function test_migration_notice_on_content_blocks_page() {
		$this->give_user_permissions();
		$mock_current_screen            = new stdClass();
		$mock_current_screen->post_type = 'block_lab';
		$mock_current_screen->base      = 'edit';

		expect( 'get_current_screen' )
			->once()
			->andReturn( $mock_current_screen );

		$this->assertTrue( $this->instance->should_display_migration_notice() );
	}

	/**
	 * Test migration_notice on the plugins page.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::should_display_migration_notice()
	 */
	public function test_migration_notice_on_plugins_page() {
		$this->give_user_permissions();
		$mock_current_screen       = new stdClass();
		$mock_current_screen->base = 'plugins';

		expect( 'get_current_screen' )
			->once()
			->andReturn( $mock_current_screen );

		$this->assertTrue( $this->instance->should_display_migration_notice() );
	}

	/**
	 * Test migration_notice on the plugins page.
	 *
	 * @covers \Block_Lab\Admin\Migration\Notice::should_display_migration_notice()
	 */
	public function test_migration_notice_on_dashboard() {
		$this->give_user_permissions();
		$mock_current_screen       = new stdClass();
		$mock_current_screen->base = 'dashboard';

		expect( 'get_current_screen' )
			->once()
			->andReturn( $mock_current_screen );

		$this->assertTrue( $this->instance->should_display_migration_notice() );
	}

	/**
	 * Gives the user permissions to see the notice.
	 */
	public function give_user_permissions() {
		$user_id = $this->factory()->user->create( [ 'role' => 'administrator' ] );
		if ( is_multisite() ) {
			grant_super_admin( $user_id );
		}

		wp_set_current_user( $user_id );
	}
}
