<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 * @since 0.0.8
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use WPGraphQL;
use WPGraphQL\RankMath\Type\WPInterface\ContentNodeSeo;
use WPGraphQL\RankMath\Type\WPInterface\Seo;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\Registrable;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeNameTrait;

/**
 * Class - SeoObjects
 */
class SeoObjects implements Registrable {
	use TypeNameTrait;

	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		add_action( 'graphql_register_types', [ static::class, 'register' ] );
	}

	/**
	 * Registers the SEO GraphQL objects to the schema.
	 */
	public static function register(): void {
		$post_types = WPGraphQL::get_allowed_post_types( 'objects' );

		foreach ( $post_types as $post_type ) {
			/** @var \WP_Post_Type $post_type */
			// Register Post Objects seo.
			register_graphql_object_type(
				'RankMath' . graphql_format_type_name( $post_type->graphql_single_name . 'ObjectSeo' ),
				[
					// translators: %s is the post type name.
					'description'     => sprintf( __( 'The %s post object SEO data', 'wp-graphql-rank-math' ), $post_type->name ),
					'interfaces'      => [ ContentNodeSeo::get_type_name() ],
					'fields'          => [],
					'eagerlyLoadType' => true,
				]
			);

			// Register Post Type seo.
			register_graphql_object_type(
				'RankMath' . graphql_format_type_name( $post_type->graphql_single_name . 'TypeSeo' ),
				[
					// translators: %s is the post type name.
					'description'     => sprintf( __( 'The %s post type object SEO data', 'wp-graphql-rank-math' ), $post_type->name ),
					'interfaces'      => [ Seo::get_type_name() ],
					'fields'          => [],
					'eagerlyLoadType' => true,
				]
			);
		}

		// Register term objects seo.
		$taxonomies = WPGraphQL::get_allowed_taxonomies( 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			/** @var \WP_Taxonomy $taxonomy */
			$name = 'RankMath' . graphql_format_type_name( $taxonomy->graphql_single_name . 'TermSeo' );
			register_graphql_object_type(
				$name,
				[
					// translators: %s is the tax term name.
					'description'     => sprintf( __( 'The %s term object SEO data', 'wp-graphql-rank-math' ), $taxonomy->name ),
					'interfaces'      => [ Seo::get_type_name() ],
					'fields'          => [],
					'eagerlyLoadType' => true,
				]
			);
		}

		// Register user object seo.
		register_graphql_object_type(
			graphql_format_type_name( 'RankMathUserSeo' ),
			[
				'description'     => __( 'The user object SEO data', 'wp-graphql-rank-math' ),
				'interfaces'      => [ Seo::get_type_name() ],
				'fields'          => [],
				'eagerlyLoadType' => true,
			]
		);
	}
}
