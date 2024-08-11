<?php
/**
 * Handles support for WPGraphQL for WooCommerce.
 *
 * @package WPGraphQL\RankMath\Extensions\WPGraphQLWooCommerce
 * @since 0.3.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Extensions\WPGraphQLWooCommerce;

use WPGraphQL\RankMath\Extensions\WPGraphQLWooCommerce\Type\WPObject\SeoObjects;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class - WPGraphQLWooCommerce
 */
class WPGraphQLWooCommerce implements Registrable {
	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		if ( ! self::is_woographql_enabled() ) {
			return;
		}

		add_filter( 'graphql_seo_registered_object_classes', [ self::class, 'objects' ] );
	}

	/**
	 * Returns whether Gravity Forms Signature is enabled.
	 */
	public static function is_woographql_enabled(): bool {
		return class_exists( 'WPGraphQL\WooCommerce\WP_GraphQL_WooCommerce' );
	}

	/**
	 * Registers the SEO objects for WPGraphQL for WooCommerce.
	 *
	 * @param string[] $object_classes The array of object classes.
	 *
	 * @return string[]
	 */
	public static function objects( array $object_classes ): array {
		$object_classes[] = SeoObjects::class;

		return $object_classes;
	}
}
