<?php
/**
 * Tests for helpers.php.
 *
 * @package Block_Lab
 */

use Block_Lab\Blocks;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Tests for helpers.php.
 */
class Test_Helpers extends \WP_UnitTestCase {

	// Shows the assertions as passing.
	use MockeryPHPUnitIntegration;

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {
		block_lab()->loader = new Blocks\Loader();
		remove_all_filters( 'block_lab_default_fields' );
		remove_all_filters( 'block_lab_data_attributes' );
		remove_all_filters( 'block_lab_data_config' );

		parent::tearDown();
	}

	/**
	 * Test block_field.
	 *
	 * @covers ::block_field()
	 */
	public function test_block_field() {
		$field_name     = 'test-user';
		$class_key      = 'className';
		$expected_class = 'baz-class';
		$mock_text      = 'Example text';

		add_filter(
			'block_lab_data_attributes',
			function( $data ) use ( $field_name, $class_key, $mock_text, $expected_class ) {
				$data[ $field_name ] = $mock_text;
				$data[ $class_key ]  = $expected_class;
				return $data;
			}
		);

		add_filter(
			'block_lab_data_config',
			function( $data ) use ( $field_name ) {
				$field_config = [ 'control' => 'text' ];
				$block_config = [
					'fields' => [
						$field_name => $field_config,
					],
				];

				$data = new Blocks\Block();
				$data->from_array( $block_config );

				return $data;
			}
		);

		// Because block_field() had the second argument of false, this should return the value stored in the field, not echo it.
		ob_start();
		$return_value = block_field( $field_name, false );
		$echoed       = ob_get_clean();
		$this->assertEquals( $mock_text, $return_value );
		$this->assertEmpty( $echoed );

		// Test the same scenario as above, but for 'className'.
		ob_start();
		$return_value = block_field( $class_key, false );
		$echoed       = ob_get_clean();
		$this->assertEquals( $expected_class, $return_value );
		$this->assertEmpty( $echoed );

		ob_start();
		$return_value      = block_field( $field_name, true );
		$actual_user_login = ob_get_clean();

		// Because block_field() has a second argument of true, this should echo the user login and return null.
		$this->assertEquals( $mock_text, $actual_user_login );
		$this->assertEquals( null, $return_value );

		ob_start();
		$return_value = block_field( $class_key, true );
		$actual_class = ob_get_clean();

		// Test the same scenario as above, but for 'className'.
		$this->assertEquals( $expected_class, $actual_class );
		$this->assertEquals( null, $return_value );

		$additional_field_name  = 'example_additional_field';
		$additional_field_value = 'Here is some text';

		remove_all_filters( 'block_lab_data_attributes' );

		add_filter(
			'block_lab_data_attributes',
			function() use ( $additional_field_name, $additional_field_value ) {
				return [ $additional_field_name => $additional_field_value ];
			}
		);

		ob_start();
		$return_value = block_field( $additional_field_name, true );
		$echoed_value = ob_get_clean();

		// When a field isn't in the block_lab()->loader->data['config'], it should not be echoed or returned.
		$this->assertEmpty( $return_value );
		$this->assertEmpty( $echoed_value );

		$default_fields_filter = 'block_lab_default_fields';

		// Don't return anything from the filter callback, to test the behavior.
		add_filter(
			$default_fields_filter,
			function( $default_fields ) use ( $additional_field_name ) {
				$default_fields[] = $additional_field_name;
			}
		);

		ob_start();
		$return_value = block_field( $additional_field_name, true );
		$echoed_value = ob_get_clean();

		// In case the filter accidentally doesn't return anything, there should still not be a fatal error, there should just be no output.
		$this->assertEmpty( $return_value );
		$this->assertEmpty( $echoed_value );
		remove_all_filters( $default_fields_filter );

		add_filter(
			$default_fields_filter,
			function( $default_fields ) use ( $additional_field_name ) {
				$default_fields[ $additional_field_name ] = 'string';
				return $default_fields;
			}
		);

		ob_start();
		$return_value = block_field( $additional_field_name, true );
		$echoed_value = ob_get_clean();

		// Now that the filter includes the additional field, the field should be echoed, even though it's not in block_lab()->data['config'].
		$this->assertEquals( null, $return_value );
		$this->assertEquals( $additional_field_value, $echoed_value );
	}

	/**
	 * Test block_lab_add_block.
	 *
	 * @covers ::block_lab_add_block()
	 */
	public function test_block_lab_add_block() {
		// Test calling this without the optional second argument.
		$block_name              = 'example-block';
		$expected_default_config = [
			'category' => 'common',
			'excluded' => [],
			'fields'   => [],
			'icon'     => 'block_lab',
			'keywords' => [],
			'name'     => $block_name,
			'title'    => 'Example Block',
		];

		$loader             = Mockery::mock( Blocks\Loader::class );
		block_lab()->loader = $loader;
		$loader->expects()->add_block( $expected_default_config );
		block_lab_add_block( $block_name );

		// Test passing a $block_config, with a long name.
		$block_name   = 'this-is-a-long-block-name';
		$block_config = [
			'category' => 'example',
			'excluded' => [ 'baz', 'another' ],
			'fields'   => [ 'text' ],
			'icon'     => 'great_icon',
			'keywords' => [ 'hero', 'ad' ],
			'name'     => $block_name,
		];

		$expected_config = array_merge(
			$block_config,
			[ 'title' => 'This Is A Long Block Name' ]
		);
		$loader->expects()->add_block( $expected_config );
		block_lab_add_block( $block_name, $block_config );
	}

	/**
	 * Test block_lab_add_field.
	 *
	 * @covers ::block_lab_add_field()
	 */
	public function test_block_lab_add_field() {
		// Test calling this without the optional third argument.
		$block_name              = 'baz-block';
		$field_name              = 'another-field';
		$expected_default_config = [
			'control'  => 'text',
			'label'    => 'Another Field',
			'name'     => $field_name,
			'order'    => 0,
			'settings' => [],
		];

		$loader             = Mockery::mock( Blocks\Loader::class );
		block_lab()->loader = $loader;
		$loader->expects()->add_field( $block_name, $expected_default_config )->once();
		block_lab_add_field( $block_name, $field_name );

		// Test passing a full $field_config.
		$block_name   = 'example-block-name-here';
		$field_name   = 'here_is_a_long_field_name';
		$field_config = [
			'control'  => 'rich_text',
			'label'    => 'Here Is Another Field',
			'order'    => 3,
			'settings' => [ 'foo' => 'baz' ],
		];

		$expected_field_config = array_merge(
			$field_config,
			[ 'name' => 'here-is-a-long-field-name' ]
		);

		$loader->expects()->add_field( $block_name, $expected_field_config )->once();
		block_lab_add_field( $block_name, $field_name, $field_config );
	}
}
