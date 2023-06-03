<?php
/**
 * The AuthorArchiveMeta GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Meta
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Meta;

use WPGraphQL\RankMath\Type\WPInterface\MetaSettingWithArchive;
use WPGraphQL\RankMath\Type\WPInterface\MetaSettingWithRobots;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - AuthorArchiveMeta
 */
class AuthorArchiveMeta extends ObjectType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'AuthorArchiveMetaSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Author Archive meta settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			MetaSettingWithArchive::get_type_name(),
			MetaSettingWithRobots::get_type_name(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'baseSlug'                => [
				'type'        => 'String',
				'description' => __( 'Change the `/author/` part in author archive URLs.', 'wp-graphql-rank-math' ),
			],
			'hasArchives'             => [
				'type'        => 'Boolean',
				'description' => __( 'Whether author archives are enabled.', 'wp-graphql-rank-math' ),
			],
			'hasCustomRobotsMeta'     => [
				'type'        => 'Boolean',
				'description' => __( 'Whether custom robots meta for author page are set. Otherwise the default meta will be used, as set in the Global Meta tab.', 'wp-graphql-rank-math' ),
			],
			'hasSlackEnhancedSharing' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to show additional information (name & total number of posts) when an author archive is shared on Slack.', 'wp-graphql-rank-math' ),
			],
			'hasSeoControls'          => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the SEO Controls meta box for user profile pages is enabled.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
