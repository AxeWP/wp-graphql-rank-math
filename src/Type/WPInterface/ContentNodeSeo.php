<?php
/**
 * Interface for ContentNode Seo fields.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 * @since 0.0.8
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use WPGraphQL\RankMath\Type\WPObject\SeoScore;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeResolverTrait;

/**
 * Class - ContentNodeSeo
 */
class ContentNodeSeo extends InterfaceType implements TypeWithInterfaces {
	use TypeResolverTrait;

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'ContentNodeSeo';
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_type_config(): array {
		$config = parent::get_type_config();

		$config['eagerlyLoadType'] = true;

		return $config;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The seo data for Post Objects', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'isPillarContent' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the item is considered pillar (cornerstone) content', 'wp-graphql-rank-math' ),
			],
			'seoScore'        => [
				'type'        => SeoScore::get_type_name(),
				'description' => __( 'The SEO score', 'wp-graphql-rank-math' ),
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [ Seo::get_type_name() ];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param \WPGraphQL\Model\Model $value The model.
	 */
	public static function get_resolved_type_name( $value ): ?string {
		$type_name = null;

		if ( isset( $value->post_type ) ) {
			$type_name = 'RankMath' . graphql_format_type_name( $value->post_type . 'ObjectSeo' );
		}

		return $type_name;
	}
}
