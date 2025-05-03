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
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Helper\Compat;
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
		/**
		 * @todo: remove this when we don't need to support WooGraphQL< 0.21.1
		 * @see https://github.com/AxeWP/wp-graphql-rank-math/pull/115#issuecomment-2660900767
		 */
		if ( defined( 'WPGRAPHQL_WOOCOMMERCE_VERSION' ) && version_compare( WPGRAPHQL_WOOCOMMERCE_VERSION, '0.21.1', '>=' ) ) {
			$product_types = array_merge(
				$product_types,
				[
					'DownloadableProduct',
					'InventoriedProduct',
					'ProductUnion',
					'ProductWithAttributes',
					'ProductWithDimensions',
					'ProductWithPricing',
					'ProductWithVariations',
					'ProductVariation',
				]
			);
		}

		foreach ( $product_types as $graphql_type_name ) {
			Utils::overload_graphql_field_type( $graphql_type_name, 'seo', 'RankMathProductObjectSeo' );
		}

		/**
		 * @todo: remove this when we don't need to support WooGraphQL< 0.21.1
		 * @see https://github.com/AxeWP/wp-graphql-rank-math/pull/115#issuecomment-2660900767
		 */
		if ( defined( 'WPGRAPHQL_WOOCOMMERCE_VERSION' ) && version_compare( WPGRAPHQL_WOOCOMMERCE_VERSION, '0.21.1', '<' ) ) {
			self::register_product_variation_types();
		}
	}

	/**
	 * Registers the SEO types for product variations.
	 *
	 * @todo: remove this when we don't need to support WooGraphQL< 0.21.1
	 * @see https://github.com/AxeWP/wp-graphql-rank-math/pull/115#issuecomment-2660900767
	 */
	private static function register_product_variation_types(): void {
		// Register the Product Variation SEO type and apply it to the Product Variation and children.
		$type_name_for_product_variation = 'RankMathProductVariationObjectSeo';

		register_graphql_object_type(
			$type_name_for_product_variation,
			// @todo Remove when WPGraphQL < 2.3.0 is dropped.
			Compat::resolve_graphql_config(
				[
					'description'     => static fn () => __( 'The product variation object SEO data', 'wp-graphql-rank-math' ),
					'interfaces'      => [ ContentNodeSeo::get_type_name() ],
					'fields'          => [],
					'eagerlyLoadType' => true,
				]
			),
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
