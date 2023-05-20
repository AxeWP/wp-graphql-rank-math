<?php
/**
 * The Schema.org WebPage interface.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface\JsonLd
 */

namespace WPGraphQL\RankMath\Type\WPInterface\JsonLd;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - WebPage
 */
class WebPage extends InterfaceType implements TypeWithInterfaces {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLdGraphWithWebPageFields';
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
		return __( 'An interface with schema.org WebPage fields', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			CreativeWork::get_type_name(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		$fields = [
			'breadcrumb'         => [ // @todo .
				'type'        => 'String',
				'description' => __( 'The main image on the page.', 'wp-graphql-rank-math' ),
			],
			'primaryImageOfPage' => [ // @todo .
				'type'        => 'String',
				'description' => __( 'The main image on the page.', 'wp-graphql-rank-math' ),
			],
		];

		return $fields;
	}
}
