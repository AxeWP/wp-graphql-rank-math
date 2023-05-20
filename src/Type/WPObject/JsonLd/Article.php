<?php
/**
 * The Schema.org Article object
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject\JsonLd;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;
use WPGraphQL\RankMath\Type\WPInterface\JsonLd\Thing;

/**
 * Class - Article
 */
class Article extends ObjectType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLdArticle';
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_type_config() : array {
		$config = parent::get_type_config();

		$config['eagerlyLoadType'] = true;

		return $config;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'The Schema.org Article .', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces() : array {
		return [
			Thing::get_type_name(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'author'        => [
				'type'        => Person::get_type_name(), // @todo
				'description' => __( 'The author of this object', 'wp-graphql-rank-math' ),
			],
			'dateModified'  => [
				'type'        => 'String',
				'description' => __( 'The JSON+LD @graph objects.', 'wp-graphql-rank-math' ),
			],
			'datePublished' => [
				'type'        => 'String',
				'description' => __( 'The JSON+LD @graph objects.', 'wp-graphql-rank-math' ),
			],
			'description'   => [
				'type'        => 'String',
				'description' => __( 'A description of the object', 'wp-graphql-rank-math' ),
			],
			'keywords'      => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'Keywords or tags used to describe some the graph item.', 'wp-graphql-rank-math' ),
			],
			'publisher'     => [
				'type'        => 'String', // @todo
				'description' => __( 'The publisher of the creative work', 'wp-graphql-rank-math' ),
			],
		];
	}
}
