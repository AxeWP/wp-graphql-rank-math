<?php
/**
 * Tests access functons
 */
class AccessFunctionsTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		update_option( 'graphql_pb_settings', [ 'delete_data_on_deactivate' => true ] );

		parent::setUp();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * Tests graphql_pb_get_setting
	 */
	public function testGraphQLPluginNameGetSetting() : void {
		$expected = true;

		$actual = graphql_pb_get_setting( 'delete_data_on_deactivate' );

		$this->assertEquals( $expected, $actual );

		// Test graphql_pb_get_setting_section_fields filter.
		$expected_value       = 'value';
		$expected_default     = 'default';
		$expected_option_name = 'mySetting';

		add_filter(
			'graphql_pb_get_setting_section_fields',
			function( array $section_fields, string $section_name, $default ) use ( $expected_value, $expected_default, $expected_option_name ) {
				$this->assertEquals( $expected_default, $default );

				return array_merge( $section_fields, [ $expected_option_name => $expected_value ] );
			},
			10,
			3
		);

		$actual = graphql_pb_get_setting( $expected_option_name, $expected_default );
		$this->assertEquals( 'value', $actual );

		// Test graphql_pb_get_setting_section_field_value filter.

		add_filter(
			'graphql_pb_get_setting_section_field_value',
			function( $value, $default, string $option_name, array $section_fields, string $section_name ) use ( $expected_value, $expected_default, $expected_option_name ) {
				$this->assertEquals( $expected_value, $value );
				$this->assertEquals( $expected_default, $default );
				$this->assertEquals( $expected_option_name, $option_name );

				return 'new value';
			},
			10,
			5,
		);

		$actual = graphql_pb_get_setting( $expected_option_name, $expected_default );
		$this->assertEquals( 'new value', $actual );
	}

}
