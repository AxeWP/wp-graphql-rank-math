<?php
/**
 * Interface for for classes that register a GraphQL type with connections to the GraphQL schema.
 *
 * @package AxeWP\GraphQL\Interfaces
 */

namespace AxeWP\GraphQL\Interfaces;

/**
 * Interface - TypeWithConnections
 */
interface TypeWithConnections extends GraphQLType {
	/**
	 * Gets the properties for the type.
	 *
	 * @return array<string, array{
	 *   toType: string,
	 *   description: string,
	 *   args?: array<string, array{
	 *     type: string|array<string, string | array<string, string>>,
	 *     description: string,
	 *     defaultValue?: mixed
	 *   }>,
	 *   connectionInterfaces?: string[],
	 *   oneToOne?: bool,
	 *   resolve?: callable,
	 * }>
	 */
	public static function get_connections() : array;
}
