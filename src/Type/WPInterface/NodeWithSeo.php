<?php
/**
 * Interface for a Node with SEO data.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 * @since 0.0.8
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use GraphQL\Error\UserError;
use WPGraphQL\Model\Model;
use WPGraphQL\RankMath\Model\ContentNodeSeo;
use WPGraphQL\RankMath\Model\ContentTypeSeo;
use WPGraphQL\RankMath\Model\TermNodeSeo;
use WPGraphQL\RankMath\Model\UserSeo;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - NodeWithSeo
 */
class NodeWithSeo extends InterfaceType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 */
	public static function register(): void {
		register_graphql_interface_type( static::type_name(), static::get_type_config() );

		/**
		 * Filters the GraphQL types that have SEO data.
		 * This is used to register the NodeWithSeo interface to the types.
		 *
		 * @since 0.0.8
		 *
		 * @param array $types_with_seo The types that have SEO data.
		 */
		$types_with_seo = apply_filters(
			'graphql_seo_types_with_seo',
			[
				'User',
				'TermNode',
				'ContentType',
				'ContentNode',
			]
		);

		// @todo only apply to ContentTypes that have SEO data.

		register_graphql_interfaces_to_types( self::type_name(), $types_with_seo );
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'NodeWithRankMathSeo';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'A node with RankMath SEO data.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'seo' => [
				'type'        => Seo::get_type_name(),
				'description' => __( 'The RankMath SEO data for the node.', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source ) {
					if ( ! $source instanceof Model ) {
						return null;
					}

					$model = self::get_model_for_node( $source );

					if ( empty( $model ) ) {
						throw new UserError(
							sprintf(
								/* translators: %s: The name of the node type */
								__( 'The %s type does not have a corresponding SEO model class.', 'wp-graphql-rank-math' ),
								get_class( $source )
							)
						);
					}

					return $model;
				},
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [ 'Node' ];
	}

	/**
	 * Gets the SEO model class for a given node model.
	 *
	 * @param \WPGraphQL\Model\Model $node_model The node model.
	 */
	private static function get_model_for_node( Model $node_model ): ?Model {
		// A map of the node models to their corresponding SEO model classes.
		switch ( true ) {
			case $node_model instanceof \WPGraphQL\Model\Post:
				$seo_model = new ContentNodeSeo( $node_model->ID );
				break;
			case $node_model instanceof \WPGraphQL\Model\PostType:
				$seo_model = new ContentTypeSeo( $node_model->name );
				break;
			case $node_model instanceof \WPGraphQL\Model\Term:
				$seo_model = new TermNodeSeo( $node_model->databaseId );
				break;
			case $node_model instanceof \WPGraphQL\Model\User:
				$seo_model = new UserSeo( $node_model->databaseId );
				break;
			default:
				$seo_model = null;
		}

		/**
		 * Filter the SEO model class used for a given node model.
		 *
		 * @since 0.0.8
		 *
		 * @param \WPGraphQL\Model\Model|null $seo_model The SEO model class to use.
		 * @param \WPGraphQL\Model\Model $node_model The Model for the node.
		 */
		$seo_model = apply_filters( 'graphql_seo_resolved_model', $seo_model, $node_model );

		return $seo_model;
	}
}
