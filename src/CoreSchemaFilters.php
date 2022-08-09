<?php
/**
 * Adds filters that modify core schema.
 *
 * @package WPGraphQL\RankMath
 */

namespace WPGraphQL\RankMath;

use AxeWP\GraphQL\Interfaces\Registrable;
use WPGraphQL\RankMath\Model\ContentTypeSeo;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Registrable {
	/**
	 * {@inheritDoc}
	 */
	public static function init() : void {
		add_filter( 'graphql_pb_type_prefix', [ __CLASS__, 'get_type_prefix' ] );
		add_filter( 'graphql_allowed_fields_on_restricted_type', [ __CLASS__, 'allow_seo_on_post_types' ], 10, 2 );
		add_filter( 'graphql_model_prepare_fields', [ __CLASS__, 'add_seo_to_model' ], 10, 3 );
	}

	/**
	 * Prefixes all plugin GraphQL types.
	 *
	 * @param string $type_name the non-prefixed type name.
	 */
	public static function get_type_prefix( string $type_name = null ) : string {
		return ! empty( $type_name ) ? $type_name : 'RankMath';
	}

	/**
	 * Sets seo to return on unauthenticated requests.
	 *
	 * @param string[] $allowed_fields .
	 * @param string   $model_name .
	 */
	public static function allow_seo_on_post_types( array $allowed_fields, string $model_name ) : array {
		if ( 'PostTypeObject' === $model_name ) {
			$allowed_fields[] = 'seo';
		}

		return $allowed_fields;
	}

	/**
	 * Add seo to content model, so it can be surfaced by unauthenticated posts.
	 *
	 * @param array  $fields .
	 * @param string $model_name .
	 * @param mixed  $data .
	 */
	public static function add_seo_to_model( array $fields, string $model_name, $data ) : array {
		if ( 'PostTypeObject' === $model_name ) {
			$fields['seo'] = function() use ( $data ) {
				$link = get_post_type_archive_link( $data->name );

				return ! empty( $link ) ? new ContentTypeSeo( $data->name ) : null;
			};
		}

		return $fields;
	}
}
