<?php
/**
 * The GlobalMeta GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Meta
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Meta;

use WPGraphQL\AppContext;
use WPGraphQL\RankMath\Type\Enum\TwitterCardTypeEnum;
use WPGraphQL\RankMath\Type\WPInterface\MetaSettingWithRobots;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - GlobalMeta
 */
class GlobalMeta extends ObjectType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'GlobalMetaSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Global settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			MetaSettingWithRobots::get_type_name(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		$fields = [
			'openGraphImage'             => [
				'type'        => 'MediaItem',
				'description' => __( 'When a featured image or an OpenGraph Image is not set for individual posts/pages/CPTs, this image will be used as a fallback thumbnail when your post is shared on Facebook.', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source, array $args, AppContext $context ) {
					return ! empty( $source['openGraphImageId'] ) ? $context->get_loader( 'post' )->load_deferred( $source['openGraphImageId'] ) : null;
				},
			],
			'separator'                  => [
				'type'        => 'String',
				'description' => __( 'The separator character used in titles.', 'wp-graphql-rank-math' ),
			],
			'shouldCapitalizeTitles'     => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to automatically capitalize the first character of each word in the titles.', 'wp-graphql-rank-math' ),
			],
			'shouldIndexEmptyTaxonomies' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to index enpty Taxonomy archives', 'wp-graphql-rank-math' ),
			],
			'twitterCardType'            => [
				'type'        => TwitterCardTypeEnum::get_type_name(),
				'description' => __( 'Card type selected when creating a new post. This will also be applied for posts without a card type selected.', 'wp-graphql-rank-math' ),
			],
		];

		if ( ! current_theme_supports( 'title-tag' ) ) {
			$fields['shouldRewriteTitle'] = [
				'type'        => 'Boolean',
				'description' => __( 'Whether titles for page, post, category, search, and archive pages can be rewritten. Only visible in themes without title-tag support', 'wp-graphql-rank-math' ),
			];
		}

		return $fields;
	}
}
