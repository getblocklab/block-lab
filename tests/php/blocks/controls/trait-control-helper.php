<?php
/**
 * Trait with a helper method for testing controls.
 *
 * @package Block_Lab
 */

/**
 * Trait with a helper method.
 */
trait Control_Helper {

	/**
	 * Assert that the settings are correct.
	 *
	 * @param array $expected_settings The expected settings, an array of arrays.
	 * @param array $actual_settings The actual settings, an array of Control_Setting instances.
	 */
	public function assert_correct_settings( $expected_settings, $actual_settings ) {
		foreach ( $actual_settings as $settings_index => $setting ) {
			$expected_setting = $expected_settings[ $settings_index ];
			foreach ( $setting as $setting_key => $setting_value ) {
				unset( $setting_value );
				$this->assertEquals( $expected_setting[ $setting_key ],  $setting->$setting_key );
				$this->assertEquals( 'Block_Lab\Blocks\Controls\Control_Setting', get_class( $setting ) );
			}
		}

		$this->assertEquals( count( $expected_settings ), count( $actual_settings ) );
	}
}
