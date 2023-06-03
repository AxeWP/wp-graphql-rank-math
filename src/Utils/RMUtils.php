<?php
/**
 * Utility functions for interacting with RankMath SEO.
 *
 * @package WPGraphQL\RankMath\Utils
 * @since @todo
 */

namespace WPGraphQL\RankMath\Utils;

use RankMath\Helper;

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
}
