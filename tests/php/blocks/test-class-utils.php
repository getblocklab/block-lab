<?php
/**
 * Tests for class Utils.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Tests for class Utils.
 */
class Test_Utils extends \WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Blocks\Utils();
	}

	/**
	 * Test get_block_lab_template_locations.
	 *
	 * @covers Block_Lab\Blocks\Utils::get_block_lab_template_locations()
	 */
	public function test_get_block_lab_template_locations() {
		$name = 'foo-baz';
		$this->assertEquals(
			array(
				"blocks/foo-baz/block.php",
				"blocks/block-foo-baz.php",
				"blocks/block.php",
			),
			$this->instance->get_block_lab_template_locations( $name )
		);

		$name = 'example';
		$type = 'another-type';
		$this->assertEquals(
			array(
				"blocks/example/another-type.php",
				"blocks/another-type-example.php",
				"blocks/another-type.php",
			),
			$this->instance->get_block_lab_template_locations( $name, $type )
		);
	}

	/**
	 * Test get_block_lab_stylesheet_locations.
	 *
	 * @covers Block_Lab\Blocks\Utils::get_block_lab_stylesheet_locations()
	 */
	public function test_get_block_lab_stylesheet_locations() {
		$name = 'foo-baz';
		$this->assertEquals(
			array(
				"blocks/foo-baz/block.css",
				"blocks/css/block-foo-baz.css",
				"blocks/block-foo-baz.css",
			),
			$this->instance->get_block_lab_stylesheet_locations( $name )
		);

		$name = 'example';
		$type = 'another-type';
		$this->assertEquals(
			array(
				"blocks/example/another-type.css",
				"blocks/css/another-type-example.css",
				"blocks/another-type-example.css",
			),
			$this->instance->get_block_lab_stylesheet_locations( $name, $type )
		);
	}
}
