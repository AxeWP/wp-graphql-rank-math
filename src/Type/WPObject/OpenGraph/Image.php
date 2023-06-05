<?php
/**
 * The Rank Math Image OpenGraph meta tags GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\OpenGraph;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Image
 */
class Image extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'OpenGraphImage';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The OpenGraph Image meta.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'url'       => [
				'type'        => 'String',
				'description' => __( 'URL for the image.', 'wp-graphql-rank-math' ),
			],
			'secureUrl' => [
				'type'        => 'String',
				'description' => __( 'The https:// URL for the image.', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?string => ! empty( $source['secure_url'] ) ? $source['secure_url'] : null,
			],
			'type'      => [
				'type'        => 'String', // @todo
				'description' => __( 'MIME type of the image. ', 'wp-graphql-rank-math' ),
			],
			'width'     => [
				'type'        => 'Float',
				'description' => __( 'Width of image in pixels.', 'wp-graphql-rank-math' ),
			],
			'height'    => [
				'type'        => 'Float',
				'description' => __( 'Height of image in pixels. ', 'wp-graphql-rank-math' ),
			],
			
		];
	}
}
