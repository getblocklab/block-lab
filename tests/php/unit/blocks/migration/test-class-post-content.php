<?php
/**
 * Test_Post_Content
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks\Migration\Post_Content;

/**
 * Class Test_Post_Content
 *
 * @package Block_Lab
 */
class Test_Post_Content extends WP_UnitTestCase {

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
	 * The instance to test.
	 *
	 * @var Post_Content
	 */
	public $instance;

	/**
	 * Initial content for a simple block.
	 *
	 * @var string
	 */
	public $image_block_initial_content = '<!-- wp:block-lab/test-image {"image":154} /-->';

	/**
	 * Expected content for a simple block.
	 *
	 * @var string
	 */
	public $image_block_expected_content = '<!-- wp:genesis-custom-blocks/test-image {"image":154} /-->';

	/**
	 * Initial content for two blocks.
	 *
	 * @var string
	 */
	public $two_blocks_initial_content = '<!-- wp:block-lab/test-textarea {"textarea":"Here is some text And some more"} /-->
		<!-- wp:block-lab/test-range {"range":32} /-->';

	/**
	 * Expected content for two blocks.
	 *
	 * @var string
	 */
	public $two_blocks_expected_content = '<!-- wp:genesis-custom-blocks/test-textarea {"textarea":"Here is some text And some more"} /-->
		<!-- wp:genesis-custom-blocks/test-range {"range":32} /-->';

	/**
	 * Sets up each test.
	 *
	 * @inheritDoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Post_Content( self::PREVIOUS_BLOCK_NAMESPACE, self::NEW_BLOCK_NAMESPACE );
	}

	/**
	 * Creates a block post with the previous post_type.
	 *
	 * @param string $content   The post content.
	 * @param string $post_type The post_type.
	 * @return WP_Post The post with the content.
	 */
	public function create_block_post( $content, $post_type = 'post' ) {
		return $this->factory()->post->create_and_get(
			[
				'post_type'    => $post_type,
				'post_content' => $content,
			]
		);
	}

	/**
	 * Gets the test data for test_migrate_single().
	 *
	 * @return array The test data.
	 */
	public function get_data_migrate_single() {
		return [
			'no_block'           => [
				'This post content does not have a block <p>Here is a paragraph</p>',
			],
			'simple_image_block' => [
				$this->image_block_initial_content,
				$this->image_block_expected_content,
			],
			'two_blocks'         => [
				$this->two_blocks_initial_content,
				$this->two_blocks_expected_content,
			],
		];
	}

	/**
	 * Test migrate_single.
	 *
	 * @dataProvider get_data_migrate_single
	 * @covers \Block_Lab\Blocks\Migration\Post_Content::migrate_single()
	 *
	 * @param string $initial_post_content  Initial post_content.
	 * @param string $expected_post_content Expected post_content of the new post.
	 */
	public function test_migrate_single( $initial_post_content, $expected_post_content = null ) {
		if ( null === $expected_post_content ) {
			$expected_post_content = $initial_post_content;
		}

		$post = $this->create_block_post( $initial_post_content );
		$this->assertInternalType( 'int', $this->instance->migrate_single( $post ) );

		$new_post = get_post( $post->ID );
		$this->assertEquals( $expected_post_content, $new_post->post_content );
	}
}
