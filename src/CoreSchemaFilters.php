<?php
/**
 * Adds filters that modify core schema.
 *
 * @package WPGraphQL\RankMath
 */

namespace WPGraphQL\RankMath;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Registrable {
	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		add_filter( 'graphql_seo_type_prefix', [ self::class, 'get_type_prefix' ] );
		add_filter( 'graphql_allowed_fields_on_restricted_type', [ self::class, 'allow_seo_on_post_types' ], 10, 2 );

		// Modules.
		Modules\Redirection\CoreSchemaFilters::init();
	}

	/**
	 * Prefixes all plugin GraphQL types.
	 *
	 * @param string $type_name the non-prefixed type name.
	 */
	public static function get_type_prefix( string $type_name = null ): string {
		return ! empty( $type_name ) ? $type_name : 'RankMath';
	}

	/**
	 * Sets seo to return on unauthenticated requests.
	 *
	 * @param string[] $allowed_fields .
	 * @param string   $model_name .
	 *
	 * @return string[]
	 */
	public static function allow_seo_on_post_types( array $allowed_fields, string $model_name ): array {
		if ( 'PostTypeObject' === $model_name ) {
			$allowed_fields[] = 'seo';
		}

		return $allowed_fields;
	}
}
