<?php
/**
 * The TaxonomyMeta GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Meta
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Meta;

use WPGraphQL\RankMath\Type\WPInterface\MetaSettingWithArchive;
use WPGraphQL\RankMath\Type\WPInterface\MetaSettingWithRobots;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - TaxonomyMeta
 */
class TaxonomyMeta extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'TaxonomyMetaSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Taxonomy meta settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register(): void {
		/** @var \WP_Taxonomy[] */
		$allowed_taxonomies = \WPGraphQL::get_allowed_taxonomies( 'objects', [ 'public' => true ] );

		foreach ( $allowed_taxonomies as $tax_object ) {
			$interfaces = [
				MetaSettingWithRobots::get_type_name(),
				MetaSettingWithArchive::get_type_name(),
			];

			register_graphql_object_type(
				ucfirst( $tax_object->graphql_single_name ) . 'MetaSettings',
				[
					'description' => sprintf(
					// translators: post type name.
						__( 'The RankMath SEO meta settings for %s.', 'wp-graphql-rank-math' ),
						$tax_object->label,
					),
					'interfaces'  => $interfaces,
					'fields'      => self::get_child_type_fields( $tax_object ),
				]
			);
		}

		parent::register();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		/** @var \WP_Taxonomy[] */
		$allowed_taxonomies = \WPGraphQL::get_allowed_taxonomies( 'objects', [ 'public' => true ] );

		$fields = [];

		foreach ( $allowed_taxonomies as $tax_object ) {
			$fields[ lcfirst( $tax_object->graphql_single_name ) ] = [
				'type'        => $tax_object->graphql_single_name . 'MetaSettings',
				'description' => sprintf(
					// translators: taxonomy name.
					__( 'The RankMath SEO meta settings for %s.', 'wp-graphql-rank-math' ),
					$tax_object->label,
				),
			];
		}

		return $fields;
	}

	/**
	 * Get the fields for the provided content type.
	 *
	 * @param \WP_Taxonomy $tax_object .
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function get_child_type_fields( \WP_Taxonomy $tax_object ): array {
		$fields = [
			'hasCustomRobotsMeta'     => [
				'type'        => 'Boolean',
				'description' => __( 'Whether custom robots meta for author page are set. Otherwise the default meta will be used, as set in the Global Meta tab.', 'wp-graphql-rank-math' ),
			],
			'hasSlackEnhancedSharing' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to show additional information (name & total number of posts) when an author archive is shared on Slack.', 'wp-graphql-rank-math' ),
			],
		];

		if ( 'post_format' !== $tax_object->name ) {
			$fields['hasSeoControls'] = [
				'type'        => 'Boolean',
				'description' => __( 'Whether the SEO Controls meta box for user profile pages is enabled.', 'wp-graphql-rank-math' ),
			];
			$fields['hasSnippetData'] = [
				'type'        => 'Boolean',
				'description' => __( 'Whether to include snippet data for this taxonomy.', 'wp-graphql-rank-math' ),
			];
		}

		return $fields;
	}
}
