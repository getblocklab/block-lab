<?php
/**
 * Tests for class Field.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Tests for class Field.
 */
class Test_Field extends \WP_UnitTestCase {

	/**
	 * A mock config array for the field.
	 *
	 * @var array
	 */
	public $config = array(
		'name'     => 'foo',
		'label'    => 'Foo',
		'control'  => 'number',
		'type'     => 'integer',
		'order'    => 1,
		'settings' => array(
			'custom'     => 'Custom Setting',
			'sub_fields' => array(
				'baz' => array(
					'name'    => 'baz',
					'label'   => 'Baz',
					'control' => 'text',
					'type'    => 'string',
					'order'   => 0,
					'parent'  => 'foo'
				)
			)
		)
	);

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->instance = new Blocks\Field( array() );
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Field::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( '', $this->instance->name );
		$this->assertEquals( '', $this->instance->label );
		$this->assertEquals( 'text', $this->instance->control );
		$this->assertEquals( 'string', $this->instance->type );
		$this->assertEquals( 0, $this->instance->order );
		$this->assertEquals( array(), $this->instance->settings );
	}

	/**
	 * Test from_array.
	 *
	 * @covers \Block_Lab\Blocks\Field::from_array()
	 */
	public function test_from_array() {
		$this->instance->from_array( $this->config );

		$this->assertEquals( 'foo', $this->instance->name );
		$this->assertEquals( 'Foo', $this->instance->label );
		$this->assertEquals( 'number', $this->instance->control );
		$this->assertEquals( 'integer', $this->instance->type );
		$this->assertEquals( 1, $this->instance->order );
		$this->assertAttributeNotEmpty( 'settings', $this->instance );
		$this->assertArrayHasKey( 'custom', $this->instance->settings );
		$this->assertEquals( 'Custom Setting', $this->instance->settings['custom'] );
		$this->assertArrayHasKey( 'sub_fields', $this->instance->settings );
		$this->assertArrayHasKey( 'baz', $this->instance->settings['sub_fields'] );
		$this->assertEquals( 'baz', $this->instance->settings['sub_fields']['baz']->name );
		$this->assertEquals( 'Baz', $this->instance->settings['sub_fields']['baz']->label );
		$this->assertAttributeNotEmpty( 'settings', $this->instance->settings['sub_fields']['baz'] );
	}

	/**
	 * Test to_array.
	 *
	 * @covers \Block_Lab\Blocks\Field::to_array()
	 */
	public function test_to_array() {
		$this->instance->from_array( $this->config );
		$config = $this->instance->to_array();

		$this->assertEquals( 'foo', $config['name'] );
		$this->assertEquals( 'Foo', $config['label'] );
		$this->assertEquals( 'number', $config['control'] );
		$this->assertEquals( 'integer', $config['type'] );
		$this->assertEquals( 1, $config['order'] );
		$this->assertArrayHasKey( 'custom', $config );
		$this->assertArrayNotHasKey( 'settings', $config );
		$this->assertEquals( 'Custom Setting', $config['custom'] );
		$this->assertArrayHasKey( 'sub_fields', $config );
		$this->assertArrayHasKey( 'baz', $config['sub_fields'] );
		$this->assertArrayNotHasKey( 'settings', $config['sub_fields']['baz'] );
		$this->assertArrayHasKey( 'parent', $config['sub_fields']['baz'] );
		$this->assertEquals( 'foo', $config['sub_fields']['baz']['parent'] );
	}
}
