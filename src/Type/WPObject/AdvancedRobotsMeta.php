<?php
/**
 * The Rank Math general settings GraphQL Object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject
 */

namespace WPGraphQL\RankMath\Type\WPObject;

use WPGraphQL\RankMath\Type\Enum\ImagePreviewSizeEnum;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - AdvancedRobotsMeta
 */
class AdvancedRobotsMeta extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'AdvancedRobotsMeta';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The config for an advanced robots meta values.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'hasSnippet'       => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to specify a maximum text length of a snippet of your page', 'wp-graphql-rank-math' ),
			],
			'snippetLength'    => [
				'type'        => 'Int',
				'description' => __( 'The maximum text length (in characters) of the snippet. -1 for no limit.', 'wp-graphql-rank-math' ),
			],
			'hasVideoPreview'  => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to specify a maximum duration of an animated video preview.', 'wp-graphql-rank-math' ),
			],
			'videoDuration'    => [
				'type'        => 'Int',
				'description' => __( 'The maximum duration (seconds characters) of the snippet. -1 for no limit.', 'wp-graphql-rank-math' ),
			],
			'hasImagePreview'  => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to specify a maximum size of image preview to be shown for images on the page.', 'wp-graphql-rank-math' ),
			],
			'imagePreviewSize' => [
				'type'        => ImagePreviewSizeEnum::get_type_name(),
				'description' => __( 'The maximum size of image preview to be shown for images.', 'wp-graphql-rank-math' ),
			],
		];
	}
}
