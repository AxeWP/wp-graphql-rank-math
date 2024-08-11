<?php
/**
 * Registers support for additional Extensions.
 *
 * @package WPGraphQL\RankMath\Extensions
 * @since 0.3.1
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Extensions;

use WPGraphQL\RankMath\Extensions\WPGraphQLWooCommerce\WPGraphQLWooCommerce;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class - Extensions
 */
class Extensions implements Registrable {
	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		$classes_to_register = [
			WPGraphQLWooCommerce::class,
		];

		foreach ( $classes_to_register as $class ) {
			$class::init();
		}
	}
}
