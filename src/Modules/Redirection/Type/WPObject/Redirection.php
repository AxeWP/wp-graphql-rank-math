<?php
/**
 * The Redirections GraphQL object.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\WPObject
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\WPObject;

use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionStatusEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionTypeEnum;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - Redirection
 */
class Redirection extends ObjectType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Redirection';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Redirection object.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'dateCreated'         => [
				'type'        => 'String',
				'description' => __( 'The date the redirection was created.', 'wp-graphql-rank-math' ),
			],
			'dateCreatedGmt'      => [
				'type'        => 'String',
				'description' => __( 'The GMT date the redirection was created.', 'wp-graphql-rank-math' ),
			],
			'dateModified'        => [
				'type'        => 'String',
				'description' => __( 'The date the redirection was last modified.', 'wp-graphql-rank-math' ),
			],
			'dateModifiedGmt'     => [
				'type'        => 'String',
				'description' => __( 'The GMT date the redirection was last modified.', 'wp-graphql-rank-math' ),
			],
			'dateLastAccessed'    => [
				'type'        => 'String',
				'description' => __( 'The date the redirection was last accessed.', 'wp-graphql-rank-math' ),
			],
			'dateLastAccessedGmt' => [
				'type'        => 'String',
				'description' => __( 'The GMT date the redirection was last accessed.', 'wp-graphql-rank-math' ),
			],
			'hits'                => [
				'type'        => 'Int',
				'description' => __( 'The number of hits for this redirection.', 'wp-graphql-rank-math' ),
			],
			'redirectToUrl'       => [
				'type'        => 'String',
				'description' => __( 'The URL to redirect to.', 'wp-graphql-rank-math' ),
			],
			'sources'             => [
				'type'        => [ 'list_of' => RedirectionSource::get_type_name() ],
				'description' => __( 'The sources of the redirection.', 'wp-graphql-rank-math' ),
			],
			'status'              => [
				'type'        => RedirectionStatusEnum::get_type_name(),
				'description' => __( 'The status of the redirection.', 'wp-graphql-rank-math' ),
			],
			'type'                => [
				'type'        => RedirectionTypeEnum::get_type_name(),
				'description' => __( 'The redirection type (HTTP status code).', 'wp-graphql-rank-math' ),
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			'Node',
			'DatabaseIdentifier',
		];
	}
}
