<?php
/**
 * Interface for for classes that register a GraphQL type with fields to the GraphQL schema.
 *
 * @package AxeWP\GraphQL\Interfaces
 */

namespace AxeWP\GraphQL\Interfaces;

if ( ! interface_exists( '\AxeWP\GraphQL\Interfaces\TypeWithFields' ) ) {

	/**
	 * Interface - TypeWithFields.
	 */
	interface TypeWithFields extends GraphQLType {
		/**
		 * Gets the fields for the type.
		 *
		 * @return array<string, array{
		 *   type: string|array<string, string | array<string, string>>,
		 *   description: string,
		 *   args?: array<string, array{
		 *     type: string|array<string, string | array<string, string>>,
		 *     description: string,
		 *     defaultValue?: mixed
		 *   }>,
		 *   resolve?: callable,
		 *   deprecationReason?: string,
		 * }>
		 */
		public static function get_fields() : array;
	}
}
