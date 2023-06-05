<?php
/**
 * The Rank Math titles and meta settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings;

use WPGraphQL\RankMath\Type\WPObject\Settings\Meta\AuthorArchiveMeta;
use WPGraphQL\RankMath\Type\WPObject\Settings\Meta\ContentTypeMeta;
use WPGraphQL\RankMath\Type\WPObject\Settings\Meta\DateArchiveMeta;
use WPGraphQL\RankMath\Type\WPObject\Settings\Meta\GlobalMeta;
use WPGraphQL\RankMath\Type\WPObject\Settings\Meta\HomepageMeta;
use WPGraphQL\RankMath\Type\WPObject\Settings\Meta\LocalMeta;
use WPGraphQL\RankMath\Type\WPObject\Settings\Meta\SocialMeta;
use WPGraphQL\RankMath\Type\WPObject\Settings\Meta\TaxonomyMeta;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Meta
 */
class Meta extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Meta';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO titles and meta site settings', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'authorArchives'               => [
				'type'        => AuthorArchiveMeta::get_type_name(),
				'description' => __( 'Author Archive settings.', 'wp-graphql-rank-math' ),
			],
			'contentTypes'                 => [
				'type'        => ContentTypeMeta::get_type_name(),
				'description' => __( 'Content type settings.', 'wp-graphql-rank-math' ),
			],
			'taxonomies'                   => [
				'type'        => TaxonomyMeta::get_type_name(),
				'description' => __( 'Taxonomy settings.', 'wp-graphql-rank-math' ),
			],
			'dateArchives'                 => [
				'type'        => DateArchiveMeta::get_type_name(),
				'description' => __( 'Date Archive settings.', 'wp-graphql-rank-math' ),
			],
			'global'                       => [
				'type'        => GlobalMeta::get_type_name(),
				'description' => __( 'Global settings.', 'wp-graphql-rank-math' ),
			],
			'local'                        => [
				'type'        => LocalMeta::get_type_name(),
				'description' => __( 'Local settings.', 'wp-graphql-rank-math' ),
			],
			'social'                       => [
				'type'        => SocialMeta::get_type_name(),
				'description' => __( 'Social settings.', 'wp-graphql-rank-math' ),
			],
			'homepage'                     => [
				'type'        => HomepageMeta::get_type_name(),
				'description' => __( 'Homepage settings. Only used is the Homepage is set to display a list of posts.', 'wp-graphql-rank-math' ),
			],
			'notFoundTitle'                => [
				'type'        => 'String',
				'description' => __( 'Title tag on 404 Not Found error page.', 'wp-graphql-rank-math' ),
			],
			'searchTitle'                  => [
				'type'        => 'String',
				'description' => __( 'Title tag on search results page.', 'wp-graphql-rank-math' ),
			],
			'shouldIndexSearch'            => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to index search result pages.', 'wp-graphql-rank-math' ),
			],
			'shouldIndexPaginatedPages'    => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to index /page/2 and further of any archive.', 'wp-graphql-rank-math' ),
			],
			'shouldIndexArchiveSubpages'   => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to index paginated archive pages from getting.', 'wp-graphql-rank-math' ),
			],
			'shouldIndexPasswordProtected' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to index password protected pages and posts.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
