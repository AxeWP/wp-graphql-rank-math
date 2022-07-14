<?php
/**
 * Abstract class to make it easy to register Types to WPGraphQL.
 *
 * @package AxeWP\GraphQL\Abstracts
 */

namespace AxeWP\GraphQL\Abstracts;

use AxeWP\GraphQL\Interfaces\GraphQLType;
use AxeWP\GraphQL\Traits\TypeNameTrait;

/**
 * Class - Type
 */
abstract class Type implements GraphQLType {
	use TypeNameTrait;

	/**
	 * Defines the GraphQL type name registered in WPGraphQL.
	 *
	 * @return string
	 */
	abstract protected static function type_name() : string;

	/**
	 * Gets the GraphQL type description.
	 */
	abstract public static function get_description() : string;

	/**
	 * Gets the $config array used to register the type to WPGraphQL.
	 */
	protected static function get_type_config() : array {
		$config = [
			'description'     => static::get_description(),
			'eagerlyLoadType' => static::should_load_eagerly(),
		];

		return $config;
	}

	/**
	 * Whether the type should be loaded eagerly by WPGraphQL. Defaults to false.
	 *
	 * Eager load should only be necessary for types that are not referenced directly (e.g. in Unions, Interfaces ).
	 */
	public static function should_load_eagerly() : bool {
		return false;
	}
}
