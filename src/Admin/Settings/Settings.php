<?php
/**
 * Registers plugin settings to the backend.
 *
 * @package WPGraphQL\RankMath\Admin\Settings
 */

namespace WPGraphQL\RankMath\Admin\Settings;

use WPGraphQL\Admin\Settings\SettingsRegistry;

/**
 * Class - Settings
 */
class Settings {
	/**
	 * An instance of the Settings API.
	 *
	 * @var ?\WPGraphQL\Admin\Settings\SettingsRegistry
	 */
	private static $settings_api;

	/**
	 * The section named used in the settings API.
	 *
	 * @var string
	 */
	public static string $section_name = 'graphql_seo_settings';

	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		add_action( 'admin_init', [ self::class, 'register_settings' ] );
	}

	/**
	 * Gets an instance of the WPGraphQL settings api.
	 */
	public static function get_settings_api(): SettingsRegistry {
		if ( ! isset( self::$settings_api ) || ! self::$settings_api instanceof SettingsRegistry ) {
			self::$settings_api = new SettingsRegistry();
		}

		return self::$settings_api;
	}

	/**
	 * Registers the settings to WPGraphQL
	 */
	public static function register_settings(): void {
		$settings_api = self::get_settings_api();

		$settings_api->register_fields(
			self::$section_name,
			[
				[
					'name'    => 'delete_data_on_deactivate',
					'label'   => __( 'Delete Data on Deactivation', 'wp-graphql-rank-math' ),
					'desc'    => __( 'Delete settings and any other data stored by WPGraphQL for Rank Math upon de-activation of the plugin. Un-checking this will keep data after the plugin is de-activated.', 'wp-graphql-rank-math' ),
					'type'    => 'checkbox',
					'default' => 'on',
				],
			]
		);

		$settings_api->admin_init();
	}
}
