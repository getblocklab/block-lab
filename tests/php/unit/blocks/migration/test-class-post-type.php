<?php
/**
 * Test_Migrate_Custom_Post_Type
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Migration\Post_Type;

/**
 * Class Test_Migrate_Custom_Post_Type
 *
 * @package Block_Lab
 */
class Test_Post_Type extends WP_UnitTestCase {

	/**
	 * The previous slug of the custom post type.
	 *
	 * @var string
	 */
	const PREVIOUS_POST_TYPE_SLUG = 'block_lab';

	/**
	 * The new slug of the custom post type.
	 *
	 * @todo Change this when there is a new name.
	 * @var string
	 */
	const NEW_POST_TYPE_SLUG = 'placeholder';

	/**
	 * The previous namespace of the block.
	 *
	 * @var string
	 */
	const PREVIOUS_BLOCK_NAMESPACE = 'block-lab';

	/**
	 * The new namespace of the block.
	 *
	 * @todo Change this when there is a new name.
	 * @var string
	 */
	const NEW_BLOCK_NAMESPACE = 'placeholder';

	/**
	 * The instance of the migration class.
	 *
	 * @var Post_Type
	 */
	public $instance;

	/**
	 * Initial content for a simple block.
	 *
	 * @var string
	 */
	public $simple_block_initial_content = '{"block-lab\/test-image":{"name":"test-image","title":"Test Image","excluded":[],"icon":"block_lab","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"image":{"name":"image","label":"Image","control":"image","type":"integer","order":0,"location":"editor","width":"50","help":"Here is some help text"}}}}';

	/**
	 * Expected content for a simple block.
	 *
	 * @var string
	 */
	public $simple_block_expected_content = '{"placeholder\/test-image":{"name":"test-image","title":"Test Image","excluded":[],"icon":"block_lab","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"image":{"name":"image","label":"Image","control":"image","type":"integer","order":0,"location":"editor","width":"50","help":"Here is some help text"}}}}';

	/**
	 * Initial content for a repeater block.
	 *
	 * @var string
	 */
	public $repeater_block_initial_content = '{"block-lab\/test-repeater":{"name":"test-repeater","title":"Test Repeater","excluded":[],"icon":"block_lab","category":{"slug":"layout","title":"Layout Elements","icon":null},"keywords":["repeater","panel","example"],"fields":{"repeater":{"name":"repeater","label":"Repeater","control":"repeater","type":"object","order":0,"help":"","min":1,"max":4,"sub_fields":{"textarea":{"name":"textarea","label":"Textarea","control":"textarea","type":"textarea","order":0,"location":null,"width":"","help":"This is more help text","default":"This is an example default value","placeholder":"Here is some placeholder text","maxlength":4,"number_rows":4,"new_lines":"autop","parent":"repeater"},"color":{"name":"color","label":"This is a label","control":"color","type":"string","order":1,"location":null,"width":"","help":"Here is some help text","default":"#ffffff","parent":"repeater"},"select":{"name":"select","label":"Select","control":"select","type":"string","order":2,"location":null,"width":"","help":"Here is some help text","options":[{"label":"First","value":"first"},{"label":"Second","value":"second"},{"label":"Third","value":"third"}],"default":"second","parent":"repeater"}}},"number":{"name":"number","label":"Number","control":"number","type":"integer","order":1,"location":"editor","width":"100","help":"This is example help text","default":"52","placeholder":"Enter a number"}}}}';

	/**
	 * Expected content for a repeater block.
	 *
	 * @var string
	 */
	public $repeater_block_expected_content = '{"placeholder\/test-repeater":{"name":"test-repeater","title":"Test Repeater","excluded":[],"icon":"block_lab","category":{"slug":"layout","title":"Layout Elements","icon":null},"keywords":["repeater","panel","example"],"fields":{"repeater":{"name":"repeater","label":"Repeater","control":"repeater","type":"object","order":0,"help":"","min":1,"max":4,"sub_fields":{"textarea":{"name":"textarea","label":"Textarea","control":"textarea","type":"textarea","order":0,"location":null,"width":"","help":"This is more help text","default":"This is an example default value","placeholder":"Here is some placeholder text","maxlength":4,"number_rows":4,"new_lines":"autop","parent":"repeater"},"color":{"name":"color","label":"This is a label","control":"color","type":"string","order":1,"location":null,"width":"","help":"Here is some help text","default":"#ffffff","parent":"repeater"},"select":{"name":"select","label":"Select","control":"select","type":"string","order":2,"location":null,"width":"","help":"Here is some help text","options":[{"label":"First","value":"first"},{"label":"Second","value":"second"},{"label":"Third","value":"third"}],"default":"second","parent":"repeater"}}},"number":{"name":"number","label":"Number","control":"number","type":"integer","order":1,"location":"editor","width":"100","help":"This is example help text","default":"52","placeholder":"Enter a number"}}}}';

	/**
	 * Initial content for a post block.
	 *
	 * @var string
	 */
	public $post_block_initial_content = '{"block-lab\/test-post-2":{"name":"test-post-2","title":"Test Post","excluded":[],"icon":"block_lab","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"post":{"name":"post","label":"Post","control":"post","type":"object","order":0,"location":"editor","width":"75","help":"This is a post","post_type_rest_slug":"posts"}}}}';

	/**
	 * Expected content for a post block.
	 *
	 * @var string
	 */
	public $post_block_expected_content = '{"placeholder\/test-post-2":{"name":"test-post-2","title":"Test Post","excluded":[],"icon":"block_lab","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"post":{"name":"post","label":"Post","control":"post","type":"object","order":0,"location":"editor","width":"75","help":"This is a post","post_type_rest_slug":"posts"}}}}';

	/**
	 * Sets up each test.
	 *
	 * @inheritDoc
	 */
	public function setUp() {
		$this->instance = new Post_Type( self::NEW_POST_TYPE_SLUG, self::NEW_BLOCK_NAMESPACE );
	}

	/**
	 * Creates a block post with the previous post_type.
	 *
	 * @param string $content The post content.
	 * @return WP_Post The post with the content.
	 */
	public function create_block_post( $content ) {
		return $this->factory()->post->create_and_get(
			[
				'post_type'    => self::PREVIOUS_POST_TYPE_SLUG,
				'post_content' => $content,
			]
		);
	}

	/**
	 * Test migrate_all.
	 *
	 * @covers \Block_Lab\Blocks\Migration\Post_Type::migrate_all()
	 */
	public function test_migrate_all() {
		$initial_block_content = [
			$this->simple_block_initial_content,
			$this->repeater_block_initial_content,
			$this->post_block_initial_content,
		];

		$expected_block_content = [
			$this->simple_block_expected_content,
			$this->repeater_block_expected_content,
			$this->post_block_expected_content,
		];

		foreach ( $initial_block_content as $content ) {
			$this->create_block_post( $content );
		}

		$this->instance->migrate_all();

		$query = new WP_Query( [ 'post_type' => self::NEW_POST_TYPE_SLUG ] );
		foreach ( $query->posts as $post ) {
			$this->assertTrue( in_array( $post->post_content, $expected_block_content, true ) );
		}
	}

	/**
	 * Gets the test data for test_migrate_single().
	 *
	 * @return array The test data.
	 */
	public function get_data_migrate_single() {
		return [
			'not_json'                        => [
				'{"malformed_json":{',
				null,
				self::PREVIOUS_POST_TYPE_SLUG,
				false,
			],
			'simple_string_in_content'        => [
				'This is only a string and should not be converted',
				null,
				self::PREVIOUS_POST_TYPE_SLUG,
				false,
			],
			'simple_block'                    => [
				$this->simple_block_initial_content,
				$this->simple_block_expected_content,
				self::NEW_POST_TYPE_SLUG,
			],
			'simple_block_wrapped_in_space'   => [
				'  ' . $this->simple_block_initial_content . ' ',
				$this->simple_block_expected_content,
				self::NEW_POST_TYPE_SLUG,
			],
			'repeater_block'                  => [
				$this->repeater_block_initial_content,
				$this->repeater_block_expected_content,
				self::NEW_POST_TYPE_SLUG,
			],
			'repeater_block_wrapped_in_space' => [
				'  ' . $this->repeater_block_initial_content . ' ',
				$this->repeater_block_expected_content,
				self::NEW_POST_TYPE_SLUG,
			],
			'post_block'                      => [
				$this->post_block_initial_content,
				$this->post_block_expected_content,
				self::NEW_POST_TYPE_SLUG,
			],
		];
	}

	/**
	 * Test migrate_single.
	 *
	 * @dataProvider get_data_migrate_single
	 * @covers \Block_Lab\Blocks\Migration\Post_Type::migrate_single()
	 *
	 * @param string $initial_post_content  Initial post_content.
	 * @param string $expected_post_content Expected post_content of the new post.
	 * @param string $expected_post_type    Expected post_type of the new post.
	 * @param bool   $expected_return       Expected return value of the method.
	 */
	public function test_migrate_single( $initial_post_content, $expected_post_content, $expected_post_type, $expected_return = true ) {
		if ( null === $expected_post_content ) {
			$expected_post_content = $initial_post_content;
		}

		$post = $this->create_block_post( $initial_post_content );
		$this->assertEquals( $expected_return, $this->instance->migrate_single( $post ) );
		$new_post = get_post( $post->ID );
		$this->assertEquals( $expected_post_content, $new_post->post_content );
		$this->assertEquals( $expected_post_type, $new_post->post_type );
	}
}
