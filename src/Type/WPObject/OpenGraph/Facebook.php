<?php
/**
 * The Rank Math Facebook OpenGraph meta tags GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Type\WPObject\OpenGraph;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Facebook
 */
class Facebook extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'OpenGraphFacebook';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The OpenGraph Facebook meta.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'appId'  => [
				'type'        => 'ID',
				'description' => __( 'The Facebook app ID associated with this resource', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?string => ! empty( $source['app_id'] ) ? (string) $source['app_id'] : null,
			],
			'admins' => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'The Facebook admins associated with this resource', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source ): ?array {
					$value = ! empty( $source['admins'] ) ? $source['admins'] : null;

					if ( empty( $value ) ) {
						return null;
					}

					if ( ! is_array( $value ) ) {
						$value = [ (string) $value ];
					}

					// Ensure all tags are strings.
					$value = array_map( 'strval', $value );

					return $value;
				},
			],
		];
	}
}
