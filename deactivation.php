<?php
/**
 * Deactivation Hook
 *
 * @package WPGraphql\RankMath
 */

if ( ! function_exists( 'graphql_seo_deactivation_callback' ) ) {
	/**
	 * Runs when WPGraphQL is de-activated.
	 *
	 * This cleans up data that WPGraphQL stores.
	 */
	function graphql_seo_deactivation_callback(): callable {
		return static function (): void {

			// Fire an action when WPGraphQL is de-activating.
			do_action( 'graphql_seo_deactivate' );

			// Delete data during activation.
			graphql_seo_delete_data();
		};
	}
}

if ( ! function_exists( 'graphql_seo_delete_data' ) ) {
	/**
	 * Delete data on deactivation.
	 */
	function graphql_seo_delete_data(): void {

		// Check if the plugin is set to delete data or not.
		$delete_data = graphql_seo_get_setting( 'delete_data_on_deactivate' );

		// Bail if not set to delete.
		if ( 'on' !== $delete_data ) {
			return;
		}

		// Delete plugin version.
		delete_option( 'wp_graphql_seo_version' );

		// Initialize the settings API.
		$settings = new \WPGraphQL\RankMath\Admin\Settings\Settings();
		$settings::register_settings();

		// Get all the registered settings fields.
		$fields = $settings::get_settings_api()->get_settings_fields();

		// Loop over the registered settings fields and delete the options.
		if ( ! empty( $fields ) && is_array( $fields ) ) {
			foreach ( $fields as $group => $fields ) {
				delete_option( $group );
			}
		}

		do_action( 'graphql_seo_delete_data' );
	}
}
