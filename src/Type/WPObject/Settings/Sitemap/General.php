<?php
/**
 * The General sitemap GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - General
 */
class General extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'SitemapGeneralSettings';
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
			'canPingSearchEngines'    => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to notify search engines when the sitemap is updated.', 'wp-graphql-rank-math' ),
			],
			'excludedPostDatabaseIds' => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'A list of post IDs excluded from the sitemap. This option **applies** to all posts types including posts, pages, and custom post types.', 'wp-graphql-rank-math' ),
			],
			'excludedTermDatabaseIds' => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'A list of term IDs excluded from the sitemap. This option **applies** to all taxonomies.', 'wp-graphql-rank-math' ),
			],
			'hasFeaturedImage'        => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the Featured Image is included in sitemaps too, even if it does not appear directly in the post content.', 'wp-graphql-rank-math' ),
			],
			'hasImages'               => [
				'type'        => 'Boolean',
				'description' => __( 'Whether reference to images from the post content is included in sitemaps.', 'wp-graphql-rank-math' ),
			],
			'linksPerSitemap'         => [
				'type'        => 'Int',
				'description' => __( 'Max number of links on each sitemap page.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
