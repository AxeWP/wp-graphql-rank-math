<?php
/**
 * Activation Hook
 *
 * @package WPGraphql\PluginName
 */

/**
 * Runs when the plugin is activated.
 */
function graphql_pb_activation_callback() : callable {
	return function() : void {
		do_action( 'graphql_pb_activate' );

		// store the current version of the plugin.
		update_option( 'wp_graphql_pb_version', WPGRAPHQL_PB_VERSION );
	};
}
