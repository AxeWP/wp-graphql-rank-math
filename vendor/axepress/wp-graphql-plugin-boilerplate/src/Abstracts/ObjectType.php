<?php
/**
 * Abstract class to make it easy to register Object types to WPGraphQL.
 *
 * @package AxeWP\GraphQL\Abstracts
 */

namespace AxeWP\GraphQL\Abstracts;

use AxeWP\GraphQL\Interfaces\TypeWithFields;

/**
 * Class - ObjectType
 */
abstract class ObjectType extends Type implements TypeWithFields {
	/**
	 * {@inheritDoc}
	 */
	public static function register() : void {
		register_graphql_object_type( static::get_type_name(), static::get_type_config() );
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_type_config() : array {
		$config = parent::get_type_config();

		$config['fields'] = static::get_fields();

		if ( method_exists( static::class, 'get_connections' ) ) {
			// @phpstan-ignore-next-line
			$config['connections'] = static::get_connections();
		}

		if ( method_exists( static::class, 'get_interfaces' ) ) {
			// @phpstan-ignore-next-line
			$config['interfaces'] = static::get_interfaces();
		}

		return $config;
	}
}
