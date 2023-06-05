<?php
/**
 * Utility functions for interacting with RankMath SEO.
 *
 * @package WPGraphQL\RankMath\Utils
 * @since @todo
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
	 * @since @todo
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
	 * @since @todo
	 */
	public static function has_cap( string $capability ): bool {
		return Helper::has_cap( $capability );
	}

	/**
	 * Get the redirections table.
	 *
	 * @see \RankMath\Redirections\DB\table()
	 *
	 * @since @todo
	 *
	 * @return \MyThemeShop\Database\Query_Builder
	 */
	public static function get_redirections_table() {
		return Database::table( 'rank_math_redirections' );
	}

	/**
	 * Get the redirections from the database.
	 *
	 * @see \RankMath\Redirections\DB\get_redirections()
	 *
	 * @param array $args The arguments to filter the redirections with.
	 *
	 * @since @todo
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
	 * @return bool|array
	 */
	public static function get_redirection_by_id( int $id, string $status = 'all' ) {
		return DB::get_redirection_by_id( $id, $status );
	}
}
