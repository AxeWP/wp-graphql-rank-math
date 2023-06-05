<?php
/**
 * The Snippet type enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

namespace WPGraphQL\RankMath\Type\Enum;

use RankMath\OpenGraph\Facebook_Locale;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;
use WPGraphQL\Type\WPEnumType;

/**
 * Class - OpenGraphLocaleEnum
 */
class OpenGraphLocaleEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'OpenGraphLocaleEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Facebook OpenGraph Locale.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		$types = Facebook_Locale::FACEBOOK_LOCALES;

		$values = [];

		foreach ( $types as $locale ) {
			$values[ WPEnumType::get_safe_name( $locale ) ] = [
				// translators: %s is the locale.
				'description' => sprintf( __( '%s.', 'wp-graphql-rank-math' ), $locale ),
				'value'       => $locale,
			];
		}

		return $values;
	}
}
