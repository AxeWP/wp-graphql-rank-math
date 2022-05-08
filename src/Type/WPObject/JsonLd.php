<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - JsonLd
 */
class JsonLd extends ObjectType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLd';
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_type_config() : array {
		$config = parent::get_type_config();

		return $config;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'The JSON+LD information.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'raw' => [
				'type'        => 'String',
				'description' => __( 'The raw JSON+LD output', 'wp-graphql-rank-math' ),
			],
		];
	}
}
