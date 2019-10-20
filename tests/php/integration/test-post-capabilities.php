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
		$GLOBALS['wp_roles'] = new WP_Roles();
		$this->block_post    = new Post_Types\Block_Post();
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
			[ 'subscriber', 'edit_posts', false ],
			[ 'subscriber', 'edit_others_posts', false ],
			[ 'subscriber', 'publish_posts', false ],
			[ 'subscriber', 'read_post', true ],
			[ 'subscriber', 'read_private_posts', false ],
			[ 'subscriber', 'delete_post', false ],
			[ 'contributor', 'edit_posts', true ],
			[ 'contributor', 'edit_others_posts', false ],
			[ 'contributor', 'publish_posts', false ],
			[ 'contributor', 'read_post', true ],
			[ 'contributor', 'read_private_posts', false ],
			[ 'contributor', 'delete_post', false ],
			[ 'author', 'edit_posts', true ],
			[ 'author', 'edit_others_posts', false ],
			[ 'author', 'publish_posts', true ],
			[ 'author', 'read_post', true ],
			[ 'author', 'read_private_posts', false ],
			[ 'author', 'delete_post', false ],
			[ 'editor', 'edit_posts', true ],
			[ 'editor', 'edit_others_posts', true ],
			[ 'editor', 'publish_posts', true ],
			[ 'editor', 'read_post', true ],
			[ 'editor', 'read_private_posts', true ],
			[ 'editor', 'delete_post', true ],
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
