<?php
/**
 * The Robots meta value enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

namespace WPGraphQL\RankMath\Type\Enum;

use RankMath\Helper;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - RobotsMetaValueEnum
 */
class RobotsMetaValueEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RobotsMetaValueEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Robot meta value tag.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		$types  = Helper::choices_robots();
		$values = [];

		foreach ( $types as $name => $description ) {
			$values[ strtoupper( $name ) ] = [
				'description' => wp_strip_all_tags( $description ),
				'value'       => $name,
			];
		}

		return $values;
	}
}
