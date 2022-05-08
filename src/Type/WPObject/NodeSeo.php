<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use AxeWP\GraphQL\Abstracts\ObjectType;
use AxeWP\GraphQL\Interfaces\TypeWithInterfaces;
use WPGraphQL\RankMath\Model\ContentTypeSeo;
use WPGraphQL\RankMath\Model\TermNodeSeo;
use WPGraphQL\RankMath\Model\UserSeo;
use WPGraphQL\RankMath\Type\WPInterface\BaseSeoFields;

/**
 * Class - NodeSeo
 */
class NodeSeo extends ObjectType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'NodeSeo';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register(): void {
		parent::register();

		// Register to User.
		register_graphql_field(
			'User',
			'seo',
			[
				'type'        => self::get_type_name(),
				'description' => self::get_description(),
				'resolve'     => function( $source ) {
					return ! empty( $source->databaseId ) ? new UserSeo( $source->databaseId ) : null;
				},
			]
		);

		// Register to TermNode.
		register_graphql_field(
			'TermNode',
			'seo',
			[
				'type'        => self::get_type_name(),
				'description' => self::get_description(),
				'resolve'     => function( $source ) {
					return ! empty( $source->databaseId ) ? new TermNodeSeo( $source->databaseId ) : null;
				},
			]
		);

		// Register to ContentType.
		register_graphql_field(
			'ContentType',
			'seo',
			[
				'type'        => self::get_type_name(),
				'description' => self::get_description(),
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
		return __( 'The seo data for the node', 'wp-graphql-rank-math' );
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
		return [];
	}
}
