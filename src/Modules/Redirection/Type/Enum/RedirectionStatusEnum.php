<?php
/**
 * The Redirection status enum.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\Enum
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\Enum;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - RedirectionStatusEnum
 */
class RedirectionStatusEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RedirectionStatusEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Redirection status.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		return [
			'ACTIVE'   => [
				'description' => __( 'Active.', 'wp-graphql-rank-math' ),
				'value'       => 'active',
			],
			'INACTIVE' => [
				'description' => __( 'Inactive.', 'wp-graphql-rank-math' ),
				'value'       => 'inactive',
			],
			'TRASH'    => [
				'description' => __( 'Trashed.', 'wp-graphql-rank-math' ),
				'value'       => 'trash',
			],
		];
	}
}
