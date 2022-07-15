<?php
/**
 * Interface for for classes that register a GraphQL type with input fields to the GraphQL schema.
 *
 * @package AxeWP\GraphQL\Interfaces
 */

namespace AxeWP\GraphQL\Interfaces;

/**
 * Interface - TypeWithInterfaces.
 */
interface TypeWithInterfaces extends GraphQLType {
	/**
	 * Gets the array of GraphQL interfaces that should be applied to the type.
	 *
	 * @return string[]
	 */
	public static function get_interfaces() : array;
}
