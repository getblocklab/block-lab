<?php
/**
 * Test_Post_Capabilities
 *
 * @package Block_Lab
 */

use Block_Lab\Post_Types;

/**
 * Class Test_Post_Capabilities
 *
 * Tests the capabilities for the 'block_lab' post type.
 *
 * @package Block_Lab
 */
class Test_Post_Capabilities extends \WP_UnitTestCase {

	/**
	 * Instance of Block_Post.
	 *
	 * @var Post_Types\Block_Post
	 */
	public $block_post;

	/**
	 * The ID of the post.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->block_post = new Post_Types\Block_Post();
		$this->block_post->set_plugin( block_lab() );
		$this->block_post->register_post_type();
		$this->block_post->add_caps();
		$this->post_id = $this->factory()->post->create( [ 'post_type' => $this->block_post->slug ] );
	}

	/**
	 * Gets the users, capabilities, and the expected results.
	 *
	 * @return array[] The users, capabilities, and the expected results.
	 */
	public function get_users() {
		return [
			[ 'subscriber', 'block_lab_edit_block', false ],
			[ 'subscriber', 'block_lab_edit_blocks', false ],
			[ 'subscriber', 'block_lab_edit_others_blocks', false ],
			[ 'subscriber', 'block_lab_publish_blocks', false ],
			[ 'subscriber', 'block_lab_read_block', true ],
			[ 'subscriber', 'block_lab_read_private_blocks', false ],
			[ 'subscriber', 'block_lab_delete_block', false ],

			[ 'contributor', 'block_lab_edit_block', false ],
			[ 'contributor', 'block_lab_edit_blocks', false ],
			[ 'contributor', 'block_lab_edit_others_blocks', false ],
			[ 'contributor', 'block_lab_publish_blocks', false ],
			[ 'contributor', 'block_lab_read_block', true ],
			[ 'contributor', 'block_lab_read_private_blocks', false ],
			[ 'contributor', 'block_lab_delete_block', false ],

			[ 'author', 'block_lab_edit_block', false ],
			[ 'author', 'block_lab_edit_blocks', false ],
			[ 'author', 'block_lab_edit_others_blocks', false ],
			[ 'author', 'block_lab_publish_blocks', false ],
			[ 'author', 'block_lab_read_block', true ],
			[ 'author', 'block_lab_read_private_blocks', false ],
			[ 'author', 'block_lab_delete_block', false ],

			[ 'editor', 'block_lab_edit_block', false ],
			[ 'editor', 'block_lab_edit_blocks', false ],
			[ 'editor', 'block_lab_edit_others_blocks', false ],
			[ 'editor', 'block_lab_publish_blocks', false ],
			[ 'editor', 'block_lab_read_block', true ],
			[ 'editor', 'block_lab_read_private_blocks', false ],
			[ 'editor', 'block_lab_delete_block', true ],

			[ 'administrator', 'edit_post', true ],
			[ 'administrator', 'edit_posts', true ],
			[ 'administrator', 'edit_others_posts', true ],
			[ 'administrator', 'publish_posts', true ],
			[ 'administrator', 'read_post', true ],
			[ 'administrator', 'read_private_posts', true ],
			[ 'administrator', 'delete_post', true ],

			[ 'administrator', 'block_lab_edit_block', true ],
			[ 'administrator', 'block_lab_edit_blocks', true ],
			[ 'administrator', 'block_lab_edit_others_blocks', true ],
			[ 'administrator', 'block_lab_publish_blocks', true ],
			[ 'administrator', 'block_lab_read_block', true ],
			[ 'administrator', 'block_lab_read_private_blocks', true ],
			[ 'administrator', 'block_lab_delete_block', true ],
		];
	}

	/**
	 * Tests that the capabilities are correct for the post type.
	 *
	 * @dataProvider get_users
	 * @covers \Block_Lab\Post_Types\Block_post::register_post_type()
	 *
	 * @param string $user_role The user role, like 'editor'.
	 * @param string $capability The capability to test for, like 'edit_post'.
	 * @param bool   $expected The expected result for those capability and roles.
	 */
	public function test_user_capability( $user_role, $capability, $expected ) {
		wp_set_current_user( $this->factory()->user->create( [ 'role' => $user_role ] ) );
		$this->assertEquals( $expected, current_user_can( $capability, $this->post_id ) );
	}
}
