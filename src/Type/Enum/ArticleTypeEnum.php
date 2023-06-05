<?php
/**
 * The SEO Article Type enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

namespace WPGraphQL\RankMath\Type\Enum;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - Article
 */
class ArticleTypeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'ArticleTypeEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The SEO Article Type', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		return [
			'ARTICLE'      => [
				'description' => __( 'Article.', 'wp-graphql-rank-math' ),
				'value'       => 'Article',
			],
			'BLOG_POST'    => [
				'description' => __( 'Blog post.', 'wp-graphql-rank-math' ),
				'value'       => 'BlogPosting',
			],
			'NEWS_ARTICLE' => [
				'description' => __( 'News article.', 'wp-graphql-rank-math' ),
				'value'       => 'NewsArticle',
			],
		];
	}
}
