<?php
/**
 * The Rank Math Settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use WPGraphQL\RankMath\Type\WPObject\Settings\General;
use WPGraphQL\RankMath\Type\WPObject\Settings\Meta;
use WPGraphQL\RankMath\Type\WPObject\Settings\Sitemap;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Settings
 */
class Settings extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Settings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO site settings', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'general' => [
				'type'        => General::get_type_name(),
				'description' => __( 'General settings.', 'wp-graphql-rank-math' ),
			],
			'meta'    => [
				'type'        => Meta::get_type_name(),
				'description' => __( 'Meta settings.', 'wp-graphql-rank-math' ),
			],
			'sitemap' => [
				'type'        => Sitemap::get_type_name(),
				'description' => __( 'Sitemap settings.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
