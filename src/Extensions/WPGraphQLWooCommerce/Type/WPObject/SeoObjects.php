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
		$product_types = array_merge(
			WP_GraphQL_WooCommerce::get_enabled_product_types(),
			[
				'ProductUnion',
				'ProductWithPricing',
				'ProductWithDimensions',
				'InventoriedProduct',
				'DownloadableProduct',
				'ProductWithAttributes',
				'ProductWithVariations',
			]
		);

		foreach ( $product_types as $graphql_type_name ) {
			Utils::overload_graphql_field_type( $graphql_type_name, 'seo', 'RankMathProductObjectSeo' );
		}

	}
}
