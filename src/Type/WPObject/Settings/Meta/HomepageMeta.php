<?php
/**
 * The HomepageMeta GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Meta
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Meta;

use WPGraphQL\AppContext;
use WPGraphQL\RankMath\Type\WPInterface\MetaSettingWithRobots;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - HomepageMeta
 */
class HomepageMeta extends ObjectType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'HomepageMetaSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Homepage settings. Only used when the Settings > Reading > Your homepage displays is set to `Your latest posts`.', 'wp-graphql-rank-math' );
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
		return [
			'title'               => [
				'type'        => 'String',
				'description' => __( 'Title tag.', 'wp-graphql-rank-math' ),
			],
			'description'         => [
				'type'        => 'String',
				'description' => __( 'Meta description.', 'wp-graphql-rank-math' ),
			],
			'hasCustomRobotsMeta' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether custom robots meta for author page are set. Otherwise the default meta will be used, as set in the Global Meta tab.', 'wp-graphql-rank-math' ),
			],
			'socialTitle'         => [
				'type'        => 'String',
				'description' => __( 'Title when shared on Facebook, Twitter and other social networks.', 'wp-graphql-rank-math' ),
			],
			'socialDescription'   => [
				'type'        => 'String',
				'description' => __( 'Description when shared on Facebook, Twitter and other social networks.', 'wp-graphql-rank-math' ),
			],
			'socialImage'         => [
				'type'        => 'MediaItem',
				'description' => __( 'Image displayed when your homepage is shared on Facebook and other social networks.', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source, array $args, AppContext $context ) {
					return ! empty( $source['socialImageId'] ) ? $context->get_loader( 'post' )->load_deferred( $source['socialImageId'] ) : null;
				},
			],
		];
	}
}
