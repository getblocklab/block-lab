<?php
/**
 * Tests for class Classic_Text.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Controls;

/**
 * Tests for class Classic_Text.
 */
class Test_Classic_Text extends \WP_UnitTestCase {

	use Testing_Helper;

	/**
	 * Instance of Classic_Text.
	 *
	 * @var Controls\Classic_Text
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Controls\Classic_Text();
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Classic_Text::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'Classic Text', $this->instance->label );
		$this->assertEquals( 'classic_text', $this->instance->name );
	}

	/**
	 * Test register_settings.
	 *
	 * @covers \Block_Lab\Blocks\Controls\Classic_Text::register_settings()
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
				'name'     => 'default',
				'label'    => 'Default Value',
				'type'     => 'text',
				'default'  => '',
				'help'     => '',
				'sanitize' => 'sanitize_text_field',
				'validate' => '',
				'value'    => null,
			],
		];

		$this->assert_correct_settings( $expected_settings, $this->instance->settings );
	}
}
