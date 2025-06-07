<?php
/**
 * The SEO Score Position enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Type\Enum;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - SeoScorePositionEnum
 */
class SeoScorePositionEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'SeoScorePositionEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The frontend SEO Score position', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		return [
			'BOTTOM' => [
				'description' => static fn () => __( 'Below content', 'wp-graphql-rank-math' ),
				'value'       => 'bottom',
			],
			'TOP'    => [
				'description' => static fn () => __( 'Above content', 'wp-graphql-rank-math' ),
				'value'       => 'top',
			],
			'BOTH'   => [
				'description' => static fn () => __( 'Above & below content', 'wp-graphql-rank-math' ),
				'value'       => 'both',
			],
			'CUSTOM' => [
				'description' => static fn () => __( 'Custom (use shortcode)', 'wp-graphql-rank-math' ),
				'value'       => 'custom',
			],
		];
	}
}
