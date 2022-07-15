<?php
/**
 * Adds filters that modify core schema.
 *
 * @package WPGraphQL\PluginName
 */

namespace WPGraphQL\PluginName;

use WPGraphQL\PluginName\Data\Factory;
use AxeWP\GraphQL\Interfaces\Hookable;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Hookable {
	/**
	 * {@inheritDoc}
	 */
	public static function init() : void {
		add_filter( 'graphql_pb_type_prefix', [ __CLASS__, 'get_type_prefix' ] );
	}

	/**
	 * Prefixes all plugin GraphQL types.
	 *
	 * @param string $type_name the non-prefixed type name.
	 */
	public static function get_type_prefix( string $type_name = null ) : string {
		return ! empty( $type_name ) ? $type_name : 'PluginName';
	}
}
