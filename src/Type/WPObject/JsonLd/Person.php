<?php
/**
 * The Schema.org Person object
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\JsonLd;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Type\WPInterface\JsonLd\Graph;
use WPGraphQL\RankMath\Type\WPInterface\JsonLd\Thing;

/**
 * Class - Person
 */
class Person extends ObjectType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLdPerson';
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_type_config() : array {
		$config = parent::get_type_config();

		$config['eagerlyLoadType'] = true;

		return $config;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'The Schema.org Person .', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces() : array {
		return [
			Graph::get_type_name(),
			Thing::get_type_name(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			// @todo worksFor
			'worksFor' => [
				'type'        => 'String', // publisher.
				'description' => __( 'Organizations that the person works for.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
