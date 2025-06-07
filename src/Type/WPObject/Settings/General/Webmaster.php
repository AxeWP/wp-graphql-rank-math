<?php
/**
 * The Webmaster GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\General
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Type\WPObject\Settings\General;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Webmaster
 */
class Webmaster extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Webmaster';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Webmaster Tools settings', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'baidu'     => [
				'type'        => 'String',
				'description' => static fn () => __( 'The Baidu Webmaster Tools verification HTML code or ID.', 'wp-graphql-rank-math' ),
			],
			'bing'      => [
				'type'        => 'String',
				'description' => static fn () => __( 'The Bing Webmaster Tools verification HTML code or ID.', 'wp-graphql-rank-math' ),
			],
			'google'    => [
				'type'        => 'String',
				'description' => static fn () => __( 'The Google Search Console verification HTML code or ID.', 'wp-graphql-rank-math' ),
			],
			'norton'    => [
				'type'        => 'String',
				'description' => static fn () => __( 'The Norton Safe Web verification HTML code or ID.', 'wp-graphql-rank-math' ),
			],
			'pinterest' => [
				'type'        => 'String',
				'description' => static fn () => __( 'The Pinterest verification HTML code or ID.', 'wp-graphql-rank-math' ),
			],
			'yandex'    => [
				'type'        => 'String',
				'description' => static fn () => __( 'The Yandex verification HTML code or ID.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
