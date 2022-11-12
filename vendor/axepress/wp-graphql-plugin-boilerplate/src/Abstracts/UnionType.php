<?php
/**
 * Abstract class to make it easy to register Union types to WPGraphQL.
 *
 * @package AxeWP\GraphQL\Abstracts
 */

namespace AxeWP\GraphQL\Abstracts;

use AxeWP\GraphQL\Traits\TypeResolverTrait;

if ( ! class_exists( '\AxeWP\GraphQL\Abstracts\UnionType' ) ) {

	/**
	 * Class - UnionType
	 */
	abstract class UnionType extends Type {
		use TypeResolverTrait;

		/**
		 * The WPGraphQL TypeRegistry instance.
		 *
		 * @var ?\WPGraphQL\Registry\TypeRegistry
		 */
		protected static $type_registry = null;

		/**
		 * Gets the array of possible GraphQL types that can be resolved to.
		 *
		 * @return string[]
		 */
		abstract public static function get_possible_types() : array;

		/**
		 * {@inheritDoc}
		 *
		 * @param \WPGraphQL\Registry\TypeRegistry $type_registry The WPGraphQL TypeRegistry instance.
		 */
		public static function register( $type_registry = null ) : void {
			self::$type_registry = $type_registry;

			register_graphql_union_type( static::get_type_name(), static::get_type_config() );
		}

		/**
		 * {@inheritDoc}
		 */
		protected static function get_type_config() : array {
			$config = parent::get_type_config();

			$config['typeNames']   = static::get_possible_types();
			$config['resolveType'] = static::get_type_resolver();

			return $config;
		}

		/**
		 * {@inheritDoc}
		 */
		public static function should_load_eagerly(): bool {
			return true;
		}
	}

}
