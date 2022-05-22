<?php
/**
 * The shared SEO fields interface.
 *
 * @package WPGraphQL\RankMath\Type\WPInterface
 */

namespace WPGraphQL\RankMath\Type\WPInterface;

use AxeWP\GraphQL\Abstracts\InterfaceType;
use RankMath\Frontend\Breadcrumbs as RMBreadcrumbs;
use RankMath\Helper;
use WPGraphQL\RankMath\Model\UserSeo;
use WPGraphQL\RankMath\Type\WPObject\Breadcrumbs;
use WPGraphQL\RankMath\Type\WPObject\JsonLd;

/**
 * Class - BaseSeoFields
 */
class BaseSeoFields extends InterfaceType {

	/**
	 * {@inheritDoc}
	 */
	protected static function type_name() : string {
		return 'BaseSeoFields';
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
		return __( 'Base SEO fields shared across WP types.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields() : array {
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
			'jsonLd'          => [
				'type'        => JsonLd::get_type_name(),
				'description' => __( 'The JSON+LD data', 'wp-graphql-rank-math' ),
			],
			'fullHead'        => [
				'type'        => 'String',
				'description' => __( 'The fully-rendered `head` tag for the given item', 'wp-graphql-rank-math' ),
			],
		];

		// Add breadcrumbs field.
		if ( Helper::is_breadcrumbs_enabled() ) {
			$fields['breadcrumbs'] = [
				'type'        => [ 'list_of' => Breadcrumbs::get_type_name() ],
				'description' => __( 'The breadcrumbs trail for the given object', 'wp-graphql-rank-math' ),
				'resolve'     => function ( $source ) {
					if ( $source instanceof UserSeo ) {
						// RankMath uses the global $author for generating crumbs.
						global $author;

						// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
						$author = $source->ID;
					}

					// Get the crumbs and shape them.
					$crumbs      = RMBreadcrumbs::get()->get_crumbs();
					$breadcrumbs = array_map(
						function( $crumb ) {
							return [
								'text'     => $crumb[0] ?? null,
								'url'      => $crumb[1] ?? null,
								'isHidden' => ! empty( $crumb['hide_in_schema'] ),
							];
						},
						$crumbs
					);

					// Pop the current item's title.
					$remove_title = ( is_single( $source->database_id ) || is_page( $source->database_id ) ) && Helper::get_settings( 'general.breadcrumbs_remove_post_title' );
					if ( $remove_title ) {
						array_pop( $breadcrumbs );
					}

					return ! empty( $breadcrumbs ) ? $breadcrumbs : null;
				},
			];
		}

		return $fields;
	}
}
