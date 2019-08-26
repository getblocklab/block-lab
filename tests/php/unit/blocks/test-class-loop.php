<?php
/**
 * Tests for class Loop.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Tests for class Loop.
 */
class Test_Loop extends \WP_UnitTestCase {

	/**
	 * The instance to test.
	 *
	 * @var Blocks\Loop
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->instance = new Blocks\Loop();
	}

	/**
	 * Test set_active.
	 *
	 * @covers \Block_Lab\Blocks\Loop::set_active()
	 */
	public function test_set_active() {
		$this->assertEquals( null, $this->instance->active );

		$loop_name = 'my-repeater';
		$this->instance->set_active( $loop_name );
		$this->assertEquals( $loop_name, $this->instance->active );

		$new_loop_name = 'different-repeater';
		$this->instance->set_active( $new_loop_name );

		// Calling set_active() should overwrite the previous active loop value.
		$this->assertEquals( $new_loop_name, $this->instance->active );
	}

	/**
	 * Test get_row.
	 *
	 * @covers \Block_Lab\Blocks\Loop::get_row()
	 */
	public function test_get_row() {
		// If there is no loop set, this should return false.
		$this->assertFalse( $this->instance->get_row() );
		$this->assertFalse( $this->instance->get_row( 'random-row-name' ) );

		// When the row has a pointer, this should return it.
		$row_name                           = 'example-row-name';
		$pointer                            = 2;
		$this->instance->loops[ $row_name ] = $pointer;
		$this->assertEquals( $pointer, $this->instance->get_row( $row_name ) );

		// If there is an active row, it shouldn't be necessary to pass that row to get_row() as an argument.
		$this->instance->active = $row_name;
		$this->assertEquals( $pointer, $this->instance->get_row() );

		// If the passed row name doesn't exist, this should return false.
		$nonexistent_row_name = 'random-row';
		$this->assertEquals( false, $this->instance->get_row( $nonexistent_row_name ) );
	}

	/**
	 * Test increment.
	 *
	 * @covers \Block_Lab\Blocks\Loop::increment()
	 */
	public function test_increment() {
		// Calling increment() for the first time for a row should return 0.
		$row_name = 'example-row-name';
		$this->assertEquals( 0, $this->instance->increment( $row_name ) );

		// Calling it again should increment it by 1.
		$this->assertEquals( 1, $this->instance->increment( $row_name ) );
		$this->assertEquals( 2, $this->instance->increment( $row_name ) );

		// If the row name is the active row, it shouldn't be necessary to pass it to increment() as an argument.
		$this->instance->active = $row_name;
		$this->assertEquals( 3, $this->instance->increment() );
	}

	/**
	 * Test reset.
	 *
	 * @covers \Block_Lab\Blocks\Loop::reset()
	 */
	public function test_reset() {
		$row_name                           = 'example-row-name';
		$pointer                            = 2;
		$this->instance->loops[ $row_name ] = $pointer;

		$this->instance->reset( $row_name );
		$this->assertFalse( isset( $this->instance->loops[ $row_name ] ) );

		$this->instance->loops[ $row_name ] = $pointer;
		$this->instance->active             = $row_name;

		// If the row is the active row, it should be necessary to pass it to reset() as an argument.
		$this->instance->reset();
		$this->assertFalse( isset( $this->instance->loops[ $row_name ] ) );
	}
}
