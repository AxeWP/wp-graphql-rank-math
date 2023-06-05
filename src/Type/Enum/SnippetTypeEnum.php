<?php
/**
 * The Snippet type enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

namespace WPGraphQL\RankMath\Type\Enum;

use RankMath\Helper;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - SnippetTypeEnum
 */
class SnippetTypeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'SnippetTypeEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The rich snippet type.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		$types = Helper::choices_rich_snippet_types( __( 'None.', 'wp-graphql-rank-math' ) );

		$values = [
			'LOCAL_BUSINESS' => [
				'description' => __( 'Local Business', 'wp-graphql-rank-math' ),
				'value'       => 'LocalBusiness',
			],
		];

		foreach ( $types as $name => $description ) {
			$values[ strtoupper( $name ) ] = [
				'description' => $description,
				'value'       => $name,
			];
		}

		if ( class_exists( 'WooCommerce' ) || class_exists( 'Easy_Digital_Downloads' ) ) {
			$values['PRODUCT'] = [
				'description' => __( 'Product.', 'wp-graphql-rank-math' ),
				'value'       => 'product',
			];
		}

		return $values;
	}
}
