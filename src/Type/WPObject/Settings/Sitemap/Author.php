<?php
/**
 * The Author sitemap GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap;

use AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Author
 */
class Author extends ObjectType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'SitemapAuthorSettings';
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
			'excludedRoles'           => [
				'type'        => [ 'list_of' => 'UserRoleEnum' ],
				'description' => __( 'List of user roles excluded from the sitemap.', 'wp-graphql-rank-math' ),
			],
			'excludedUserDatabaseIds' => [
				'type'        => [ 'list_of' => 'Int' ],
				'description' => __( 'List of user IDs excluded from the sitemap.', 'wp-graphql-rank-math' ),
			],
			'sitemapUrl'              => [
				'type'        => 'String',
				'description' => __( 'The sitemap URL.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
