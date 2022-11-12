<?php
/**
 * Plugin Name: WPGraphQL for Rank Math SEO
 * Plugin URI: https://github.com/AxeWP/wp-graphql-rank-math
 * GitHub Plugin URI: https://github.com/AxeWP/wp-graphql-rank-math
 * Description: Adds RankMath support to WPGraphQL
 * Author: AxePress
 * Author URI: https://github.com/AxeWP
 * Update URI: https://github.com/AxeWP/wp-graphql-rank-math
 * Version: 0.0.7
 * Text Domain: wp-graphql-rank-math
 * Domain Path: /languages
 * Requires at least: 5.4.1
 * Tested up to: 6.1
 * Requires PHP: 7.4
 * WPGraphQL requires at least: 1.8.0
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPGraphQL\RankMath
 * @author axepress
 * @license GPL-3
 * @version 0.0.7
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


/**
 * Define plugin constants.
 */
function graphql_seo_constants() : void {
	// Plugin version.
	if ( ! defined( 'WPGRAPHQL_SEO_VERSION' ) ) {
		define( 'WPGRAPHQL_SEO_VERSION', '0.0.7' );
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

	// The Plugin Boilerplate hook prefix.
	if ( ! defined( 'AXEWP_PB_HOOK_PREFIX' ) ) {
		define( 'AXEWP_PB_HOOK_PREFIX', 'graphql_seo' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	}
}

/**
 * Checks if all the the required plugins are installed and activated.
 */
function graphql_seo_dependencies_not_ready() : array {
	$deps = [];

	if ( ! class_exists( '\WPGraphQL' ) ) {
		$deps[] = 'WPGraphQL';
	}
	if ( ! class_exists( '\RankMath' ) ) {
		$deps[] = 'RankMath SEO';
	}

	return $deps;
}

/**
 * Initializes plugin.
 */
function graphql_seo_init() : void {
	graphql_seo_constants();

	$not_ready = graphql_seo_dependencies_not_ready();

	if ( empty( $not_ready ) && defined( 'WPGRAPHQL_SEO_PLUGIN_DIR' ) ) {
		require_once WPGRAPHQL_SEO_PLUGIN_DIR . 'src/Main.php';
		\WPGraphQL\RankMath\Main::instance();
		return;
	}

	foreach ( $not_ready as $dep ) {
		add_action(
			'admin_notices',
			function() use ( $dep ) {
				?>
				<div class="error notice">
					<p>
						<?php
							printf(
								/* translators: dependency not ready error message */
								esc_html__( '%1$s must be active for WPGraphQL for Rank Math to work.', 'wp-graphql-rank-math' ),
								esc_html( $dep )
							);
						?>
					</p>
				</div>
				<?php
			}
		);
	}
}

add_action( 'graphql_init', 'graphql_seo_init' );
