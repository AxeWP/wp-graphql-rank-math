<?php
/**
 * The Redirection default Behavior enum.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\Enum
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\Enum;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - RedirectionBehaviorEnum
 */
class RedirectionBehaviorEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RedirectionBehaviorEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Redirection behavior.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		return [
			'DEFAULT'  => [
				'description' => __( 'Redirect to default 404.', 'wp-graphql-rank-math' ),
				'value'       => 'default',
			],
			'HOMEPAGE' => [
				'description' => __( 'Redirect to Home page.', 'wp-graphql-rank-math' ),
				'value'       => 'homepage',
			],
			'CUSTOM'   => [
				'description' => __( 'Redirect to custom URL.', 'wp-graphql-rank-math' ),
				'value'       => 'custom',
			],
		];
	}
}
