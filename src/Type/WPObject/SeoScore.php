<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use WPGraphQL\RankMath\Type\Enum\SeoRatingEnum;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - SeoScore
 */
class SeoScore extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'SeoScore';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Seo score information.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'badgeHtml'        => [
				'type'        => 'String',
				'description' => __( 'The html output for the Frontend SEO badge', 'wp-graphql-rank-math' ),
			],
			'hasFrontendScore' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the SEO score should be displayed on the frontend', 'wp-graphql-rank-math' ),
			],
			'score'            => [
				'type'        => 'Integer',
				'description' => __( 'The SEO score', 'wp-graphql-rank-math' ),
			],
			'rating'           => [
				'type'        => SeoRatingEnum::get_type_name(),
				'description' => __( 'The SEO score', 'wp-graphql-rank-math' ),
			],
		];
	}
}
