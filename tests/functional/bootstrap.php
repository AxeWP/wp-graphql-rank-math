<?php

// Disable updates to prevent WP from going into maintenance mode while tests run
add_filter( 'enable_maintenance_mode', '__return_false' );
add_filter( 'wp_auto_update_core', '__return_false' );
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );
// enable pretty permalinks.
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%year%/%monthnum%/%postname%/' );
$wp_rewrite->flush_rules(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
