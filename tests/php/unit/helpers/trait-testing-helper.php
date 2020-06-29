<?php
/**
 * Trait with testing helper methods.
 *
 * @package Block_Lab
 */

/**
 * Trait with helper methods.
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

	/**
	 * Asserts that markup is the same.
	 *
	 * Forked from Alain Schlesser's work:
	 * https://github.com/ampproject/amp-wp/blob/b11c04cba3b97ebcfb40dc5833cbdb70a4cf2186/lib/optimizer/tests/src/MarkupComparison.php#L13-L30
	 *
	 * @param string $expected Expected markup.
	 * @param string $actual   Actual markup.
	 */
	public function assert_equal_markup( $expected, $actual ) {
		$actual   = preg_replace( '/\s+/', ' ', $actual );
		$expected = preg_replace( '/\s+/', ' ', $expected );
		$actual   = preg_replace( '/(?<=>)\s+(?=<)/', '', trim( $actual ) );
		$expected = preg_replace( '/(?<=>)\s+(?=<)/', '', trim( $expected ) );

		$this->assertEquals(
			array_filter( preg_split( '#(<[^>]+>|[^<>]+)#', $expected, -1, PREG_SPLIT_DELIM_CAPTURE ) ),
			array_filter( preg_split( '#(<[^>]+>|[^<>]+)#', $actual, -1, PREG_SPLIT_DELIM_CAPTURE ) )
		);
	}
}
