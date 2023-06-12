<?php
/**
 * Registers the connections to Redirection
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Connection
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use WPGraphQL\RankMath\Modules\Redirection\Data\Connection\RedirectionConnectionResolver;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionStatusEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Input\RedirectionConnectionOrderbyInput;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ConnectionType;

/**
 * Class - RedirectionConnection
 */
class RedirectionConnection extends ConnectionType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Redirection';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register(): void {
		/** @var array $config */
		$config = self::get_connection_config(
			[
				'fromType'       => 'RootQuery',
				'fromFieldName'  => 'redirections',
				'connectionArgs' => self::get_connection_args(),
				'description'    => __( 'A RankMath SEO redirection object.', 'wp-graphql-rank-math' ),
				'resolve'        => static function ( $source, array $args, AppContext $context, ResolveInfo $info ) {
					$resolver = new RedirectionConnectionResolver( $source, $args, $context, $info );

					return $resolver->get_connection();
				},
			]
		);

		register_graphql_connection( $config );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function connection_args(): array {
		return [
			'search'  => [
				'type'        => 'String',
				'description' => __( 'Search the status, redirection url, or sources for the provided value.', 'wp-graphql-rank-math' ),
			],
			'status'  => [
				'type'        => [ 'list_of' => RedirectionStatusEnum::get_type_name() ],
				'description' => __( 'Filter the redirections by their status.', 'wp-graphql-rank-math' ),
			],
			'orderby' => [
				'type'        => RedirectionConnectionOrderbyInput::get_type_name(),
				'description' => __( 'Order the results by a specific field.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
