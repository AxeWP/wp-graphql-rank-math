<?php
/**
 * The FrontendSeoScore GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\General
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\General;

use WPGraphQL\RankMath\Type\Enum\SeoScorePositionEnum;
use WPGraphQL\RankMath\Type\Enum\SeoScoreTemplateTypeEnum;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - FrontendSeoScore
 */
class FrontendSeoScore extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'FrontendSeoScore';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'RankMath Frontend SEO Score settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'enabledPostTypes'    => [
				'type'        => [ 'list_of' => 'ContentTypeEnum' ],
				'description' => __( 'The list of post types which should display the calculated SEO score.', 'wp-graphql-rank-math' ),
			],
			'template'            => [
				'type'        => SeoScoreTemplateTypeEnum::get_type_name(),
				'description' => __( 'The list of post types which should display the calculated SEO score.', 'wp-graphql-rank-math' ),
			],
			'position'            => [
				'type'        => SeoScorePositionEnum::get_type_name(),
				'description' => __( 'Where the SEO score badges should be displayed automatically, or if the `[rank_math_seo_score]` shortcode is used instead.', 'wp-graphql-rank-math' ),
			],
			'hasRankMathBacklink' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to insert a backlink to RankMath.com to show your support, if you are showing the SEO scores on the front end.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
