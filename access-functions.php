<?php
/**
 * This file contains access functions for various class methods.
 *
 * @package WPGraphQL/RankMath
 */

if ( ! function_exists( 'graphql_seo_get_setting' ) ) {
	/**
	 * Get an option value from the plugin settings.
	 *
	 * @param string $option_name The key of the option to return.
	 * @param mixed  $default The default value the setting should return if no value is set.
	 * @param string $section_name The settings group section that the option belongs to.
	 *
	 * @return mixed
	 */
	function graphql_seo_get_setting( string $option_name, $default = '', $section_name = 'graphql_seo_settings' ) {
		$section_fields = get_option( $section_name );

		/**
		 * Filter the section fields
		 *
		 * @param array  $section_fields The values of the fields stored for the section
		 * @param string $section_name   The name of the section
		 * @param mixed  $default        The default value for the option being retrieved
		 */
		$section_fields = apply_filters( 'graphql_seo_get_setting_section_fields', $section_fields, $section_name, $default );

		/**
		 * Get the value from the stored data, or return the default
		 */
		$value = isset( $section_fields[ $option_name ] ) ? $section_fields[ $option_name ] : $default;

		/**
		 * Filter the value before returning it
		 *
		 * @param mixed  $value          The value of the field
		 * @param mixed  $default        The default value if there is no value set
		 * @param string $option_name    The name of the option
		 * @param array  $section_fields The setting values within the section
		 * @param string $section_name   The name of the section the setting belongs to
		 */
		return apply_filters( 'graphql_seo_get_setting_section_field_value', $value, $default, $option_name, $section_fields, $section_name );
	}
}
