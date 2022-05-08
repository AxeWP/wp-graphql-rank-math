<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use AxeWP\GraphQL\Abstracts\ObjectType;
use AxeWP\GraphQL\Interfaces\TypeWithInterfaces;
use WPGraphQL\RankMath\Model\ContentNodeSeo as ModelContentNodeSeo;
use WPGraphQL\RankMath\Type\WPInterface\BaseSeoFields;

/**
 * Class - ContentNodeSeo
 */
class ContentNodeSeo extends ObjectType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'ContentNodeSeo';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register(): void {
		parent::register();

		register_graphql_field(
			'ContentNode',
			'seo',
			[
				'type'        => self::get_type_name(),
				'description' => self::get_description(),
				'resolve'     => function( $source ) {
					return ! empty( $source->databaseId ) ? new ModelContentNodeSeo( $source->databaseId ) : null;
				},
			]
		);
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
		return __( 'The seo data for Post Objects', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces() : array {
		return [ BaseSeoFields::get_type_name() ];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'isPillarContent' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the item is considered pillar (cornerstone) content', 'wp-graphql-rank-math' ),
			],
			'seoScore'        => [
				'type'        => SeoScore::get_type_name(),
				'description' => __( 'The SEO score', 'wp-graphql-rank-math' ),
			],
		];
	}
}
