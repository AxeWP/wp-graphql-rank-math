<?php
/**
 * The SEO Score Position enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

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
				'description' => __( 'Below content', 'wp-graphql-rank-math' ),
				'value'       => 'bottom',
			],
			'TOP'    => [
				'description' => __( 'Above content', 'wp-graphql-rank-math' ),
				'value'       => 'top',
			],
			'BOTH'   => [
				'description' => __( 'Above & below content', 'wp-graphql-rank-math' ),
				'value'       => 'both',
			],
			'CUSTOM' => [
				'description' => __( 'Custom (use shortcode)', 'wp-graphql-rank-math' ),
				'value'       => 'custom',
			],
		];
	}
}
