<?php
/**
 * The JSON+LD Graph Type enum.
 *
 * @package WPGraphQL\RankMath\Type\Enum
 */

namespace WPGraphQL\RankMath\Type\Enum;

use RankMath\Helper;
use WPGraphQL\Type\WPEnumType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;


/**
 * Class - Article
 */
class JsonLdGraphTypeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLdGraphTypeEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The JSON+LD graph type', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		$types = Helper::choices_rich_snippet_types();

		$values = [];

		foreach ( $types as $value => $description ) {
			$values[ WPEnumType::get_safe_name( $value ) ] = [
				'description' => $description,
				'value'       => ucfirst( $value ),
			];
		}

		// Not all values are RankMath options.
		$values['WEBSITE']        = [
			'description' => __( 'WebSite', 'wp-graphql-rank-math' ),
			'value'       => 'WebSite',
		];
		$values['WEBPAGE']        = [
			'description' => __( 'WebPage', 'wp-graphql-rank-math' ),
			'value'       => 'WebPage',
		];
		$values['COLLECTIONPAGE'] = [
			'description' => __( 'CollectionPage', 'wp-graphql-rank-math' ),
			'value'       => 'CollectionPage',
		];

		return $values;
	}
}
