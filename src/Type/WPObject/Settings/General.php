<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings;

use WPGraphQL\RankMath\Type\WPObject\Settings\General\BreadcrumbsConfig;
use WPGraphQL\RankMath\Type\WPObject\Settings\General\FrontendSeoScore;
use WPGraphQL\RankMath\Type\WPObject\Settings\General\Links;
use WPGraphQL\RankMath\Type\WPObject\Settings\General\Webmaster;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - General
 */
class General extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'General';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO general site settings', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'breadcrumbs'         => [
				'type'        => BreadcrumbsConfig::get_type_name(),
				'description' => __( 'Breadcrumbs settings.', 'wp-graphql-rank-math' ),
			],
			'hasBreadcrumbs'      => [
				'type'        => 'Boolean',
				'description' => __( 'Whether RankMath breadcrumbs are enabled.', 'wp-graphql-rank-math' ),
			],
			'links'               => [
				'type'        => Links::get_type_name(),
				'description' => __( 'Link settings.', 'wp-graphql-rank-math' ),
			],
			'webmaster'           => [
				'type'        => Webmaster::get_type_name(),
				'description' => __( 'Webmaster Tools settings.', 'wp-graphql-rank-math' ),
			],
			'hasFrontendSeoScore' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to display the calculated SEO Score as a badge on the frontend. It can be disabled for specific posts in the post editor.', 'wp-graphql-rank-math' ),
			],
			'frontendSeoScore'    => [
				'type'        => FrontendSeoScore::get_type_name(),
				'description' => __( 'Frontend SEO score settings.', 'wp-graphql-rank-math' ),
			],
			'rssBeforeContent'    => [
				'type'        => 'String',
				'description' => __( 'The content to add before each post in your site feeds', 'wp-graphql-rank-math' ),
			],
			'rssAfterContent'     => [
				'type'        => 'String',
				'description' => __( 'The content to add after each post in your site feeds', 'wp-graphql-rank-math' ),
			],
		];
	}
}
