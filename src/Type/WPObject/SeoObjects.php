<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 * @since 0.0.8
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Type\WPObject;

use WPGraphQL;
use WPGraphQL\RankMath\Type\WPInterface\ContentNodeSeo;
use WPGraphQL\RankMath\Type\WPInterface\Seo;
use WPGraphQL\RankMath\Utils\Utils;
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
			$type_name_for_post_object = 'RankMath' . graphql_format_type_name( $post_type->graphql_single_name . 'ObjectSeo' );
			// Register Post Object seo.
			register_graphql_object_type(
				$type_name_for_post_object,
				[
					// translators: %s is the post type name.
					'description'     => sprintf( __( 'The %s post object SEO data', 'wp-graphql-rank-math' ), $post_type->name ),
					'interfaces'      => [ ContentNodeSeo::get_type_name() ],
					'fields'          => [],
					'eagerlyLoadType' => true,
				]
			);

			// Register Post Object's SEO field.
			Utils::overload_graphql_field_type( $post_type->graphql_single_name, 'seo', $type_name_for_post_object );

			// Register Post Type seo.
			$type_name_for_post_type = 'RankMath' . graphql_format_type_name( $post_type->graphql_single_name . 'TypeSeo' );
			register_graphql_object_type(
				$type_name_for_post_type,
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
			$type_name_for_term = 'RankMath' . graphql_format_type_name( $taxonomy->graphql_single_name . 'TermSeo' );
			register_graphql_object_type(
				$type_name_for_term,
				[
					// translators: %s is the tax term name.
					'description'     => sprintf( __( 'The %s term object SEO data', 'wp-graphql-rank-math' ), $taxonomy->name ),
					'interfaces'      => [ Seo::get_type_name() ],
					'fields'          => [],
					'eagerlyLoadType' => true,
				]
			);

			// Register Term Object's SEO field.
			Utils::overload_graphql_field_type( $taxonomy->graphql_single_name, 'seo', $type_name_for_term );
		}

		// Register user object seo.
		$type_name_for_user = 'RankMathUserSeo';
		register_graphql_object_type(
			$type_name_for_user,
			[
				'description'     => __( 'The user object SEO data', 'wp-graphql-rank-math' ),
				'interfaces'      => [ Seo::get_type_name() ],
				'fields'          => [
					'facebookProfileUrl' => [
						'type'        => 'String',
						'description' => __( 'The complete Facebook profile URL.', 'wp-graphql-rank-math' ),
					],
					'twitterUserName'    => [
						'type'        => 'String',
						'description' => __( 'Twitter Username of the user.', 'wp-graphql-rank-math' ),
					],
					'additionalProfiles' => [
						'type'        => [ 'list_of' => 'String' ],
						'description' => __( 'Additional social profile URLs to add to the sameAs property.', 'wp-graphql-rank-math' ),
					],
				],
				'eagerlyLoadType' => true,
			]
		);

		// Register User Object's SEO field.
		Utils::overload_graphql_field_type( 'User', 'seo', $type_name_for_user );
	}
}
