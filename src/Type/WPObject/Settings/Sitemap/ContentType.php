<?php
/**
 * The ContentType sitemap GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap;

use RankMath\Helper;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithConnections;

/**
 * Class - ContentType
 */
class ContentType extends ObjectType implements TypeWithConnections {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'SitemapContentTypeSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_connections(): array {
		return [
			'connectedContentNodes' => [
				'toType'      => 'ContentNode',
				'description' => __( 'The connected authors whose URLs are included in the sitemap', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source, $args, $context, $info ) {
					if ( empty( $source['isInSitemap'] ) ) {
						return null;
					}

					$resolver = new PostObjectConnectionResolver( $source, $args, $context, $info, $source['type'] );

					$excluded_post_ids = Helper::get_settings( 'sitemap.exclude_posts' );

					if ( ! empty( $excluded_post_ids ) ) {
						$resolver->set_query_arg( 'post__not_in', $excluded_post_ids );
					}

					return $resolver->get_connection();
				},
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Sitemap general settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'customImageMetaKeys' => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'List of custom field (post meta) names which contain image URLs to include them in the sitemaps.', 'wp-graphql-rank-math' ),
			],
			'isInSitemap'         => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the content type is included in the sitemap.', 'wp-graphql-rank-math' ),
			],
			'sitemapUrl'          => [
				'type'        => 'String',
				'description' => __( 'The sitemap URL.', 'wp-graphql-rank-math' ),
			],
			'type'                => [
				'type'        => 'ContentTypeEnum',
				'description' => __( 'The content type.', 'wp-graphql-rank-math' ),
			],
			
		];
	}
}
