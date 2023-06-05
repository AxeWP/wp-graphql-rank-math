<?php
/**
 * The Author sitemap GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap;

use WPGraphQL\Data\Connection\UserConnectionResolver;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithConnections;

/**
 * Class - Author
 */
class Author extends ObjectType implements TypeWithConnections {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'SitemapAuthorSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_connections(): array {
		return [
			'connectedAuthors' => [
				'toType'      => 'User',
				'description' => __( 'The connected authors whose URLs are included in the sitemap', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source, $args, $context, $info ) {
					$resolver = new UserConnectionResolver( $source, $args, $context, $info );

					if ( ! empty( $source->excludedRoles ) ) {
						$resolver->set_query_arg( 'role__not_in', $source->excludedRoles );
					}

					if ( ! empty( $source->excludedUserDatabaseIds ) ) {
						$resolver->set_query_arg( 'exclude', $source->excludedUserDatabaseIds );
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
