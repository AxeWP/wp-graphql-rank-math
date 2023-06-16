<?php
/**
 * The shared SEO fields interface.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 * @since 0.0.8
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use RankMath\Helper;
use WPGraphQL\RankMath\Model\ContentNodeSeo;
use WPGraphQL\RankMath\Model\ContentTypeSeo;
use WPGraphQL\RankMath\Model\TermNodeSeo;
use WPGraphQL\RankMath\Model\UserSeo;
use WPGraphQL\RankMath\Type\WPObject\Breadcrumbs;
use WPGraphQL\RankMath\Type\WPObject\JsonLd;
use WPGraphQL\RankMath\Type\WPObject\OpenGraphMeta;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeResolverTrait;

/**
 * Class - Seo
 */
class Seo extends InterfaceType {
	use TypeResolverTrait;

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'Seo';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Base SEO fields shared across WP types.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		$fields = [
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
			'fullHead'        => [
				'type'        => 'String',
				'description' => __( 'The fully-rendered `head` tag for the given item', 'wp-graphql-rank-math' ),
			],
			'jsonLd'          => [
				'type'        => JsonLd::get_type_name(),
				'description' => __( 'The JSON+LD data', 'wp-graphql-rank-math' ),
			],
			'openGraph'       => [
				'type'        => OpenGraphMeta::get_type_name(),
				'description' => __( 'The open graph meta properties.', 'wp-graphql-rank-math' ),
			],
			
		];

		// Add breadcrumbs field.
		if ( Helper::is_breadcrumbs_enabled() ) {
			$fields['breadcrumbs'] = [
				'type'        => [ 'list_of' => Breadcrumbs::get_type_name() ],
				'description' => __( 'The breadcrumbs trail for the given object', 'wp-graphql-rank-math' ),
			];
		}

		return $fields;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param \WPGraphQL\Model\Model $value The value from the resolver of the parent field.
	 */
	public static function get_resolved_type_name( $value ): ?string {
		switch ( true ) {
			case $value instanceof ContentNodeSeo:
				$type_name = 'RankMath' . graphql_format_type_name( $value->get_object_type() . 'ObjectSeo' );
				break;
			case $value instanceof ContentTypeSeo:
				$type_name = 'RankMath' . graphql_format_type_name( $value->get_object_type() . 'TypeSeo' );
				break;
			case $value instanceof TermNodeSeo:
				$type_name = 'RankMath' . graphql_format_type_name( $value->get_object_type() . 'TermSeo' );
				break;
			case $value instanceof UserSeo:
				$type_name = graphql_format_type_name( 'RankMathUserSeo' );
				break;
			default:
				$type_name = null;
		}

		/**
		 * Filters the GraphQL Object type name for the given SEO model.
		 *
		 * @param string|null $type_name The GraphQL type name for the SEO Object.
		 * @param \WPGraphQL\Model\Model $model The SEO model for the type.
		 */
		$type_name = apply_filters( 'graphql_seo_resolved_type_name', $type_name, $value );

		return $type_name;
	}
}
