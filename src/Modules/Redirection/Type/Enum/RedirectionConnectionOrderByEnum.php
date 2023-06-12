<?php
/**
 * The Redirection connection OrderBy enum.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Type\Enum
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Type\Enum;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType;

/**
 * Class - RedirectionConnectionOrderByEnum
 */
class RedirectionConnectionOrderByEnum extends EnumType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RedirectionConnectionOrderByEnum';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Options for ordering the Redirection connection.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_values(): array {
		return [
			'DATABASE_ID'        => [
				'description' => __( 'Order by the database ID.', 'wp-graphql-rank-math' ),
				'value'       => 'id',
			],
			'DATE_CREATED'       => [
				'description' => __( 'Order by the date created.', 'wp-graphql-rank-math' ),
				'value'       => 'created',
			],
			'DATE_LAST_ACCESSED' => [
				'description' => __( 'Order by the date last accessed.', 'wp-graphql-rank-math' ),
				'value'       => 'last_accessed',
			],
			'DATE_UPDATED'       => [
				'description' => __( 'Order by the date created.', 'wp-graphql-rank-math' ),
				'value'       => 'created',
			],
			'HITS'               => [
				'description' => __( 'Order by the number of hits.', 'wp-graphql-rank-math' ),
				'value'       => 'hits',
			],
			'REDIRECT_TO_URL'    => [
				'description' => __( 'Order by the Redirect To URL', 'wp-graphql-rank-math' ),
				'value'       => 'url_to',
			],
			'TYPE'               => [
				'description' => __( 'Order by the redirection type (HTTP status code).', 'wp-graphql-rank-math' ),
				'value'       => 'header_code',
			],
		];
	}
}
