<?php
/**
 * The Robots image preview size enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

namespace WPGraphQL\RankMath\Type\Enum;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - ImagePreviewSizeEnum
 */
class ImagePreviewSizeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'ImagePreviewSize';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Robots meta image preview size.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		return [
			'LARGE'    => [
				'description' => __( 'Large', 'wp-graphql-rank-math' ),
				'value'       => 'large',
			],
			'STANDARD' => [
				'description' => __( 'Standard.', 'wp-graphql-rank-math' ),
				'value'       => 'standard',
			],
			'NONE'     => [
				'description' => __( 'Prevents search engines from following links on the pages', 'wp-graphql-rank-math' ),
				'value'       => 'none',
			],
		];
	}
}
