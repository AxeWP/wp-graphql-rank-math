<?php
/**
 * Abstract class to make it easy to register Input types to WPGraphQL.
 *
 * @package AxeWP\GraphQL\Abstracts
 */

namespace AxeWP\GraphQL\Abstracts;

use AxeWP\GraphQL\Interfaces\TypeWithInputFields;

if ( ! class_exists( '\AxeWP\GraphQL\Abstracts\InputType' ) ) {

	/**
	 * Class - InputType
	 */
	abstract class InputType extends Type implements TypeWithInputFields {
		/**
		 * {@inheritDoc}
		 */
		public static function register() : void {
			register_graphql_input_type( static::get_type_name(), static::get_type_config() );
		}

		/**
		 * {@inheritDoc}
		 */
		protected static function get_type_config() : array {
			$config = parent::get_type_config();

			$config['fields'] = static::get_fields();

			return $config;
		}
	}
}
