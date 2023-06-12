<?php
/**
 * The Redirection connection Orderby Input.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\Input
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\Input;

use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionConnectionOrderByEnum;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InputType;

/**
 * Class - RedirectionConnectionOrderbyInput
 */
class RedirectionConnectionOrderbyInput extends InputType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RedirectionConnectionOrderbyInput';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Redirection connection orderby input.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'field' => [
				'type'        => RedirectionConnectionOrderByEnum::get_type_name(),
				'description' => __( 'The field to order the results by.', 'wp-graphql-rank-math' ),
			],
			'order' => [
				'type'        => 'OrderEnum',
				'description' => __( 'The ordering direction.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
