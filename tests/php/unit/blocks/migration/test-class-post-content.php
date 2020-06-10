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
				'post_content' => wp_slash( $content ),
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
			'no_block'                               => [
				'This post content does not have a block <p>Here is a paragraph</p>',
			],
			'no_block_lab_block'                     => [
				'<!-- wp:image {"id":145,"sizeSlug":"large"} -->
					<figure class="wp-block-image size-large"><img src="https://example.test/wp-content/uploads/2020/01/81-600x400-1.jpg" alt="" class="wp-image-145" /></figure>
					<!-- /wp:image -->

					<!-- wp:paragraph -->
					<p>Here is some text</p>
					<!-- /wp:paragraph -->

					<!-- wp:core-embed/youtube {"url":"https://www.youtube.com/watch?v=gS6_xOABTWo","type":"video","providerNameSlug":"youtube","className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
					<figure class="wp-block-embed-youtube wp-block-embed is-type-video is-provider-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
					https://www.youtube.com/watch?v=gS6_xOABTWo
					</div></figure>
					<!-- /wp:core-embed/youtube -->',
			],
			'simple_image_block'                     => [
				$this->image_block_initial_content,
				$this->image_block_expected_content,
			],
			'two_blocks'                             => [
				$this->two_blocks_initial_content,
				$this->two_blocks_expected_content,
			],
			'single_block_lab_block_among_others'    => [
				'<!-- wp:block-lab/repeater-with-classic {"repeater":{"rows":[{"":"","classic":"\u003cp\u003eHere is a classic text field\u003c/p\u003e\n\u003cp\u003eAnd another line\u003c/p\u003e"},{"":"","classic":"\u003cp\u003eHere is another\u003c/p\u003e"}]}} /-->

					<!-- wp:group -->
					<div class="wp-block-group"><div class="wp-block-group__inner-container"><!-- wp:image {"id":149,"sizeSlug":"large"} -->
					<figure class="wp-block-image size-large"><img src="https://bl.test/wp-content/uploads/2020/01/858-600x400-1.jpg" alt="" class="wp-image-149"/></figure>
					<!-- /wp:image --></div></div>
					<!-- /wp:group -->
					
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link" href="https://example.com">Click here</a></div>
					<!-- /wp:button -->',
				'<!-- wp:genesis-custom-blocks/repeater-with-classic {"repeater":{"rows":[{"":"","classic":"\u003cp\u003eHere is a classic text field\u003c/p\u003e\n\u003cp\u003eAnd another line\u003c/p\u003e"},{"":"","classic":"\u003cp\u003eHere is another\u003c/p\u003e"}]}} /-->

					<!-- wp:group -->
					<div class="wp-block-group"><div class="wp-block-group__inner-container"><!-- wp:image {"id":149,"sizeSlug":"large"} -->
					<figure class="wp-block-image size-large"><img src="https://bl.test/wp-content/uploads/2020/01/858-600x400-1.jpg" alt="" class="wp-image-149"/></figure>
					<!-- /wp:image --></div></div>
					<!-- /wp:group -->
					
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link" href="https://example.com">Click here</a></div>
					<!-- /wp:button -->',
			],
			'multiple_block_lab_blocks_among_others' => [
				'<!-- wp:block-lab/all-fields-test {"text":"This is some example text","textarea":"Lorem ipsum dolor","url":"https://foobaz.com","email":"art@example.com","number":532,"color":"#2c0c0c","image":149,"select":"baz","multiselect":["foo"],"toggle":true,"range":64,"repeater":{"rows":[{"":"","text":"Here is some text","image":150}]},"post":{"id":606,"name":"Testing"},"rich-text":"\u003cp\u003eThis is \u003cstrong\u003ebold\u003c/strong\u003e and \u003cem\u003eitalic\u003c/em\u003e\u003c/p\u003e","classic-text":"\u003cp\u003eThis is the first line\u003c/p\u003e\n\u003cp\u003e \u003c/p\u003e\n\u003cp\u003eHere is another line\u003c/p\u003e","taxonomy":{"id":5,"name":"Cat"},"user":{"id":1,"userName":"admin"},"checkbox":true,"radio":"another"} /-->

					<!-- wp:archives /-->

					<!-- wp:gallery {"ids":[154,147,148]} -->
					<figure class="wp-block-gallery columns-3 is-cropped"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="https://bl.test/wp-content/uploads/2020/01/979-200x400-2.jpg" alt="" data-id="154" data-full-url="https://bl.test/wp-content/uploads/2020/01/979-200x400-2.jpg" data-link="https://bl.test/?attachment_id=154" class="wp-image-154"/></figure></li><li class="blocks-gallery-item"><figure><img src="https://bl.test/wp-content/uploads/2020/01/568-1000x1000-1.jpg" alt="" data-id="147" data-full-url="https://bl.test/wp-content/uploads/2020/01/568-1000x1000-1.jpg" data-link="https://bl.test/?attachment_id=147" class="wp-image-147"/></figure></li><li class="blocks-gallery-item"><figure><img src="https://bl.test/wp-content/uploads/2020/01/726-300x300-1.jpg" alt="" data-id="148" data-full-url="https://bl.test/wp-content/uploads/2020/01/726-300x300-1.jpg" data-link="https://bl.test/?attachment_id=148" class="wp-image-148"/></figure></li></ul></figure>
					<!-- /wp:gallery -->

					<!-- wp:block-lab/test-post-2 {"post":{"id":606,"name":"Testing"}} /-->

					<!-- wp:block-lab/test-url {"url":"https://example.com/foo"} /-->',
				'<!-- wp:genesis-custom-blocks/all-fields-test {"text":"This is some example text","textarea":"Lorem ipsum dolor","url":"https://foobaz.com","email":"art@example.com","number":532,"color":"#2c0c0c","image":149,"select":"baz","multiselect":["foo"],"toggle":true,"range":64,"repeater":{"rows":[{"":"","text":"Here is some text","image":150}]},"post":{"id":606,"name":"Testing"},"rich-text":"\u003cp\u003eThis is \u003cstrong\u003ebold\u003c/strong\u003e and \u003cem\u003eitalic\u003c/em\u003e\u003c/p\u003e","classic-text":"\u003cp\u003eThis is the first line\u003c/p\u003e\n\u003cp\u003e \u003c/p\u003e\n\u003cp\u003eHere is another line\u003c/p\u003e","taxonomy":{"id":5,"name":"Cat"},"user":{"id":1,"userName":"admin"},"checkbox":true,"radio":"another"} /-->

					<!-- wp:archives /-->

					<!-- wp:gallery {"ids":[154,147,148]} -->
					<figure class="wp-block-gallery columns-3 is-cropped"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="https://bl.test/wp-content/uploads/2020/01/979-200x400-2.jpg" alt="" data-id="154" data-full-url="https://bl.test/wp-content/uploads/2020/01/979-200x400-2.jpg" data-link="https://bl.test/?attachment_id=154" class="wp-image-154"/></figure></li><li class="blocks-gallery-item"><figure><img src="https://bl.test/wp-content/uploads/2020/01/568-1000x1000-1.jpg" alt="" data-id="147" data-full-url="https://bl.test/wp-content/uploads/2020/01/568-1000x1000-1.jpg" data-link="https://bl.test/?attachment_id=147" class="wp-image-147"/></figure></li><li class="blocks-gallery-item"><figure><img src="https://bl.test/wp-content/uploads/2020/01/726-300x300-1.jpg" alt="" data-id="148" data-full-url="https://bl.test/wp-content/uploads/2020/01/726-300x300-1.jpg" data-link="https://bl.test/?attachment_id=148" class="wp-image-148"/></figure></li></ul></figure>
					<!-- /wp:gallery -->

					<!-- wp:genesis-custom-blocks/test-post-2 {"post":{"id":606,"name":"Testing"}} /-->

					<!-- wp:genesis-custom-blocks/test-url {"url":"https://example.com/foo"} /-->',
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
		// Prevent sanitization of post_content.
		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );

		if ( null === $expected_post_content ) {
			$expected_post_content = $initial_post_content;
		}

		$post         = $this->create_block_post( $initial_post_content );
		$return_value = $this->instance->migrate_single( $post );
		$this->assertInternalType( 'int', $return_value );

		$new_post = get_post( $post->ID );
		$this->assertEquals( $expected_post_content, $new_post->post_content );
	}
}
