<?php
/**
 * The Rank Math UserSocialMeta GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - UserSocialMeta
 */
class UserSocialMeta extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'UserSocialMeta';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO User Social data', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'facebookProfileUrl' => [
				'type'        => 'String',
				'description' => __( 'The complete Facebook profile URL.', 'wp-graphql-rank-math' ),
			],
			'twitterUserName'    => [
				'type'        => 'String',
				'description' => __( 'Twitter Username of the user.', 'wp-graphql-rank-math' ),
			],
			'additionalProfiles' => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'A list of additional social profiles.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
