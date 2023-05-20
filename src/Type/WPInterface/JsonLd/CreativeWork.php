<?php
/**
 * The Schema.org CreativeWork interface.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface\JsonLd
 */

namespace WPGraphQL\RankMath\Type\WPInterface\JsonLd;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInterfaces;

/**
 * Class - CreativeWork
 */
class CreativeWork extends InterfaceType implements TypeWithInterfaces {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'JsonLdCreativeWork';
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
		return __( 'A schema.org CreativeWork', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_interfaces(): array {
		return [
			Graph::get_type_name(),
			Thing::get_type_name(),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
		$fields = [
			'publisher'       => [ // @todo.
				'type'        => 'String',
				'description' => __( 'The publisher of the creative work.', 'wp-graphql-rank-math' ),
			],
			'inLanguage'      => [ // @todo.
				'type'        => 'String',
				'description' => __( 'The language of the creative work.', 'wp-graphql-rank-math' ),
			],
			'potentialAction' => [
				'type'        => [ 'list_of' => Action::get_type_name() ],
				'description' => __( 'The language of the creative work.', 'wp-graphql-rank-math' ),
			],
			'isPartOf'        => [
				'type'        => self::get_type_name(),
				'description' => __( 'Indicates an item or CreativeWork that this item, or CreativeWork (in some sense), is part of.', 'wp-graphql-rank-math' ),
			],
		];

		return $fields;
	}
}
