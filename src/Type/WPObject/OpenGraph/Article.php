<?php
/**
 * The Rank Math Facebook OpenGraph meta tags GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\OpenGraph;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Article
 */
class Article extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'OpenGraphArticle';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The OpenGraph Article meta.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'modifiedTime'  => [
				'type'        => 'String',
				'description' => __( 'The date modified.', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?string => ! empty( $source['modified_time'] ) ? $source['modified_time'] : null,
			],
			'publishedTime' => [
				'type'        => 'String',
				'description' => __( 'The date published.', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?string => ! empty( $source['published_time'] ) ? $source['published_time'] : null,
			],
			'publisher'     => [
				'type'        => 'String',
				'description' => __( 'The publisher', 'wp-graphql-rank-math' ),
			],
			'author'        => [
				'type'        => 'String',
				'description' => __( 'The author.', 'wp-graphql-rank-math' ),
			],
			'tags'          => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'The article tags.', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source ): ?array {
					$value = ! empty( $source['tag'] ) ? $source['tag'] : null;

					if ( is_string( $value ) ) {
						$value = [ $value ];
					}

					return $value;
				},
			],
			'section'       => [
				'type'        => 'String',
				'description' => __( 'The article category.', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?string => ! empty( $source['section'] ) ? $source['section'] : null,
			],
		];
	}
}
