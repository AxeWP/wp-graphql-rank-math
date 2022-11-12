<?php
/**
 * Interface for classes that register a GraphQL type to the GraphQL schema.
 *
 * @package AxeWP\GraphQL\Interfaces
 */

namespace AxeWP\GraphQL\Interfaces;

if ( ! interface_exists( '\AxeWP\GraphQL\Interfaces\GraphQLType' ) ) {

	/**
	 * Interface - GraphQLType
	 */
	interface GraphQLType {
		/**
		 * Register connections to the GraphQL Schema.
		 */
		public static function register() : void;
	}
}
