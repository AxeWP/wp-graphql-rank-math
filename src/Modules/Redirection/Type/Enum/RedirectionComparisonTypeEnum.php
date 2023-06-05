<?php
/**
 * The Redirection Comparison Type enum.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\Enum
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\Enum;

use RankMath\Helper;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;
use WPGraphQL\Type\WPEnumType;

/**
 * Class - RedirectionComparisonTypeEnum
 */
class RedirectionComparisonTypeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RedirectionComparisonTypeEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Redirection comparison type.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		$redirection_types = Helper::choices_comparison_types();

		$values = [];

		foreach ( $redirection_types as $value => $description ) {
			$values[ WPEnumType::get_safe_name( $value ) ] = [
				'description' => $description,
				'value'       => $value,
			];
		}

		return $values;
	}
}
