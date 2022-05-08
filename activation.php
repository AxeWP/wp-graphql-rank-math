<?php
/**
 * Activation Hook
 *
 * @package WPGraphql\RankMath
 */

/**
 * Runs when the plugin is activated.
 */
function graphql_seo_activation_callback() : callable {
	return function() : void {
		do_action( 'graphql_seo_activate' );

		// store the current version of the plugin.
		update_option( 'wp_graphql_seo_version', WPGRAPHQL_SEO_VERSION );
	};
}
