<?php
/**
 * The Redirection Source GraphQL object.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\WPObject
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\WPObject;

use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionComparisonTypeEnum;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - RedirectionSource
 */
class RedirectionSource extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RedirectionSource';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Redirection source to match.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'ignore'     => [
				'type'        => 'String',
				'description' => __( 'The ignore flag to use when matching the pattern.', 'wp-graphql-rank-math' ),
			],
			'pattern'    => [
				'type'        => 'String',
				'description' => __( 'The pattern to match.', 'wp-graphql-rank-math' ),
			],
			'comparison' => [
				'type'        => RedirectionComparisonTypeEnum::get_type_name(),
				'description' => __( 'The comparison type to use when matching the pattern.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
