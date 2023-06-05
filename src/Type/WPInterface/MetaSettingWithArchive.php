<?php
/**
 * Interface for meta settings with archive fields.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;

/**
 * Class - MetaSettingWithArchive
 */
class MetaSettingWithArchive extends InterfaceType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'MetaSettingWithArchive';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Meta Settings with archive fields.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'archiveTitle'       => [
				'type'        => 'String',
				'description' => __( 'Default title tag for archive page.', 'wp-graphql-rank-math' ),
			],
			'archiveDescription' => [
				'type'        => 'String',
				'description' => __( 'Description for archive pages.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
