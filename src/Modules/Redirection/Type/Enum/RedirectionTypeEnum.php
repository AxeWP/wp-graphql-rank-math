<?php
/**
 * The SEO Twitter Card Type enum.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\Enum
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\Enum;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;
use \RankMath\Helper;
use WPGraphQL\Type\WPEnumType;

/**
 * Class - RedirectionTypeEnum
 */
class RedirectionTypeEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'RedirectionType';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The Redirection type.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		$redirection_types = Helper::choices_redirection_types();

		$values = [];

		foreach ( $redirection_types as $value => $description ) {
			$values[ WPEnumType::get_safe_name( 'REDIRECT_' . (string) $value ) ] = [
				'description' => $description,
				'value'       => $value,
			];
		}

		return $values;
	}
}
