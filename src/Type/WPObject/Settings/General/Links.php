<?php
/**
 * The Links GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\General
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\General;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - Links
 */
class Links extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Links';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO links settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'defaultAttachmentRedirectUrl' => [
				'type'        => 'String',
				'description' => __( 'The default redirection url for attachments without a parent post', 'wp-graphql-rank-math' ),
			],
			'hasCategoryBase'              => [
				'type'        => 'Boolean',
				'description' => __( 'Whether  /category/ should be included in category archive URLs.', 'wp-graphql-rank-math' ),
			],
			'nofollowDomains'              => [
				'type'        => 'String',
				'description' => __( 'Only add `nofollow` attributes to links with the following target domains. If null, `nofollow` will be applied to <em>all</em> external domains.', 'wp-graphql-rank-math' ),
			],
			'nofollowExcludedDomains'      => [
				'type'        => 'String',
				'description' => __( '`nofollow` attributes will <em>not</em> be added to links with the following target domains.', 'wp-graphql-rank-math' ),
			],
			'shouldNofollowImageLinks'     => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to automatically add the `rel="nofollow" attribute to links pointing to external image files.', 'wp-graphql-rank-math' ),
			],
			'shouldNofollowLinks'          => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to automatically add the `rel="nofollow" attribute to external links appearing in your posts, pages, and other post types.', 'wp-graphql-rank-math' ),
			],
			'shouldOpenInNewWindow'        => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to automatically add `target="_blank"` attribute for external links appearing in your posts, pages, and other post types to make them open in a new browser tab or window.', 'wp-graphql-rank-math' ),
			],
			'shouldRedirectAttachments'    => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to redirect all attachment page URLs to the post they appear in.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
