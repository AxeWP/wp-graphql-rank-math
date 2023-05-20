<?php
/**
 * The Schema.org Action interface.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface\JsonLd
 */

namespace WPGraphQL\RankMath\Type\WPInterface\JsonLd;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - Action
 */
class Action extends InterfaceType implements TypeWithInterfaces {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLdAction';
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
		return __( 'A schema.org Action', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			Graph::get_type_name(),
			Thing::get_type_name(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		$fields = [
			'target' => [
				'type'        => 'String',
				'description' => __( 'The target EntryPoint for an Action.', 'wp-graphql-rank-math' ),
			],
			// @todo mainEntityOfPage
		];

		return $fields;
	}
}
