<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Type\WPInterface\JsonLd\Graph;

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
			'raw'     => [
				'type'        => 'String',
				'description' => __( 'The raw JSON+LD output', 'wp-graphql-rank-math' ),
			],
			'context' => [
				'type'        => 'String',
				'description' => __( 'The JSON+LD context', 'wp-graphql-rank-math' ),
			],
			'graph'   => [
				'type'        => [ 'list_of' => Graph::get_type_name() ],
				'description' => __( 'The JSON+LD @graph objects.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
