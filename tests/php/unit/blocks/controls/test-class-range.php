<?php
/**
 * Tests for class Range.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Range.
 */
class Test_Range extends \WP_UnitTestCase {

	use Testing_Helper;

	/**
	 * Instance of Range.
	 *
	 * @var Controls\Range
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Range();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Range::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Range', $this->instance->label );
		$this->assertEquals( 'range', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * The parent constructor calls register_settings(), so there's no need to call it again here.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Range::register_settings()
	 */
	public function test_register_settings() {
		$expected_settings = [
			[
				'name'     => 'location',
				'label'    => 'Field Location',
				'type'     => 'location',
				'default'  => 'editor',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_location' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'width',
				'label'    => 'Field Width',
				'type'     => 'width',
				'default'  => '100',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			],
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
				'label'    => 'Minimum Value',
				'type'     => 'number',
				'default'  => '',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_number' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'max',
				'label'    => 'Maximum Value',
				'type'     => 'number',
				'default'  => '',
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_number' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'step',
				'label'    => 'Step Size',
				'type'     => 'number_non_negative',
				'default'  => 1,
				'help'     => '',
				'sanitize' => [ $this->instance, 'sanitize_number' ],
				'validate' => '',
				'value'    => null,
			],
			[
				'name'     => 'default',
				'label'    => 'Default Value',
				'type'     => 'number',
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
