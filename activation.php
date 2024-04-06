<?php
/**
 * Activation Hook
 *
 * @package WPGraphql\RankMath
 */

namespace WPGraphQL\RankMath;

/**
 * Runs when the plugin is activated.
 */
function activation_callback(): callable {
	return static function (): void {
		do_action( 'graphql_seo_activate' );

		// store the current version of the plugin.
		update_option( 'wp_graphql_seo_version', WPGRAPHQL_SEO_VERSION );
	};
}
