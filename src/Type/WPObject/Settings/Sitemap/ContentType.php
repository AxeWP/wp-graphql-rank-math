<?php
/**
 * The ContentType sitemap GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap;

use AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - ContentType
 */
class ContentType extends ObjectType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'SitemapContentTypeSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'The RankMath SEO Sitemap general settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
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
