<?php
/**
 * The Schema.org Thing interface.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface\JsonLd
 */

namespace WPGraphQL\RankMath\Type\WPInterface\JsonLd;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - Thing
 */
class Thing extends InterfaceType implements TypeWithInterfaces {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLdThing';
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_type_config() : array {
		$config = parent::get_type_config();

		return $config;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'A schema.org Thing', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			Graph::get_type_name(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		$fields = [
			'name'        => [
				'type'        => 'String',
				'description' => __( 'The name of the item.', 'wp-graphql-rank-math' ),
			],
			'description' => [
				'type'        => 'String',
				'description' => __( 'A description of the item.', 'wp-graphql-rank-math' ),
			],
			'url'         => [
				'type'        => 'String',
				'description' => __( 'URL of the item.', 'wp-graphql-rank-math' ),
			],
			// @todo image
			'sameAs'      => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'URL of a reference Web page that unambiguously indicates the item\'s identity.', 'wp-graphql-rank-math' ),
			],
			// @todo mainEntityOfPage
		];

		return $fields;
	}
}
