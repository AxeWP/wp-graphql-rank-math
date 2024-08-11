<?php

namespace Tests\WPGraphQL\RankMath\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Wpunit extends \Codeception\Module {

	/**
	 * Converts a string value to its Enum equivalent
	 *
	 * @param string      $enumName Name of the Enum registered in GraphQL.
	 * @param string|null $value .
	 * @return string|null
	 */
	public static function get_enum_for_value( string $enumName, $value ) {
		if ( null === $value ) {
			return null;
		}

		$typeRegistry = \WPGraphQL::get_type_registry();
		return $typeRegistry->get_type( $enumName )->serialize( $value );
	}
}
