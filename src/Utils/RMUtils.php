<?php
/**
 * Utility functions for interacting with RankMath SEO.
 *
 * @package WPGraphQL\RankMath\Utils
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Utils;

use MyThemeShop\Database\Database;
use RankMath\Helper;
use RankMath\Redirections\DB;

/**
 * Class - RMUtils
 */
class RMUtils {
	/**
	 * Check if a module is active.
	 *
	 * @see \RankMath\Helper::is_module_active()
	 *
	 * @param string $module The module name.
	 *
	 * @since 0.0.13
	 */
	public static function is_module_active( string $module ): bool {
		return Helper::is_module_active( $module );
	}

	/**
	 * Check if a user has a RankMath capability.
	 *
	 * @see \RankMath\Helper::has_cap()
	 *
	 * @param string $capability The capability name.
	 *
	 * @since 0.0.13
	 */
	public static function has_cap( string $capability ): bool {
		return Helper::has_cap( $capability );
	}

	/**
	 * Get the redirections table.
	 *
	 * @see \RankMath\Redirections\DB\table()
	 *
	 * @since 0.0.13
	 *
	 * @return \MyThemeShop\Database\Query_Builder
	 */
	public static function get_redirections_table() {
		/** 
		 * Query_Builder gives us the methods we need to interact with.
		 *
		 * @var \MyThemeShop\Database\Query_Builder
		 */
		return Database::table( 'rank_math_redirections' );
	}

	/**
	 * Get the redirections from the database.
	 *
	 * @see \RankMath\Redirections\DB\get_redirections()
	 *
	 * @param array<string,mixed> $args The arguments to filter the redirections with.
	 *
	 * @since 0.0.13
	 *
	 * @return array<string,mixed>
	 */
	public static function get_redirections( array $args = [] ): array {
		return DB::get_redirections( $args );
	}

	/**
	 * Get a redirection by its ID.
	 *
	 * @see \RankMath\Redirections\DB\get_redirection_by_id()
	 *
	 * @param int    $id     ID of the record to search for.
	 * @param string $status Status to filter with.
	 *
	 * @since 0.0.13
	 *
	 * @return bool|array<string,mixed>
	 */
	public static function get_redirection_by_id( int $id, string $status = 'all' ) {
		return DB::get_redirection_by_id( $id, $status );
	}

	/**
	 * Gets a redirection directly from the database.
	 *
	 * This allows us to check for redirections without having to reset the existing DB::$table.
	 *
	 * @see https://support.rankmath.com/ticket/adding-where-clause-to-redirection-query-overwrites-existing-query
	 *
	 * @param int $id ID of the redirection to get.
	 *
	 * @since 0.0.13
	 *
	 * @return array<string,mixed>|null
	 */
	public static function get_redirection_from_db( int $id ) {
		$result = wp_cache_get( 'rm_redirection_' . $id, 'rm_redirections' );

		if ( false === $result ) {
			global $wpdb;

			$result = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}rank_math_redirections WHERE id = %d LIMIT 1",
					$id
				),
				ARRAY_A
			);

			wp_cache_set( 'rm_redirection_' . $id, $result, 'rm_redirections' );
		}

		return ! empty( $result ) ? $result : null;
	}
}
