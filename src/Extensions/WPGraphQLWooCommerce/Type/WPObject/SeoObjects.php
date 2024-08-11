<?php
/**
 * Registers the SEO objects for WPGraphQL for WooCommerce.
 *
 * @package WPGraphQL\RankMath\Extensions\WPGraphQLWooCommerce\Type\WPObject
 * @since 0.3.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Extensions\WPGraphQLWooCommerce\Type\WPObject;

use WPGraphQL\RankMath\Type\WPInterface\ContentNodeSeo;
use WPGraphQL\RankMath\Utils\Utils;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\Registrable;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeNameTrait;
use WPGraphQL\WooCommerce\WP_GraphQL_WooCommerce;

/**
 * Class - SeoObjects
 */
class SeoObjects implements Registrable {
	use TypeNameTrait;

	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		add_action( 'graphql_register_types', [ static::class, 'register' ] );
	}

	/**
	 * Registers the SEO GraphQL objects to the schema.
	 */
	public static function register(): void {
		// Set SEO field types for product children.
		$product_types = WP_GraphQL_WooCommerce::get_enabled_product_types();

		foreach ( $product_types as $graphql_type_name ) {
			Utils::overload_graphql_field_type( $graphql_type_name, 'seo', 'RankMathProductObjectSeo' );
		}

		// Register the Product Variation SEO type and apply it to the Product Variation and children.
		$type_name_for_product_variation = 'RankMathProductVariationObjectSeo';

		register_graphql_object_type(
			$type_name_for_product_variation,
			[
				'description'     => __( 'The product variation object SEO data', 'wp-graphql-rank-math' ),
				'interfaces'      => [ ContentNodeSeo::get_type_name() ],
				'fields'          => [],
				'eagerlyLoadType' => true,
			]
		);

		$product_variations = array_merge(
			[
				'ProductVariation',
			],
			WP_GraphQL_WooCommerce::get_enabled_product_variation_types(),
		);

		foreach ( $product_variations as $product_variation ) {
			Utils::overload_graphql_field_type( $product_variation, 'seo', $type_name_for_product_variation );
		}
	}
}
