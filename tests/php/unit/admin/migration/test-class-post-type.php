<?php
/**
 * Test_Post_Type
 *
 * @package Block_Lab
 */

use Block_Lab\Admin\Migration\Post_Type;

/**
 * Class Test_Post_Type
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
	 * @var string
	 */
	const NEW_POST_TYPE_SLUG = 'genesis_custom_block';

	/**
	 * The previous namespace of the block.
	 *
	 * @var string
	 */
	const PREVIOUS_BLOCK_NAMESPACE = 'block-lab';

	/**
	 * The new namespace of the block.
	 *
	 * @var string
	 */
	const NEW_BLOCK_NAMESPACE = 'genesis-custom-blocks';

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
	public $simple_block_expected_content = '{"genesis-custom-blocks\/test-image":{"name":"test-image","title":"Test Image","excluded":[],"icon":"genesis_custom_blocks","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"image":{"name":"image","label":"Image","control":"image","type":"integer","order":0,"location":"editor","width":"50","help":"Here is some help text"}}}}';

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
	public $repeater_block_expected_content = '{"genesis-custom-blocks\/test-repeater":{"name":"test-repeater","title":"Test Repeater","excluded":[],"icon":"genesis_custom_blocks","category":{"slug":"layout","title":"Layout Elements","icon":null},"keywords":["repeater","panel","example"],"fields":{"repeater":{"name":"repeater","label":"Repeater","control":"repeater","type":"object","order":0,"help":"","min":1,"max":4,"sub_fields":{"textarea":{"name":"textarea","label":"Textarea","control":"textarea","type":"textarea","order":0,"location":null,"width":"","help":"This is more help text","default":"This is an example default value","placeholder":"Here is some placeholder text","maxlength":4,"number_rows":4,"new_lines":"autop","parent":"repeater"},"color":{"name":"color","label":"This is a label","control":"color","type":"string","order":1,"location":null,"width":"","help":"Here is some help text","default":"#ffffff","parent":"repeater"},"select":{"name":"select","label":"Select","control":"select","type":"string","order":2,"location":null,"width":"","help":"Here is some help text","options":[{"label":"First","value":"first"},{"label":"Second","value":"second"},{"label":"Third","value":"third"}],"default":"second","parent":"repeater"}}},"number":{"name":"number","label":"Number","control":"number","type":"integer","order":1,"location":"editor","width":"100","help":"This is example help text","default":"52","placeholder":"Enter a number"}}}}';

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
	public $post_block_expected_content = '{"genesis-custom-blocks\/test-post-2":{"name":"test-post-2","title":"Test Post","excluded":[],"icon":"genesis_custom_blocks","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"post":{"name":"post","label":"Post","control":"post","type":"object","order":0,"location":"editor","width":"75","help":"This is a post","post_type_rest_slug":"posts"}}}}';

	/**
	 * Initial content for a block with a non-default icon.
	 *
	 * @var string
	 */
	public $different_icon_block_initial_content = '{"block-lab\/test-post-2":{"name":"test-post-2","title":"Test Post","excluded":[],"icon":"bookmark_border","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"post":{"name":"post","label":"Post","control":"post","type":"object","order":0,"location":"editor","width":"75","help":"This is a post","post_type_rest_slug":"posts"}}}}';

	/**
	 * Expected content for a block with a non-default icon.
	 *
	 * @var string
	 */
	public $different_icon_block_expected_content = '{"genesis-custom-blocks\/test-post-2":{"name":"test-post-2","title":"Test Post","excluded":[],"icon":"bookmark_border","category":{"slug":"common","title":"Common Blocks","icon":null},"keywords":[""],"fields":{"post":{"name":"post","label":"Post","control":"post","type":"object","order":0,"location":"editor","width":"75","help":"This is a post","post_type_rest_slug":"posts"}}}}';

	/**
	 * Sets up each test.
	 *
	 * @inheritDoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Post_Type( 'block_lab', 'block-lab', 'block_lab', self::NEW_POST_TYPE_SLUG, self::NEW_BLOCK_NAMESPACE, 'genesis_custom_blocks' );
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
	 * @covers \Block_Lab\Blocks\Migration\Post_Type::query_for_posts()
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

		$result = $this->instance->migrate_all();
		$this->assertEquals(
			[
				'successCount' => count( $initial_block_content ),
				'errorCount'   => 0,
			],
			$result
		);

		$query = new WP_Query( [ 'post_type' => self::NEW_POST_TYPE_SLUG ] );
		foreach ( $query->posts as $post ) {
			$this->assertTrue( in_array( $post->post_content, $expected_block_content, true ) );
		}
	}

	/**
	 * Test migrate_all with many posts.
	 *
	 * @covers \Block_Lab\Blocks\Migration\Post_Type::migrate_all()
	 */
	public function test_migrate_all_many_posts() {
		$number_of_block_lab_posts = 203;
		$block_lab_posts           = [];
		for ( $i = 0; $i < $number_of_block_lab_posts; $i++ ) {
			$block_lab_posts[] = $this->create_block_post( $this->post_block_initial_content );
		}

		$non_block_lab_posts           = [];
		$non_block_lab_post_content    = 'This is example content';
		$number_of_non_block_lab_posts = 21;
		for ( $i = 0; $i < $number_of_non_block_lab_posts; $i++ ) {
			$non_block_lab_posts[] = $this->factory()->post->create_and_get(
				[ 'post_content' => $non_block_lab_post_content ]
			);
		}

		$result = $this->instance->migrate_all();
		$this->assertEquals(
			[
				'successCount' => $number_of_block_lab_posts,
				'errorCount'   => 0,
			],
			$result
		);

		$queried_block_lab_post_type = new WP_Query(
			[
				'post_type'      => self::PREVIOUS_POST_TYPE_SLUG,
				'posts_per_page' => -1,
			]
		);

		// All block_lab CPT posts should have been migrated, so there should be none here.
		$this->assertEquals( 0, $queried_block_lab_post_type->post_count );

		$queried_new_cpt_posts = new WP_Query(
			[
				'post_type'      => self::NEW_POST_TYPE_SLUG,
				'posts_per_page' => -1,
			]
		);

		// The block_lab CPT posts should now have the new post_type and migrated post_content.
		$this->assertEquals( $number_of_block_lab_posts, $queried_new_cpt_posts->post_count );
		foreach ( $queried_new_cpt_posts->posts as $cpt_post ) {
			$this->assertEquals( self::NEW_POST_TYPE_SLUG, $cpt_post->post_type );
			$this->assertEquals( $this->post_block_expected_content, $cpt_post->post_content );
		}

		// Posts of type 'post' should not have been migrated.
		$queried_non_block_lab_posts = new WP_Query( [ 'posts_per_page' => -1 ] );
		$this->assertEquals( $number_of_non_block_lab_posts, $queried_non_block_lab_posts->post_count );
		foreach ( $queried_non_block_lab_posts->posts as $non_block_lab_post ) {
			$this->assertEquals( $non_block_lab_post_content, $non_block_lab_post->post_content );
			$this->assertEquals( 'post', $non_block_lab_post->post_type );
		}
	}

	/**
	 * Test that migrate_all does not affect a custom post type other than block_lab.
	 *
	 * @covers \Block_Lab\Blocks\Migration\Post_Type::migrate_all()
	 */
	public function test_migrate_all_other_post_type() {
		$other_post_type_slug = 'testimonial';
		register_post_type( $other_post_type_slug );
		$post_content    = 'This is the content of an example post';
		$posts           = [];
		$number_of_posts = 15;
		for ( $i = 0; $i < $number_of_posts; $i++ ) {
			$posts[] = $this->factory()->post->create_and_get(
				[
					'post_type'    => $other_post_type_slug,
					'post_content' => $post_content,
				]
			);
		}

		$result = $this->instance->migrate_all();
		$this->assertEquals(
			[
				'successCount' => 0,
				'errorCount'   => 0,
			],
			$result
		);

		$queried_posts = new WP_Query(
			[
				'post_type'      => $other_post_type_slug,
				'posts_per_page' => -1,
			]
		);

		foreach ( $queried_posts->posts as $post ) {
			$this->assertEquals( $post_content, $post->post_content );
			$this->assertEquals( $other_post_type_slug, $post->post_type );
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
			'non_default_icon'                => [
				$this->different_icon_block_initial_content,
				$this->different_icon_block_expected_content,
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
