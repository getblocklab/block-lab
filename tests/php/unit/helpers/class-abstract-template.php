<?php
/**
 * Abstract test class, used for testing functions that get templates.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;

/**
 * Abstract test class.
 */
abstract class Abstract_Template extends \WP_UnitTestCase {

	/**
	 * The name of a testing block.
	 *
	 * @var string
	 */
	public $mock_block_name = 'mock-bl';

	/**
	 * The path of the parent theme.
	 *
	 * @var string
	 */
	public $theme_directory;

	/**
	 * The directories that were created, in order to later remove them in tearDown().
	 *
	 * @var string[]
	 */
	public $directories_created = array();

	/**
	 * The files that were created, in order to later remove them in tearDown().
	 *
	 * @var string[]
	 */
	public $files_created = array();

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Blocks\Loader();
		$this->instance->set_plugin( block_lab() );

		$this->theme_directory    = get_template_directory();
		$this->template_locations = block_lab()->get_template_locations( $this->mock_block_name );
		$this->create_block_template_directories();
	}

	/**
	 * Teardown.
	 *
	 * Deletes the mock templates and directories that were created.
	 * This is in tearDown(), as it runs even if a test fails.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		// Delete testing templates and CSS files.
		array_map(
			function( $file ) {
				if ( file_exists( $file ) ) {
					unlink( $file );
				}
			},
			$this->files_created
		);

		// Remove testing directories that were created, in reverse order.
		array_map(
			function( $directory ) {
				if ( is_dir( $directory ) ) {
					rmdir( $directory );
				}
			},
			array_reverse( $this->directories_created )
		);

		parent::tearDown();
	}

	/**
	 * Gets the directories that block templates and CSS files could be in.
	 */
	public function create_block_template_directories() {
		array_map(
			function( $directory ) {
				$this->mkdir( $directory );
			},
			array(
				"{$this->theme_directory}/blocks/",
				"{$this->theme_directory}/blocks/css/",
				"{$this->theme_directory}/blocks/{$this->mock_block_name}/",
			)
		);
	}

	/**
	 * Gets the template paths for the the mock block, in order of descending priority.
	 *
	 * @return array The template paths in the parent theme.
	 */
	public function get_template_paths_in_theme() {
		return array_map(
			function( $template_location ) {
				return "{$this->theme_directory}/{$template_location}";
			},
			$this->template_locations
		);
	}

	/**
	 * Creates a directory, and stores the directory in order to later remove it in tearDown().
	 *
	 * @param string $directory The directory to create.
	 */
	public function mkdir( $directory ) {
		if ( ! is_dir( $directory ) ) {
			mkdir( $directory );
			array_push( $this->directories_created, $directory );
		}
	}

	/**
	 * Puts contents in a file, and stores the file name in order to later remove it in tearDown().
	 *
	 * @param string $file     The full file path.
	 * @param string $contents The contents of the file.
	 */
	public function file_put_contents( $file, $contents ) {
		file_put_contents( $file, $contents ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		array_push( $this->files_created, $file );
	}
}
