<?php
/**
 * Interface for Social Seo fields.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use WPGraphQL\RankMath\Type\WPObject\UserSocialMeta;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;

/**
 * Class - SocialSeo
 */
class SocialSeo extends InterfaceType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'SocialSeo';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The social seo data.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'social' => [
				'type'        => UserSocialMeta::get_type_name(),
				'description' => __( 'The social meta properties.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
