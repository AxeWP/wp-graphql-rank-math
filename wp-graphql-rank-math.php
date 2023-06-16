<?php
/**
 * Plugin Name: WPGraphQL for Rank Math SEO
 * Plugin URI: https://github.com/AxeWP/wp-graphql-rank-math
 * GitHub Plugin URI: https://github.com/AxeWP/wp-graphql-rank-math
 * Description: Adds RankMath support to WPGraphQL
 * Author: AxePress
 * Author URI: https://github.com/AxeWP
 * Update URI: https://github.com/AxeWP/wp-graphql-rank-math
 * Version: 0.0.14
 * Text Domain: wp-graphql-rank-math
 * Domain Path: /languages
 * Requires at least: 5.4.1
 * Tested up to: 6.2.2
 * Requires PHP: 7.4
 * WPGraphQL requires at least: 1.8.1
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPGraphQL\RankMath
 * @author axepress
 * @license GPL-3
 * @version 0.0.14
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If the codeception remote coverage file exists, require it.
// This file should only exist locally or when CI bootstraps the environment for testing.
if ( file_exists( __DIR__ . '/c3.php' ) ) {
	require_once __DIR__ . '/c3.php';
}

// Run this function when the plugin is activated.
if ( file_exists( __DIR__ . '/activation.php' ) ) {
	require_once __DIR__ . '/activation.php';
	register_activation_hook( __FILE__, 'graphql_seo_activation_callback' );
}

// Run this function when the plugin is deactivated.
if ( file_exists( __DIR__ . '/deactivation.php' ) ) {
	require_once __DIR__ . '/deactivation.php';
	register_activation_hook( __FILE__, 'graphql_seo_deactivation_callback' );
}

if ( ! function_exists( 'graphql_seo_constants' ) ) {
	/**
	 * Define plugin constants.
	 */
	function graphql_seo_constants(): void {
		// Plugin version.
		if ( ! defined( 'WPGRAPHQL_SEO_VERSION' ) ) {
			define( 'WPGRAPHQL_SEO_VERSION', '0.0.14' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'WPGRAPHQL_SEO_PLUGIN_DIR' ) ) {
			define( 'WPGRAPHQL_SEO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'WPGRAPHQL_SEO_PLUGIN_URL' ) ) {
			define( 'WPGRAPHQL_SEO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'WPGRAPHQL_SEO_PLUGIN_FILE' ) ) {
			define( 'WPGRAPHQL_SEO_PLUGIN_FILE', __FILE__ );
		}

		// Whether to autoload the files or not.
		if ( ! defined( 'WPGRAPHQL_SEO_AUTOLOAD' ) ) {
			define( 'WPGRAPHQL_SEO_AUTOLOAD', true );
		}
	}
}

if ( ! function_exists( 'graphql_seo_dependencies_not_ready' ) ) {
	/**
	 * Checks if all the the required plugins are installed and activated.
	 *
	 * @return array<string, string> List of dependencies that are not ready.
	 */
	function graphql_seo_dependencies_not_ready(): array {
		$wpgraphql_version = '1.8.1';
		$rankmath_version  = '1.0.90';

		$deps = [];

		// WPGraphQL Check.
		if ( ! class_exists( '\WPGraphQL' ) || ( defined( 'WPGRAPHQL_VERSION' ) && version_compare( WPGRAPHQL_VERSION, $wpgraphql_version, '<' ) ) ) { // @phpstan-ignore-line
			$deps['WPGraphQL'] = $wpgraphql_version;
		}

		if ( ! class_exists( '\RankMath' ) || defined( 'RANK_MATH_VERSION' ) && version_compare( RANK_MATH_VERSION, $rankmath_version, '<' ) ) {
			$deps['RankMath SEO'] = $rankmath_version;
		}

		return $deps;
	}
}

if ( ! function_exists( 'graphql_seo_plugin_conflicts' ) ) {
	/**
	 * Checks if any known plugin conflicts are present.
	 *
	 * @return array<string, string> List of conflicting plugins.
	 *
	 * @since 0.0.12
	 */
	function graphql_seo_plugin_conflicts(): array {
		$conflicts = [];

		if ( function_exists( 'wp_gql_seo_build_content_type_data' ) ) {
			$conflicts['WPGraphQL Yoast SEO Addon'] = __( 'This plugin may appear as "Add WPGraphQL SEO" in the plugin list.', 'wp-graphql-rank-math' );
		}

		return $conflicts;
	}
}

if ( ! function_exists( 'graphql_seo_init' ) ) {

	/**
	 * Initializes plugin.
	 */
	function graphql_seo_init(): void {
		graphql_seo_constants();

		$not_ready = graphql_seo_dependencies_not_ready();

		// Get the conflicting plugins.
		$conflicts = graphql_seo_plugin_conflicts();

		if ( empty( $not_ready ) && empty( $conflicts ) && defined( 'WPGRAPHQL_SEO_PLUGIN_DIR' ) ) {
			require_once WPGRAPHQL_SEO_PLUGIN_DIR . 'src/Main.php';
			\WPGraphQL\RankMath\Main::instance();
			return;
		}

		// Output an error notice for the dependencies that are not ready.
		foreach ( $not_ready as $dep => $version ) {
			add_action(
				'admin_notices',
				static function () use ( $dep, $version ) {
					?>
					<div class="error notice">
						<p>
							<?php
								printf(
									/* translators: dependency not ready error message */
									esc_html__( '%1$s (v%2$s+) must be active for WPGraphQL for Rank Math to work.', 'wp-graphql-rank-math' ),
									esc_attr( $dep ),
									esc_attr( $version ),
								);
							?>
						</p>
					</div>
					<?php
				}
			);
		}

		// Output an error notice for the conflicting plugins.
		foreach ( $conflicts as $conflict => $note ) {
			add_action(
				'admin_notices',
				static function () use ( $conflict, $note ) {
					?>
				<div class="error notice">
					<p>
						<?php
						printf(
							/* translators: dependency not ready error message */
							esc_html__( '%1$s is not compatible with WPGraphQL for Rank Math SEO. Please deactivate it.', 'wp-graphql-rank-math' ),
							esc_attr( $conflict ),
						);

						if ( ! empty( $note ) ) {
							// translators: resolution message.
							printf(
								'<br /><em>%1$s</em> %2$s',
								esc_html__( 'Note: ', 'wp-graphql-rank-math' ),
								esc_html( $note ),
							);
						}
						?>
					</p>
				</div>
					<?php
				}
			);
		}
	}
}

// Initialize the plugin.
add_action( 'graphql_init', 'graphql_seo_init' );
