<?php
/**
 * Registers fields to RootQuery
 *
 * @package WPGraphQL\Fields
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Fields;

use WPGraphQL\AppContext;
use WPGraphQL\RankMath\Modules\Redirection\Data\Loader\RedirectionsLoader;
use WPGraphQL\RankMath\Modules\Redirection\Type\WPObject\Redirection;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\FieldsType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeNameTrait;
use WPGraphQL\Utils\Utils;

/**
 * Class - RootQuery
 */
class RootQuery extends FieldsType {
	use TypeNameTrait;

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'RootQuery';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public static function get_type_name(): string {
		return static::type_name();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'redirection' => [
				'type'        => Redirection::get_type_name(),
				'args'        => [
					'id' => [
						'type'        => 'ID',
						'description' => __( 'The ID of the redirection. Accepts either a global or database ID.', 'wp-graphql-rank-math' ),
					],
				],
				'description' => __( 'RankMath SEO redirection', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source, array $args, AppContext $context ) {
					$database_id = Utils::get_database_id_from_id( $args['id'] );

					return ! empty( $database_id ) ? $context->get_loader( RedirectionsLoader::$name )->load( $database_id ) : null;
				},
			],
		];
	}
}
