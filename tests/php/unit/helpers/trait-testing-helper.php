<?php
/**
 * Trait with a helper method for testing controls.
 *
 * @package Block_Lab
 */

/**
 * Trait with a helper method.
 */
trait Testing_Helper {

	/**
	 * Assert that the settings are correct.
	 *
	 * $actual_settings has objects, and $expected_settings has arrays.
	 * So this iterates through the $expected_settings for each object,
	 * ensuring the key and value of the $expected_settings array match a property of the object.
	 *
	 * @param array $expected_settings The expected settings, an array of arrays.
	 * @param array $actual_settings The actual settings, an array of Control_Setting instances.
	 */
	public function assert_correct_settings( $expected_settings, $actual_settings ) {
		foreach ( $actual_settings as $settings_index => $setting ) {
			$expected_setting = $expected_settings[ $settings_index ];
			foreach ( $setting as $setting_key => $setting_value ) {
				unset( $setting_value );
				$this->assertEquals( $expected_setting[ $setting_key ], $setting->$setting_key );
				$this->assertEquals( 'Block_Lab\Blocks\Controls\Control_Setting', get_class( $setting ) );
			}
		}

		$this->assertEquals( count( $expected_settings ), count( $actual_settings ) );
	}

	/**
	 * Sets whether the license is valid or not.
	 *
	 * @param bool $is_valid Whether the license is valid.
	 */
	public function set_license_validity( $is_valid ) {
		if ( $is_valid ) {
			$transient_value = [
				'license' => 'valid',
				'expires' => gmdate( 'D, d M Y H:i:s', time() + 1000 ),
			];
		} else {
			$transient_value = [
				'license' => 'expired',
			];
		}

		set_transient( 'block_lab_license', $transient_value );
	}
}
