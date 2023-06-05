<?php
/**
 * The SEO Score Template Type enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

namespace WPGraphQL\RankMath\Type\Enum;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - OpenGraphProductAvailabilityEnum
 */
class OpenGraphProductAvailabilityEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'OpenGraphProductAvailabilityEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The OpenGraph Product availibility', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		return [
			'IN_STOCK'     => [
				'description' => __( 'The product is in stock', 'wp-graphql-rank-math' ),
				'value'       => 'instock',
			],
			'OUT_OF_STOCK' => [
				'description' => __( 'The product is out of stock', 'wp-graphql-rank-math' ),
				'value'       => '',
			],
		];
	}
}
