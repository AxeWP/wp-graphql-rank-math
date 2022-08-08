<?php
/**
 * The Rank Math Facebook OpenGraph meta tags GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\OpenGraph;

use AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Facebook
 */
class Facebook extends ObjectType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'OpenGraphFacebook';
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
		return __( 'The OpenGraph Facebook meta.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'appId'  => [
				'type'        => 'ID',
				'description' => __( 'The Facebook app ID associated with this resource', 'wp-graphql-rank-math' ),
				'resolve'     => fn( $source ) :?string => ! empty( $source['app_id'] ) ? $source['app_id'] : null,
			],
			'admins' => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'The Facebook admins associated with this resource', 'wp-graphql-rank-math' ),
				'resolve'     => function( $source ) : ?array {
					$value = ! empty( $source['admins'] ) ? $source['admins'] : null;

					if ( is_string( $value ) ) {
						$value = [ $value ];
					}

					return $value;
				},
			],
		];
	}
}
