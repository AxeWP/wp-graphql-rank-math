<?php
/**
 * Interface for a Node with SEO data.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 * @since @todo
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use AxeWP\GraphQL\Abstracts\InterfaceType;
use AxeWP\GraphQL\Interfaces\TypeWithInterfaces;
use GraphQL\Error\UserError;
use WPGraphQL\Model\Model;
use WPGraphQL\RankMath\Model\ContentNodeSeo;
use WPGraphQL\RankMath\Model\ContentTypeSeo;
use WPGraphQL\RankMath\Model\TermNodeSeo;
use WPGraphQL\RankMath\Model\UserSeo;

/**
 * Class - NodeWithSeo
 */
class NodeWithSeo extends InterfaceType implements TypeWithInterfaces {
	/**
	 * {@inheritDoc}
	 *
	 * @param \WPGraphQL\Registry\TypeRegistry $type_registry
	 */
	public static function register( $type_registry = null ): void {
		self::$type_registry = $type_registry;

		register_graphql_interface_type( static::type_name(), static::get_type_config() );

		/**
		 * Filters the GraphQL types that have SEO data.
		 * This is used to register the NodeWithSeo interface to the types.
		 *
		 * @param array $types_with_seo The types that have SEO data.
		 * @since @todo
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

		register_graphql_interfaces_to_types( self::type_name(), $types_with_seo );
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'NodeWithRankMathSeo';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'A node with RankMath SEO data.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		return [
			'seo' => [
				'type'        => Seo::get_type_name(),
				'description' => __( 'The RankMath SEO data for the node.', 'wp-graphql-rank-math' ),
				'resolve'     => function( $source ) {
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
	public static function get_interfaces() : array {
		return [ 'Node' ];
	}

	/**
	 * Gets the SEO model class for a given node model.
	 *
	 * @param Model $node_model The node model.
	 */
	private static function get_model_for_node( Model $node_model ) : ?Model {
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
		 * @since @todo
		 *
		 * @param Model|null $seo_model The SEO model class to use.
		 * @param Model       $node_model The Model for the node.
		 */
		$seo_model = apply_filters( 'graphql_seo_resolved_model', $seo_model, $node_model );

		return $seo_model;
	}
}
