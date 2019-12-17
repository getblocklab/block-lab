<?php
/**
 * Tests for class Block.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Tests for class Block.
 */
class Test_Block extends \WP_UnitTestCase {

	/**
	 * The instance to test.
	 *
	 * @var Blocks\Block
	 */
	public $instance;

	/**
	 * A mock JSON blob for the block.
	 *
	 * @var string
	 */
	const JSON = '
	{
		"block-lab\\/simple-test-block": {
			"name": "simple-test-block",
			"title": "Simple Test Block",
			"icon": "block_lab",
			"category": "common",
			"keywords": [
				"keywords",
				"go",
				"here"
			],
			"fields": {
				"heading": {
					"name": "heading",
					"label": "Heading",
					"control": "text",
					"type": "string",
					"location": "editor",
					"order": 0,
					"help": "",
					"default": "",
					"placeholder": "",
					"maxlength": null
				},
				"content": {
					"name": "content",
					"label": "Content",
					"control": "textarea",
					"type": "textarea",
					"location": "editor",
					"order": 1,
					"help": "",
					"default": "",
					"placeholder": "",
					"maxlength": null,
					"number_rows": 4,
					"new_lines": "autop"
				},
				"parent": {
					"name": "parent",
					"label": "Parent",
					"control": "repeater",
					"type": "array",
					"order": 0,
					"help": "",
					"sub_fields": {
						"child": {
							"name": "child",
							"label": "Child",
							"control": "text",
							"type": "string",
							"order": 0,
							"location": "editor",
							"help": "",
							"default": "",
							"placeholder": "",
							"maxlength": null,
							"parent": "parent"
						}
					}
				}
			}
		}
	}
	';

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$post = $this->factory()->post->create(
			[
				'post_title' => 'Simple Test Block',
				'post_name'  => 'simple-test-block',
				'post_type'  => 'block_lab',
			]
		);

		$this->instance = new Blocks\Block( $post );
	}

	/**
	 * Test __construct.
	 *
	 * @covers \Block_Lab\Blocks\Block::__construct()
	 */
	public function test_construct() {
		$this->assertEquals( 'simple-test-block', $this->instance->name );
	}

	/**
	 * Test from_json.
	 *
	 * @covers \Block_Lab\Blocks\Block::from_json()
	 */
	public function test_from_json() {
		$this->instance->from_json( self::JSON );

		// Check all the base attributes.
		$this->assertEquals( 'Simple Test Block', $this->instance->title );
		$this->assertEquals( 'block_lab', $this->instance->icon );
		$this->assertEquals(
			[
				'icon'  => null,
				'slug'  => 'common',
				'title' => 'Common',
			],
			$this->instance->category
		);
		$this->assertEquals( [ 'keywords', 'go', 'here' ], $this->instance->keywords );

		// Check that we've got three fields.
		$this->assertCount( 3, $this->instance->fields );
		$this->assertArrayHasKey( 'heading', $this->instance->fields );
		$this->assertArrayHasKey( 'content', $this->instance->fields );
		$this->assertArrayHasKey( 'parent', $this->instance->fields );

		// Check that the repeater works as expected.
		$this->assertAttributeNotEmpty( 'settings', $this->instance->fields['parent'] );
		$this->assertArrayHasKey( 'sub_fields', $this->instance->fields['parent']->settings );
		$this->assertArrayHasKey( 'child', $this->instance->fields['parent']->settings['sub_fields'] );
		$this->assertEquals( 'child', $this->instance->fields['parent']->settings['sub_fields']['child']->name );
		$this->assertAttributeNotEmpty( 'settings', $this->instance->fields['parent']->settings['sub_fields']['child'] );
		$this->assertArrayHasKey( 'parent', $this->instance->fields['parent']->settings['sub_fields']['child']->settings );
		$this->assertEquals( 'parent', $this->instance->fields['parent']->settings['sub_fields']['child']->settings['parent'] );
	}

	/**
	 * Test to_json.
	 *
	 * @covers \Block_Lab\Blocks\Block::to_json()
	 */
	public function test_to_json() {
		$this->instance->from_json( self::JSON );
		$json = $this->instance->to_json();

		$decoded = json_decode( $json, true );
		$this->assertArrayHasKey( 'block-lab/simple-test-block', $decoded );

		// Check all the base attributes.
		$block = $decoded['block-lab/simple-test-block'];
		$this->assertArrayHasKey( 'name', $block );
		$this->assertArrayHasKey( 'title', $block );
		$this->assertArrayHasKey( 'icon', $block );
		$this->assertArrayHasKey( 'category', $block );
		$this->assertArrayHasKey( 'keywords', $block );
		$this->assertArrayHasKey( 'fields', $block );

		// Check that we've got three fields.
		$this->assertCount( 3, $block['fields'] );
		$this->assertArrayHasKey( 'heading', $block['fields'] );
		$this->assertArrayHasKey( 'content', $block['fields'] );
		$this->assertArrayHasKey( 'parent', $block['fields'] );

		// Check that the repeater works as expected.
		$this->assertArrayHasKey( 'sub_fields', $block['fields']['parent'] );
		$this->assertArrayHasKey( 'child', $block['fields']['parent']['sub_fields'] );
		$this->assertArrayHasKey( 'name', $block['fields']['parent']['sub_fields']['child'] );
		$this->assertEquals( 'child', $block['fields']['parent']['sub_fields']['child']['name'] );
		$this->assertEquals( 'parent', $block['fields']['parent']['sub_fields']['child']['parent'] );
	}
}
