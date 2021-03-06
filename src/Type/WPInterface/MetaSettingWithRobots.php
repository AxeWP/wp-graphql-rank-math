<?php
/**
 * Interface for meta settings with robots fields.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Type\Enum\RobotsMetaValueEnum;
use WPGraphQL\RankMath\Type\WPObject\AdvancedRobotsMeta;

/**
 * Class - Settings
 */
class MetaSettingWithRobots extends InterfaceType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'MetaSettingWithRobots';
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
		return __( 'Meta settings with robots fields.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'advancedRobotsMeta' => [
				'type'        => AdvancedRobotsMeta::get_type_name(),
				'description' => __( 'Advanced robots meta tag settings.', 'wp-graphql-rank-math' ),
			],
			'robotsMeta'         => [
				'type'        => [ 'list_of' => RobotsMetaValueEnum::get_type_name() ],
				'description' => __( 'Custom values for robots meta tag.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
