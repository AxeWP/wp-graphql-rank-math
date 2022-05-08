<?php
/**
 * The SEO Twitter Card Type enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

namespace WPGraphQL\RankMath\Type\Enum;

use AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - TwitterCardTypeEnum
 */
class TwitterCardTypeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'TwitterCardTypeEnum';
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
			'SUMMARY'             => [
				'description' => __( 'Summary Card.', 'wp-graphql-rank-math' ),
				'value'       => 'summary_card',
			],
			'SUMMARY_LARGE_IMAGE' => [
				'description' => __( 'Summary Card with Large Image.', 'wp-graphql-rank-math' ),
				'value'       => 'summary_large_image',
			],
		];
	}
}
