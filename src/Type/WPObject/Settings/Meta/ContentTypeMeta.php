<?php
/**
 * The ContentTypeMeta GraphQL object.
 *
 * @package WPGraphQL\RankMath\Type\WPObject\Settings\Meta
 */

namespace WPGraphQL\RankMath\Type\WPObject\Settings\Meta;

use GraphQL\Error\UserError;
use RankMath\Helper;
use WPGraphQL\AppContext;
use WPGraphQL\RankMath\Type\Enum\ArticleTypeEnum;
use WPGraphQL\RankMath\Type\Enum\BulkEditingTypeEnum;
use WPGraphQL\RankMath\Type\Enum\SnippetTypeEnum;
use WPGraphQL\RankMath\Type\WPInterface\MetaSettingWithArchive;
use WPGraphQL\RankMath\Type\WPInterface\MetaSettingWithRobots;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType;

/**
 * Class - ContentTypeMeta
 */
class ContentTypeMeta extends ObjectType {
	/**
	 * {@inheritDoc}
	 */
	protected static function type_name(): string {
		return 'ContentTypeMetaSettings';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'The RankMath SEO Post Type settings.', 'wp-graphql-rank-math' );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function register(): void {
		/** @var \WP_Post_Type[] */
		$allowed_post_types = \WPGraphQL::get_allowed_post_types( 'objects', [ 'public' => true ] );

		foreach ( $allowed_post_types as $post_type_object ) {
			// Skip attachment meta if redirection is enabled.
			if ( 'attachment' === $post_type_object->name && Helper::get_settings( 'general.attachment_redirect_urls', true ) ) {
				continue;
			}

			$interfaces = [
				MetaSettingWithRobots::get_type_name(),
			];
			if ( $post_type_object->has_archive ) {
				$interfaces[] = MetaSettingWithArchive::get_type_name();
			}

			register_graphql_object_type(
				ucfirst( $post_type_object->graphql_single_name ) . 'MetaSettings',
				[
					'description' => sprintf(
					// translators: post type name.
						__( 'The RankMath SEO meta settings for %s.', 'wp-graphql-rank-math' ),
						$post_type_object->label,
					),
					'interfaces'  => $interfaces,
					'fields'      => self::get_child_type_fields( $post_type_object ),
				]
			);
		}

		parent::register();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_fields(): array {
		/** @var \WP_Post_Type[] */
		$allowed_post_types = \WPGraphQL::get_allowed_post_types( 'objects', [ 'public' => true ] );

		$fields = [];

		foreach ( $allowed_post_types as $post_type_object ) {

			// Skip attachment meta if redirection is enabled.
			if ( 'attachment' === $post_type_object->name && Helper::get_settings( 'general.attachment_redirect_urls', true ) ) {
				continue;
			}

			$fields[ lcfirst( $post_type_object->graphql_single_name ) ] = [
				'type'        => $post_type_object->graphql_single_name . 'MetaSettings',
				'description' => sprintf(
					// translators: post type name.
					__( 'The RankMath SEO meta settings for %s.', 'wp-graphql-rank-math' ),
					$post_type_object->label,
				),
			];
		}

		return $fields;
	}

	/**
	 * Get the fields for the provided content type.
	 *
	 * @param \WP_Post_Type $post_type_object .
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_child_type_fields( \WP_Post_Type $post_type_object ): array {
		$fields = [
			'title'                   => [
				'type'        => 'String',
				'description' => sprintf(
					// translators: post type label.
					__( 'Default title tag for single %s pages.', 'wp-graphql-rank-math' ),
					$post_type_object->label,
				),
			],
			'description'             => [
				'type'        => 'String',
				'description' => sprintf(
					// translators: post type label.
					__( 'Default description for single %s pages.', 'wp-graphql-rank-math' ),
					$post_type_object->label,
				),
			],
			'snippetType'             => [
				'type'        => SnippetTypeEnum::get_type_name(),
				'description' => sprintf(
					// translators: post type label.
					__( 'Default rich snippet select when creating a new %s.', 'wp-graphql-rank-math' ),
					$post_type_object->label,
				),
			],
			'articleType'             => [
				'type'        => ArticleTypeEnum::get_type_name(),
				'description' => sprintf(
					// translators: post type label.
					__( 'Default article type when creating a new %s.', 'wp-graphql-rank-math' ),
					$post_type_object->label,
				),
			],
			'snippetHeadline'         => [
				'type'        => 'String',
				'description' => __( 'Default rich snippet headline.', 'wp-graphql-rank-math' ),
			],
			'snippetDescription'      => [
				'type'        => 'String',
				'description' => __( 'Default rich snippet headline.', 'wp-graphql-rank-math' ),
			],
			'hasCustomRobotsMeta'     => [
				'type'        => 'Boolean',
				'description' => __( 'Whether custom robots meta for author page are set. Otherwise the default meta will be used, as set in the Global Meta tab.', 'wp-graphql-rank-math' ),
			],
			'hasLinkSuggestions'      => [
				'type'        => 'Boolean',
				'description' => __( 'Whether Link Suggestions meta box and the Pillar Content featured are enabled for this post type.', 'wp-graphql-rank-math' ),
			],
			'shouldUseFocusKeyword'   => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to use the Focus Keyword as the default text for the links instead of the post titles.', 'wp-graphql-rank-math' ),
			],
			'hasBulkEditing'          => [
				'type'        => BulkEditingTypeEnum::get_type_name(),
				'description' => __( 'Whether to list bulk editing columns to the post listing screen.', 'wp-graphql-rank-math' ),
			],
			'socialImage'             => [
				'type'        => 'MediaItem',
				'description' => __( 'The default image to display when sharing this post type on social media', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source, array $args, AppContext $context ) {
					return ! empty( $source['socialImage'] ) ? $context->get_loader( 'post' )->load_deferred( $source['socialImage'] ) : null;
				},
			],
			'hasSlackEnhancedSharing' => [
				'type'        => 'Boolean',
				'description' => __( 'Whether to show additional information (name & total number of posts) when an author archive is shared on Slack.', 'wp-graphql-rank-math' ),
			],
			'hasSeoControls'          => [
				'type'        => 'Boolean',
				'description' => __( 'Whether the SEO Controls meta box for user profile pages is enabled.', 'wp-graphql-rank-math' ),
			],
			'analyzedFields'          => [
				'type'        => [ 'list_of' => 'String' ],
				'description' => __( 'List of custom fields name to include in the Page analysis', 'wp-graphql-rank-math' ),
			],
		];

		$all_taxonomies = Helper::get_object_taxonomies( $post_type_object->name );
		$all_taxonomies = is_array( $all_taxonomies ) && ! empty( $all_taxonomies ) ? $all_taxonomies : [];

		$allowed_taxonomies = \WPGraphQL::get_allowed_taxonomies( 'names', [ 'public' => true ] );

		$taxonomies = array_intersect( $all_taxonomies, $allowed_taxonomies );

		if ( ! empty( $taxonomies ) ) {
			$fields['primaryTaxonomy'] = [
				'type'        => 'TaxonomyEnum',
				'description' => __( 'The taxonomy used with the Primary Term Feature and displayed in the Breadcrumbs.', 'wp-graphql-rank-math' ),
				'resolve'     => static function ( $source ) use ( $allowed_taxonomies ) {
					if ( ! in_array( $source, $allowed_taxonomies, true ) ) {
						throw new UserError(
							sprintf(
								// translators: taxonomy name.
								__( 'The %s post type is not available in WPGraphQL', 'wp-graphql-rank-math' ),
								$source
							)
						);
					}
				},
			];
		}

		if ( $post_type_object->has_archive ) {
			unset( $fields['socialImage'] );
		}

		if ( in_array( $post_type_object->name, [ 'product', 'download', 'rank_math_locations' ], true ) ) {
			unset( $fields['snippetDescription'] );
			unset( $fields['snippetHeadline'] );
		}

		if ( 'attachment' === $post_type_object->name ) {
			unset( $fields['hasLinkSuggestions'] );
			unset( $fields['shouldUseFocusKeyword'] );
			unset( $fields['hasSlackEnhancedSharing'] );
		}

		if ( defined( 'WEBSTORIES_VERSION' ) && 'web-story' === $post_type_object->name ) {
			unset( $fields['snippetDescription'] );
			unset( $fields['description'] );
			unset( $fields['hasLinkSuggestions'] );
			unset( $fields['shouldUseFocusKeyword'] );
			unset( $fields['analyzedFields'] );
			unset( $fields['hasBulkEditing'] );
			unset( $fields['hasSeoControls'] );
		}

		return $fields;
	}
}
