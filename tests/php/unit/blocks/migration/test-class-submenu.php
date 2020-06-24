<?php
/**
 * Test_Submenu
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Migration\Submenu;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Class Test_Submenu
 *
 * @package Block_Lab
 */
class Test_Submenu extends WP_UnitTestCase {

	use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	/**
	 * The instance to test.
	 *
	 * @var Submenu
	 */
	public $instance;

	/**
	 * Sets up each test.
	 *
	 * @inheritDoc
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();
		$this->instance = new Submenu();
	}

	/**
	 * Tears down after each test.
	 *
	 * @inheritDoc
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test init.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::init()
	 */
	public function test_init() {
		$this->instance->init();
		$this->assertEquals( 10, has_action( 'admin_menu', [ $this->instance, 'add_submenu_page' ] ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $this->instance, 'enqueue_scripts' ] ) );
	}

	/**
	 * Test add_submenu_page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::add_submenu_page()
	 */
	public function test_add_submenu_pages() {
		Functions\expect( 'add_submenu_page' )
			->once()
			->with(
				'edit.php?post_type=block_lab',
				'Migrate to Genesis Custom Blocks',
				'Migrate',
				'manage_options',
				'block-lab-migration',
				[ $this->instance, 'render_page' ]
			);

		$this->instance->add_submenu_page();
	}

	/**
	 * Test enqueue_scripts when not on a page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::enqueue_scripts()
	 */
	public function test_enqueue_scripts_not_on_page() {
		$this->instance->enqueue_scripts();
		$this->assertFalse( wp_style_is( 'block-lab-migration' ) );
		$this->assertFalse( wp_script_is( 'block-lab-migration' ) );
	}

	/**
	 * Test enqueue_scripts when on the wrong page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::enqueue_scripts()
	 */
	public function test_enqueue_scripts_wrong_page() {
		Monkey\Functions\expect( 'filter_input' )
			->once()
			->with(
				INPUT_GET,
				'page',
				FILTER_SANITIZE_STRING
			)
			->andReturn( 'wrong-page' );

		$this->instance->enqueue_scripts();
		$this->assertFalse( wp_style_is( 'block-lab-migration' ) );
		$this->assertFalse( wp_script_is( 'block-lab-migration' ) );
	}

	/**
	 * Test enqueue_scripts on the right page.
	 *
	 * @covers Block_Lab\Blocks\Migration\Submenu::enqueue_scripts()
	 */
	public function test_enqueue_scripts_right_page() {
		Monkey\Functions\expect( 'filter_input' )
			->once()
			->with(
				INPUT_GET,
				'page',
				FILTER_SANITIZE_STRING
			)
			->andReturn( 'block-lab-migration' );

		$this->instance->enqueue_scripts();
		$this->assertTrue( wp_style_is( 'block-lab-migration' ) );
		$this->assertTrue( wp_script_is( 'block-lab-migration' ) );
	}
}
