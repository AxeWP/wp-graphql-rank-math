<?php
/**
 * Registers plugin settings to the backend.
 *
 * @package WPGraphQL\PluginName\Admin\Settings
 */

namespace WPGraphQL\PluginName\Admin\Settings;

use \WPGraphQL\Admin\Settings\SettingsRegistry;

/**
 * Class - Settings
 */
class Settings {
	/**
	 * An instance of the Settings API.
	 *
	 * @var ?SettingsRegistry
	 */
	private static $settings_api;

	/**
	 * The section named used in the settings API.
	 *
	 * @var string
	 */
	public static string $section_name = 'wpgraphql_pb_settings';

	/**
	 * {@inheritDoc}
	 */
	public static function init() : void {
		add_action( 'init', [ __CLASS__, 'register_settings' ] );
	}

	/**
	 * Gets an instance of the WPGraphQL settings api.
	 */
	public static function get_settings_api() : SettingsRegistry {
		if ( ! isset( self::$settings_api ) ) {
			self::$settings_api = new SettingsRegistry();
		}

		return self::$settings_api;
	}

	/**
	 * Registers the settings to WPGraphQL
	 */
	public static function register_settings() : void {
		$settings_api = self::get_settings_api();

		$settings_api->register_fields(
			self::$section_name,
			[
				[
					'name'    => 'delete_data_on_deactivate',
					'label'   => __( 'Delete Data on Deactivation', 'wp-graphql-plugin-name' ),
					'desc'    => __( 'Delete settings and any other data stored by WPGraphQL Plugin Name upon de-activation of the plugin. Un-checking this will keep data after the plugin is de-activated.', 'wp-graphql-plugin-name' ),
					'type'    => 'checkbox',
					'default' => 'on',
				],
			]
		);

		$settings_api->admin_init();
	}
}
