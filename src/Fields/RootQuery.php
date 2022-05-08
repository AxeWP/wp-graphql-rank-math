<?php
/**
 * Registers fields to RootQuery
 *
 * @package WPGraphQL\Fields
 */

namespace WPGraphQL\RankMath\Fields;

use AxeWP\GraphQL\Abstracts\FieldsType;
use WPGraphQL\RankMath\Model\Settings as ModelSettings;
use WPGraphQL\RankMath\Type\WPObject\Settings;

/**
 * Class - RootQuery
 */
class RootQuery extends FieldsType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'RootQuery';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public static function get_type_name() : string {
		return static::type_name();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'rankMathSettings' => [
				'type'        => Settings::get_type_name(),
				'description' => __( 'RankMath SEO site settings', 'wp-graphql-rank-math' ),
				'resolve'     => fn() => new ModelSettings(),
			],
		];
	}
}
