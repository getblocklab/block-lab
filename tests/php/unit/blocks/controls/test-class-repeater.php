<?php
/**
 * Tests for class Repeater.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Repeater.
 */
class Test_Repeater extends \WP_UnitTestCase {

	use Testing_Helper;

	/**
	 * Instance of Repeater.
	 *
	 * @var Controls\Repeater
	 */
	public $instance;

	/**
	 * Instance of the setting.
	 *
	 * @var Controls\Control_Setting
	 */
	public $setting;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Repeater();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Repeater::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Repeater', $this->instance->label );
		$this->assertEquals( 'repeater', $this->instance->name );
		$this->assertEquals( 'object', $this->instance->type );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Repeater::register_settings()
	 */
	public function test_register_settings() {
		$expected_settings = [
			[
				'name'     => 'help',
				'label'    => 'Help Text',
				'type'     => 'text',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'min',
				'label'    => 'Minimum Rows',
				'type'     => 'number_non_negative',
				'default'  => '',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_number' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'max',
				'label'    => 'Maximum Rows',
				'type'     => 'number_non_negative',
				'default'  => '',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_number' ],
				'validate' => '',
				'value'    => null,
			],
		];

		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
	}
}
