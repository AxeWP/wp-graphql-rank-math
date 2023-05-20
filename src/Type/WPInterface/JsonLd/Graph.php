<?php
/**
 * The JSON+LD @graph interface.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface\JsonLd
 */

namespace WPGraphQL\RankMath\Type\WPInterface\JsonLd;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeResolverTrait;
use WPGraphQL\RankMath\Type\Enum\JsonLdGraphTypeEnum;
use GraphQL\Error\UserError;
use WPGraphQL\RankMath\Type\WPObject\JsonLd;

/**
 * Class - Graph
 */
class Graph extends InterfaceType {
	use TypeResolverTrait;

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLdGraph';
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_type_config() : array {
		$config = parent::get_type_config();

		$config['eagerlyLoadType'] = true;

		return $config;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description() : string {
		return __( 'The associated JSON+LD @graph objects.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		$fields = [
			'type' => [
				'type'        => [ 'list_of' => JsonLdGraphTypeEnum::get_type_name() ],
				'description' => __( 'The JSON+LD graph object `@type`', 'wp-graphql-rank-math' ),
			],
			'id'   => [
				'type'        => 'String',
				'description' => __( 'The JSON+LD graph object ID.', 'wp-graphql-rank-math' ),
			],
			'name' => [
				'type'        => 'String',
				'description' => __( 'The JSON+LD graph object name.', 'wp-graphql-rank-math' ),
			],
		];

		return $fields;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param mixed $value the value passed from the parent field.
	 *
	 * @throws UserError .
	 */
	public static function get_resolved_type_name( $value ): ?string {
		if ( empty( $value['type'] ) ) {
			throw new UserError(
				__( 'Invalid JSON+LD graph object. The @type property is missing.', 'wp-graphql-rank-math' )
			);
		}

		if ( is_array( $value['type'] ) ) {
			$value['type'] = $value['type'][0];
		}

		$possible_types = self::get_possible_types();
		if ( isset( $possible_types[ $value['type'] ] ) ) {
			return $possible_types[ $value['type'] ];
		}

		throw new UserError(
			sprintf( 
				// translators: the JSON+LD graph type.
				__( 'The schema.org @type %s is not supported.', 'wp-graphql-rank-math' ),
				$value['type']
			)
		);
	}

	/**
	 * Get the possible Schema.org types and their corresponding GraphQL type name
	 */
	public static function get_possible_types() : array {
		return [
			'Article'        => JsonLd\Article::get_type_name(),
			'CollectionPage' => JsonLd\CollectionPage::get_type_name(),
			'Person'         => JsonLd\Person::get_type_name(),
			'SearchAction'   => JsonLd\SearchAction::get_type_name(),
			'WebPage'        => JsonLd\WebPage::get_type_name(),
			'WebSite'        => JsonLd\WebSite::get_type_name(),
		];
	}
}
