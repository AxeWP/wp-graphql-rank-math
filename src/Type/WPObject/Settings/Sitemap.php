<?php
/**
 * The Rank Math sitemaps settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings;

use WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap\Author;
use WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap\ContentType;
use WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap\General;
use WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap\Taxonomy;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Sitemap
 */
class Sitemap extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Sitemap';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO titles and meta site settings', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'author'          => [
				'type'        => Author::get_type_name(),
				'description' => __( 'Author sitemap settings. Null if authors are not indexable.', 'wp-graphql-rank-math' ),
			],
			'contentTypes'    => [
				'type'        => [ 'list_of' => ContentType::get_type_name() ],
				'args'        => [
					'include' => [
						'type'        => [ 'list_of' => 'ContentTypeEnum' ],
						'description' => __( 'Limit results to specific content types.', 'wp-graphql-rank-math' ),
					],
				],
				'description' => __( 'Content types included in the sitemap.', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source, array $args ) {
					$content_types = $source['contentTypes'];

					if ( ! empty( $args['include'] ) ) {
						$content_types = array_intersect_key( $content_types, array_flip( $args['include'] ) );
					}

					return ! empty( $content_types ) ? $content_types : null;
				},
			],
			'general'         => [
				'type'        => General::get_type_name(),
				'description' => __( 'Sitemap general settings.', 'wp-graphql-rank-math' ),
			],
			'sitemapIndexUrl' => [
				'type'        => 'String',
				'description' => __( 'The URL to the sitemap index.', 'wp-graphql-rank-math' ),
			],
			'taxonomies'      => [
				'type'        => [ 'list_of' => Taxonomy::get_type_name() ],
				'args'        => [
					'include' => [
						'type'        => [ 'list_of' => 'TaxonomyEnum' ],
						'description' => __( 'Limit results to specific taxonomies.', 'wp-graphql-rank-math' ),
					],
				],
				'description' => __( 'Content types included in the sitemap.', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source, array $args ) {
					$taxonomies = $source['taxonomies'];

					if ( ! empty( $args['include'] ) ) {
						$taxonomies = array_intersect_key( $taxonomies, array_flip( $args['include'] ) );
					}

					return ! empty( $taxonomies ) ? $taxonomies : null;
				},
			],
		];
	}
}
