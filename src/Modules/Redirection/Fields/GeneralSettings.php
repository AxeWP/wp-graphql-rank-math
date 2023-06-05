<?php
/**
 * Registers fields to General
 *
 * @package WPGraphQL\Fields
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Fields;

use RankMath;
use WPGraphQL\RankMath\Modules\Redirection\Model\Redirection as ModelRedirection;
use WPGraphQL\RankMath\Modules\Redirection\Type\WPObject\Redirection;
use WPGraphQL\RankMath\Modules\Redirection\Type\WPObject\RedirectionSettings;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\FieldsType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeNameTrait;

/**
 * Class - GeneralSettings
 */
class GeneralSettings extends FieldsType {
	use TypeNameTrait;

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'General';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		return [
			'redirections'     => [
				'type'        => RedirectionSettings::get_type_name(),
				'description' => __( 'RankMath SEO redirection settings', 'wp-graphql-rank-math' ),
			],
			'redirectionQuery' => [
				'type'        => [ 'list_of' => Redirection::get_type_name() ],
				'description' => __( 'RankMath SEO redirections', 'wp-graphql-rank-math' ),
				'resolve'     => static function () {
					$redirections = RankMath\Redirections\DB::get_redirections();

					return array_map(
						static function ( $redirection ) {
							return new ModelRedirection( $redirection );
						},
						$redirections['redirections']
					);
				},
			],
		];
	}
}
