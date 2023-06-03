<?php
/**
 * The Rank Math Twitter OpenGraph meta tags GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\OpenGraph;

use WPGraphQL\RankMath\Type\Enum\TwitterCardTypeEnum;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Twitter
 */
class Twitter extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'OpenGraphTwitter';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The OpenGraph Twitter meta.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'card'                    => [
				'type'        => TwitterCardTypeEnum::get_type_name(),
				'description' => __( 'The Twitter card type', 'wp-graphql-rank-math' ),
			],
			'title'                   => [
				'type'        => 'String',
				'description' => __( 'Title of content', 'wp-graphql-rank-math' ),
			],
			'description'             => [
				'type'        => 'String',
				'description' => __( 'Description of content (maximum 200 characters)', 'wp-graphql-rank-math' ),
			],
			'appCountry'              => [
				'type'        => 'String',
				'description' => __( 'The app country.', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?string => ! empty( $source['app:country'] ) ? $source['app:country'] : null,
			],
			'ipadApp'                 => [
				'type'        => TwitterApp::get_type_name(),
				'description' => __( 'The Twitter iPad app meta', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?array => self::get_app_meta( $source, 'ipad' ),
			],
			'iphoneApp'               => [
				'type'        => TwitterApp::get_type_name(),
				'description' => __( 'The Twitter iPhone app meta', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?array => self::get_app_meta( $source, 'iphone' ),
			],
			'googleplayApp'           => [
				'type'        => TwitterApp::get_type_name(),
				'description' => __( 'The Twitter Google Play app meta', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?array => self::get_app_meta( $source, 'googleplay' ),
			],
			'playerUrl'               => [
				'type'        => 'Integer',
				'description' => __( 'URL of the twitter player.', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?int => ! empty( $source['player'] ) ? $source['player'] : null,
			],
			'playerStream'            => [
				'type'        => 'String',
				'description' => __( 'URL to raw video or audio stream', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?int => ! empty( $source['player:stream'] ) ? $source['player:stream'] : null,
			],
			'site'                    => [
				'type'        => 'String',
				'description' => __( '@username of website', 'wp-graphql-rank-math' ),
			],
			'playerStreamContentType' => [
				'type'        => 'String',
				'description' => __( 'The content type of the stream', 'wp-graphql-rank-math' ),
				'resolve'     => static fn ( $source ): ?int => ! empty( $source['player:stream:content_type'] ) ? $source['player:stream:content_type'] : null,
			],
			'image'                   => [
				'type'        => 'String',
				'description' => __( 'URL of image to use in the card.', 'wp-graphql-rank-math' ),
			],
			'creator'                 => [
				'type'        => 'String',
				'description' => __( '@username of content creator', 'wp-graphql-rank-math' ),
			],
			
		];
	}

	/**
	 * Get the app meta for the twitter app type.
	 *
	 * @param array<string, mixed> $source The values from the resolver.
	 * @param string               $type The app type.
	 *
	 * @return ?array<string, mixed>
	 */
	protected static function get_app_meta( array $source, string $type ): ?array {
		$values = [];

		if ( ! empty( $source[ 'app:name:' . $type ] ) ) {
			$values['name'] = $source[ 'app:name:' . $type ];
		}
		if ( ! empty( $source[ 'app:id:' . $type ] ) ) {
			$values['id'] = $source[ 'app:id:' . $type ];
		}
		if ( ! empty( $source[ 'app:url:' . $type ] ) ) {
			$values['url'] = $source[ 'app:url:' . $type ];
		}
	
		return $values ?: null;
	}
}
