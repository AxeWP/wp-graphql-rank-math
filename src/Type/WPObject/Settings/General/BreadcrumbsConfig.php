<?php
/**
 * The Breadcrumbs GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\General
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\General;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - BreadcrumbsConfig
 */
class BreadcrumbsConfig extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'BreadcrumbsConfig';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO breadcrumbs settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		$fields = [
			'separator'             => [
				'type'        => 'String',
				'description' => __( 'Separator character or string that appears between breadcrumb items.', 'wp-graphql-rank-math' ),
			],
			'hasHome'               => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to display the homepage breadcrumb in trail.', 'wp-graphql-rank-math' ),
			],
			'homeLabel'             => [
				'type'        => 'String',
				'description' => __( 'Label used for homepage link (first item) in breadcrumbs.', 'wp-graphql-rank-math' ),
			],
			'homeUrl'               => [
				'type'        => 'String',
				'description' => __( 'Link to use for homepage (first item) in breadcrumbs.', 'wp-graphql-rank-math' ),
			],
			'prefix'                => [
				'type'        => 'String',
				'description' => __( 'Prefix for the breadcrumb path.', 'wp-graphql-rank-math' ),
			],
			'archiveFormat'         => [
				'type'        => 'String',
				'description' => __( 'Format the label used for archive pages.', 'wp-graphql-rank-math' ),
			],
			'searchFormat'          => [
				'type'        => 'String',
				'description' => __( 'Format the label used for search results pages.', 'wp-graphql-rank-math' ),
			],
			'notFoundLabel'         => [
				'type'        => 'String',
				'description' => __( 'Label used for 404 error item in breadcrumbs.', 'wp-graphql-rank-math' ),
			],
			'hasPostTitle'          => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the post title is visible in the breadcrumbs.', 'wp-graphql-rank-math' ),
			],
			'hasAncestorCategories' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to show all ancestor categories, if a category is a child category.', 'wp-graphql-rank-math' ),
			],
			'hasTaxonomyName'       => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the taxonomy name is visible in the breadcrumbs.', 'wp-graphql-rank-math' ),
			],
		];

		if ( 'page' === get_option( 'show_on_front' ) && 0 < get_option( 'page_for_posts' ) ) {
			$fields['hasBlogPage'] = [
				'type'        => 'Boolean',
				'description' => __( 'Whether the Blog page is visible in the breadcrumbs. Only relevant if you have a Posts page set.', 'wp-graphql-rank-math' ),
			];
		}

		return $fields;
	}
}
