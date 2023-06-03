<?php
/**
 * Helper methods.
 *
 * @package WPGraphQL\RankMath\Utils\Utils;
 */

namespace WPGraphQL\RankMath\Utils;

/**
 * Class - Utils
 */
class Utils {
	/**
	 * Truncate text for given length.
	 *
	 * @param string $str    Text to truncate.
	 * @param int    $length Length to truncate for.
	 * @param string $append Append to the end if string is truncated.
	 */
	public static function truncate( string $str, int $length = 110, string $append = '' ): string {
		$str     = wp_strip_all_tags( $str, true );
		$strlen  = mb_strlen( $str );
		$excerpt = mb_substr( $str, 0, $length );

		// Remove part of an entity at the end.
		$excerpt = preg_replace( '/&[^;\s]{0,6}$/', '', $excerpt );
		if ( $str !== $excerpt ) {
			$excerpt = mb_substr( $str, 0, (int) mb_strrpos( trim( (string) $excerpt ), ' ' ) );
		}

		if ( $strlen > $length ) {
			$excerpt .= $append;
		}

		return $excerpt;
	}

	/**
	 * Checks if a given url is relative.
	 *
	 * @param string $url the url to check.
	 */
	public static function is_url_relative( string $url ): bool {
		return ( 0 !== strpos( $url, 'http' ) && 0 !== strpos( $url, '//' ) );
	}

	/**
	 * Appends the base url to the provided path.
	 *
	 * @param string $path .
	 */
	public static function base_url( string $path = '' ): string {
		$blog_id = is_multisite() ? get_current_blog_id() : null;

		$base_url = get_home_url( $blog_id, $path );

		return user_trailingslashit( $base_url );
	}
}
