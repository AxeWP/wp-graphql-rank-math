<?php
/**
 * The shared SEO fields interface.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Type\WPObject\JsonLd;

/**
 * Class - BaseSeoFields
 */
class BaseSeoFields extends InterfaceType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'BaseSeoFields';
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
		return __( 'Base SEO fields shared across WP types.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'title'           => [
				'type'        => 'String',
				'description' => __( 'The title.', 'wp-graphql-rank-math' ),
			],
			'description'     => [
				'type'        => 'String',
				'description' => __( 'The meta description.', 'wp-graphql-rank-math' ),
			],
			'robots'          => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'A list of the robots meta properties to output.', 'wp-graphql-rank-math' ),
			],
			'focusKeywords'   => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'The focus keywords you want to rank for', 'wp-graphql-rank-math' ),
			],
			'canonicalUrl'    => [
				'type'        => 'String',
				'description' => __( 'The canonical url.', 'wp-graphql-rank-math' ),
			],
			'breadcrumbTitle' => [
				'type'        => 'String',
				'description' => __( 'The title to use in the breadcrumbs for this post', 'wp-graphql-rank-math' ),
			],
			'jsonLd'          => [
				'type'        => JsonLd::get_type_name(),
				'description' => __( 'The JSON+LD data', 'wp-graphql-rank-math' ),
			],
			'fullHead'        => [
				'type'        => 'String',
				'description' => __( 'The fully-rendered `head` tag for the given item', 'wp-graphql-rank-math' ),
			],
		];
	}
}
