<?php
/**
 * The Redirection Settings GraphQL object.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\WPObject
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\WPObject;

use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionBehaviorEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionTypeEnum;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - RedirectionSettings
 */
class RedirectionSettings extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RedirectionSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Redirection settings', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'fallbackBehavior'    => [
				'type'        => RedirectionBehaviorEnum::get_type_name(),
				'description' => __( 'The fallback redirection behavior', 'wp-graphql-rank-math' ),
			],
			'fallbackCustomUrl'   => [
				'type'        => 'String',
				'description' => __( 'The custom redirection URL to use as a fallback. Only set if `fallbackBehavior` is `CUSTOM`.', 'wp-graphql-rank-math' ),
			],
			'hasAutoPostRedirect' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the auto post redirection is enabled.', 'wp-graphql-rank-math' ),
			],
			'hasDebug'            => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the redirection Debug console is enabled.', 'wp-graphql-rank-math' ),
			],
			'redirectionType'     => [
				'type'        => RedirectionTypeEnum::get_type_name(),
				'description' => __( 'The redirection type.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
