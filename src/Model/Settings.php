<?php
/**
 * Settings Model class
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use Exception;
use RankMath\Helper;
use RankMath\Sitemap\Router;
use WPGraphQL\Model\Model;

/**
 * Class - Settings
 */
class Settings extends Model {
	/**
	 * {@inheritDoc}
	 *
	 * @var array<string, mixed>
	 */
	protected $data;

	/**
	 * Array of active modules
	 * 
	 * @var string[]
	 */
	protected array $active_modules;

	/**
	 * Constructor.
	 *
	 * @throws \Exception .
	 */
	public function __construct() {
		/** @property \RankMath\Settings $settings_obj */
		$settings_obj = rank_math()->settings;
		$settings     = $settings_obj->all();

		if ( empty( $settings ) ) {
			throw new Exception( __( 'The Rank Math settings cannot be found', 'wp-graphql-rank-math' ) );
		}

		$this->data = $settings;

		$this->active_modules = Helper::get_active_modules();

		parent::__construct();
	}

	/**
	 * Initializes the object
	 *
	 * @return void
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'general' => fn () => $this->general_fields(),
				'meta'    => fn () => [
					'authorArchives'               => $this->meta_author_archive_fields(),
					'global'                       => $this->meta_global_fields(),
					'local'                        => $this->meta_local_fields(),
					'social'                       => $this->meta_social_fields(),
					'homepage'                     => $this->meta_homepage_fields(),
					'dateArchives'                 => $this->meta_date_archive_fields(),
					'contentTypes'                 => $this->meta_content_type_fields(),
					'taxonomies'                   => $this->meta_taxonomy_fields(),
					'notFoundTitle'                => ! empty( $this->data['titles']['404_title'] ) ? $this->data['titles']['404_title'] : null,
					'searchTitle'                  => ! empty( $this->data['titles']['search_title'] ) ? $this->data['titles']['search_title'] : null,
					'shouldIndexSearch'            => empty( $this->data['titles']['noindex_search'] ),
					'shouldIndexPaginatedPages'    => empty( $this->data['titles']['noindex_paginated_pages'] ),
					'shouldIndexArchiveSubpages'   => empty( $this->data['titles']['noindex_archive_subpages'] ),
					'shouldIndexPasswordProtected' => empty( $this->data['titles']['noindex_password_protected'] ),
				],
			];

			if ( in_array( 'sitemap', $this->active_modules, true ) ) {
				$this->fields['sitemap'] = fn () => [
					'author'          => $this->sitemap_author_fields(),
					'contentTypes'    => $this->sitemap_content_type_fields(),
					'general'         => $this->sitemap_general_fields(),
					'sitemapIndexUrl' => rank_math_get_sitemap_url(),
					'taxonomies'      => $this->sitemap_taxonomy_fields(),
				];
			}
		}
	}

	/**
	 * Resolve the general settings fields.
	 *
	 * @return array<string, mixed>
	 */
	private function general_fields(): array {
		return [
			'breadcrumbs'         => function (): array {
				$has_home = ! empty( $this->data['general']['breadcrumbs_home'] );

				return [
					'archiveFormat'         => ! empty( $this->data['general']['breadcrumbs_archive_format'] ) ? $this->data['general']['breadcrumbs_archive_format'] : null,
					'hasPostTitle'          => empty( $this->data['general']['breadcrumbs_remove_post_title'] ),
					'hasAncestorCategories' => ! empty( $this->data['general']['breadcrumbs_ancestor_categories'] ),
					'hasTaxonomyName'       => empty( $this->data['general']['breadcrumbs_hide_taxonomy_name'] ),
					'hasBlogPage'           => ! empty( $this->data['general']['breadcrumbs_blog_page'] ),
					'hasHome'               => $has_home,
					'homeLabel'             => function () use ( $has_home ): ?string {
						if ( ! $has_home ) {
							return null;
						}
						return ! empty( $this->data['general']['breadcrumbs_home_label'] ) ? $this->data['general']['breadcrumbs_home_label'] : null;
					},
					'homeUrl'               => function () use ( $has_home ): ?string {
						if ( ! $has_home ) {
							return null;
						}
						$value = ! empty( $this->data['general']['breadcrumbs_home_link'] ) ? $this->data['general']['breadcrumbs_home_link'] : null;
						return ! empty( $value ) ? $value : null;
					},
					'notFoundLabel'         => ! empty( $this->data['general']['breadcrumbs_404_label'] ) ? $this->data['general']['breadcrumbs_404_label'] : null,
					'prefix'                => ! empty( $this->data['general']['breadcrumbs_prefix'] ) ? $this->data['general']['breadcrumbs_prefix'] : null,
					'separator'             => ! empty( $this->data['general']['breadcrumbs_separator'] ) ? $this->data['general']['breadcrumbs_separator'] : null,
					'searchFormat'          => ! empty( $this->data['general']['breadcrumbs_search_format'] ) ? $this->data['general']['breadcrumbs_search_format'] : null,
				];
			},
			'frontendSeoScore'    => [
				'enabledPostTypes'    => ! empty( $this->data['general']['frontend_seo_score_post_types'] ) ? $this->data['general']['frontend_seo_score_post_types'] : null,
				'template'            => ! empty( $this->data['general']['frontend_seo_score_template'] ) ? $this->data['general']['frontend_seo_score_template'] : null,
				'position'            => ! empty( $this->data['general']['frontend_seo_score_position'] ) ? $this->data['general']['frontend_seo_score_position'] : null,
				'hasRankMathBacklink' => ! empty( $this->data['general']['support_rank_math'] ),
			],
			'hasBreadcrumbs'      => ! empty( $this->data['general']['breadcrumbs'] ),
			'hasFrontendSeoScore' => ! empty( $this->data['general']['frontend_seo_score'] ),
			'links'               => [
				'defaultAttachmentRedirectUrl' => ! empty( $this->data['general']['attachment_redirect_default'] ) ? $this->data['general']['attachment_redirect_default'] : null,
				'hasCategoryBase'              => empty( $this->data['general']['strip_category_base'] ),
				'nofollowDomains'              => ! empty( $this->data['general']['nofollow_domains'] ) ? $this->data['general']['nofollow_domains'] : null,
				'nofollowExcludedDomains'      => ! empty( $this->data['general']['nofollow_exclude_domains'] ) ? $this->data['general']['nofollow_exclude_domains'] : null,
				'shouldNofollowImageLinks'     => ! empty( $this->data['general']['nofollow_image_links'] ),
				'shouldNofollowLinks'          => ! empty( $this->data['general']['nofollow_external_links'] ),
				'shouldOpenInNewWindow'        => ! empty( $this->data['general']['new_window_external_links'] ),
				'shouldRedirectAttachments'    => ! empty( $this->data['general']['attachment_redirect_urls'] ),
			],
			'webmaster'           => [
				'baidu'     => ! empty( $this->data['general']['baidu_verify'] ) ? $this->data['general']['baidu_verify'] : null,
				'bing'      => ! empty( $this->data['general']['bing_verify'] ) ? $this->data['general']['bing_verify'] : null,
				'google'    => ! empty( $this->data['general']['google_verify'] ) ? $this->data['general']['google_verify'] : null,
				'norton'    => ! empty( $this->data['general']['norton_verify'] ) ? $this->data['general']['norton_verify'] : null,
				'pinterest' => ! empty( $this->data['general']['pinterest_verify'] ) ? $this->data['general']['pinterest_verify'] : null,
				'yandex'    => ! empty( $this->data['general']['yandex_verify'] ) ? $this->data['general']['yandex_verify'] : null,
			],
			'rssBeforeContent'    => ! empty( $this->data['general']['rss_before_content'] ) ? $this->data['general']['rss_before_content'] : null,
			'rssAfterContent'     => ! empty( $this->data['general']['rss_after_content'] ) ? $this->data['general']['rss_after_content'] : null,
			'redirections'        => [
				'hasDebug'          => ! empty( $this->data['general']['redirect_debug'] ),
				'fallbackBehavior'  => ! empty( $this->data['general']['redirections_fallback'] ) ? $this->data['general']['redirections_fallback'] : 'default',
				'fallbackCustomUrl' => ! empty( $this->data['general']['redirections_custom_url'] ) ? $this->data['general']['redirections_custom_url'] : null,
				'redirectionType'   => ! empty( $this->data['general']['redirections_header_code'] ) ? $this->data['general']['redirections_header_code'] : '301',
				'hasAutoPostDirect' => ! empty( $this->data['general']['redirections_post_redirect'] ),
			],
		];
	}

	/**
	 * Get the advanced robots meta for the provided key.
	 *
	 * @param string $key the array key used to store the meta.
	 *
	 * @return ?array<string, mixed>
	 */
	private function advanced_robots_meta( string $key ): ?array {
		return ! empty( $this->data['titles'][ $key ] )
			? [
				'hasSnippet'       => ! empty( $this->data['titles'][ $key ]['max-snippet'] ),
				'snippetLength'    => ! empty( $this->data['titles'][ $key ]['max-snippet'] ) ? $this->data['titles'][ $key ]['max-snippet'] : null,
				'hasVideoPreview'  => ! empty( $this->data['titles'][ $key ]['max-video-preview'] ),
				'videoDuration'    => ! empty( $this->data['titles'][ $key ]['max-video-preview'] ) ? $this->data['titles'][ $key ]['max-video-preview'] : null,
				'hasImagePreview'  => ! empty( $this->data['titles'][ $key ]['max-image-preview'] ),
				'imagePreviewSize' => ! empty( $this->data['titles'][ $key ]['max-image-preview'] ) ? $this->data['titles'][ $key ]['max-image-preview'] : null,
			]
			: null;
	}

	/**
	 * Resolve titles and meta Global fields.
	 *
	 * @return array<string, mixed>
	 */
	private function meta_global_fields(): array {
		return [
			'advancedRobotsMeta'         => $this->advanced_robots_meta( 'advanced_robots_global' ),
			'robotsMeta'                 => ! empty( $this->data['titles']['robots_global'] ) ? $this->data['titles']['robots_global'] : null,
			'openGraphImageId'           => ! empty( $this->data['titles']['open_graph_image_id'] ) ? $this->data['titles']['open_graph_image_id'] : null,
			'separator'                  => ! empty( $this->data['titles']['title_separator'] ) ? $this->data['titles']['title_separator'] : null,
			'twitterCardType'            => ! empty( $this->data['titles']['twitter_card_type'] ) ? $this->data['titles']['twitter_card_type'] : null,
			'shouldCapitalizeTitles'     => ! empty( $this->data['titles']['capitalize_titles'] ),
			'shouldIndexEmptyTaxonomies' => empty( $this->data['titles']['noindex_empty_taxonomies'] ),
			'shouldRewriteTitle'         => ! empty( $this->data['titles']['rewrite_title'] ),
		];
	}

	/**
	 * Resolve titles and meta social fields.
	 *
	 * @return array<string, mixed>
	 */
	private function meta_social_fields(): array {
		return [
			'facebookPageUrl'   => ! empty( $this->data['titles']['social_url_facebook'] ) ? $this->data['titles']['social_url_facebook'] : null,
			'facebookAuthorUrl' => ! empty( $this->data['titles']['facebook_author_urls'] ) ? $this->data['titles']['facebook_author_urls'] : null,
			'facebookAdminId'   => ! empty( $this->data['titles']['facebook_admin_id'] ) ? $this->data['titles']['facebook_admin_id'] : null,
			'facebookAppId'     => ! empty( $this->data['titles']['facebook_app_id'] ) ? $this->data['titles']['facebook_app_id'] : null,
			'twitterAuthorName' => ! empty( $this->data['titles']['twitter_author_names'] ) ? $this->data['titles']['twitter_author_names'] : null,
		];
	}

	/**
	 * Resolve titles and meta local fields.
	 *
	 * @return array<string, mixed>
	 */
	private function meta_local_fields(): array {
		return [
			'type'   => ! empty( $this->data['titles']['knowledgegraph_type'] ) ? $this->data['titles']['knowledgegraph_type'] : null,
			'name'   => ! empty( $this->data['titles']['knowledgegraph_name'] ) ? $this->data['titles']['knowledgegraph_name'] : null,
			'url'    => ! empty( $this->data['titles']['url'] ) ? $this->data['titles']['url'] : null,
			'logoId' => ! empty( $this->data['titles']['knowledgegraph_logo_id'] ) ? $this->data['titles']['knowledgegraph_logo_id'] : null,
		];
	}

	/**
	 * Resolve the titles and meta homepage fields.
	 *
	 * @return ?array<string, mixed>
	 */
	private function meta_homepage_fields(): ?array {
		return 'page' !== get_option( 'show_on_front' ) ? [
			'advancedRobotsMeta'  => $this->advanced_robots_meta( 'homepage_advanced_robots' ),
			'description'         => ! empty( $this->data['titles']['homepage_description'] ) ? $this->data['titles']['author_archive_description'] : null,
			'hasCustomRobotsMeta' => ! empty( $this->data['titles']['homepage_custom_robots'] ),
			'robotsMeta'          => ! empty( $this->data['titles']['homepage_robots'] ) ? $this->data['titles']['homepage_robots'] : null,
			'socialDescription'   => ! empty( $this->data['titles']['homepage_facebook_description'] ) ? $this->data['titles']['homepage_facebook_description'] : null,
			'socialImageId'       => ! empty( $this->data['titles']['homepage_facebook_image_id'] ) ? $this->data['titles']['homepage_facebook_image_id'] : null,
			'socialTitle'         => ! empty( $this->data['titles']['homepage_facebook_title'] ) ? $this->data['titles']['homepage_facebook_title'] : null,
			'title'               => ! empty( $this->data['titles']['homepage_title'] ) ? $this->data['titles']['homepage_title'] : null,
		] : null;
	}

	/**
	 * Resolve the titles and meta date archive fields.
	 *
	 * @return array<string, mixed>
	 */
	private function meta_date_archive_fields(): array {
		$has_archives = empty( $this->data['titles']['disable_date_archives'] );

		return [
			'hasArchives'        => $has_archives,
			'advancedRobotsMeta' => $has_archives ? $this->advanced_robots_meta( 'date_advanced_robots' ) : null,
			'robotsMeta'         => $has_archives && ! empty( $this->data['titles']['date_archive_robots'] ) ? $this->data['titles']['date_archive_robots'] : null,
			'archiveDescription' => $has_archives && ! empty( $this->data['titles']['date_archive_description'] ) ? $this->data['titles']['date_archive_description'] : null,
			'archiveTitle'       => $has_archives && ! empty( $this->data['titles']['date_archive_title'] ) ? $this->data['titles']['date_archive_title'] : null,
		];
	}

	/**
	 * Resolve the titles and meta author archive fields.
	 *
	 * @return array<string, mixed>
	 */
	private function meta_author_archive_fields(): array {
		$has_archives = empty( $this->data['titles']['disable_author_archives'] );
		return [
			'advancedRobotsMeta'      => $has_archives ? $this->advanced_robots_meta( 'author_advanced_robots' ) : null,
			'archiveDescription'      => $has_archives && ! empty( $this->data['titles']['author_archive_description'] ) ? $this->data['titles']['author_archive_description'] : null,
			'archiveTitle'            => $has_archives && ! empty( $this->data['titles']['author_archive_title'] ) ? $this->data['titles']['author_archive_title'] : null,
			'baseSlug'                => $has_archives && ! empty( $this->data['titles']['url_author_base'] ) ? $this->data['titles']['url_author_base'] : null,
			'robotsMeta'              => $has_archives && ! empty( $this->data['titles']['author_robots'] ) ? $this->data['titles']['author_robots'] : null,
			'hasArchives'             => $has_archives,
			'hasCustomRobotsMeta'     => $has_archives && ! empty( $this->data['titles']['author_custom_robots'] ),
			'hasSeoControls'          => $has_archives && ! empty( $this->data['titles']['author_add_meta_box'] ),
			'hasSlackEnhancedSharing' => $has_archives && ! empty( $this->data['titles']['author_slack_enhanced_sharing'] ),
		];
	}

	/**
	 * Resolve the titles and meta for taxonomy fields.
	 *
	 * @return ?array<string, array<string,mixed>>
	 */
	private function meta_taxonomy_fields(): ?array {
		/** @var string[] $taxonomies */
		$taxonomies = \WPGraphQL::get_allowed_taxonomies();

		$fields = [];

		foreach ( $taxonomies as $taxonomy ) {
			$prefix = 'tax_' . $taxonomy;

			$fields[ $taxonomy ] = [
				'archiveTitle'            => ! empty( $this->data['titles'][ $prefix . '_archive_title' ] ) ? $this->data['titles'][ $prefix . '_archive_title' ] : null,
				'archiveDescription'      => ! empty( $this->data['titles'][ $prefix . '_archive_description' ] ) ? $this->data['titles'][ $prefix . '_archive_description' ] : null,
				'hasCustomRobotsMeta'     => ! empty( $this->data['titles'][ $prefix . '_custom_robots' ] ) ? $this->data['titles'][ $prefix . '_custom_robots' ] : null,
				'robotsMeta'              => ! empty( $this->data['titles'][ $prefix . '_robots' ] ) ? $this->data['titles'][ $prefix . '_robots' ] : null,
				'advancedRobotsMeta'      => $this->advanced_robots_meta( $prefix . '_advanced_robots' ),
				'hasSlackEnhancedSharing' => ! empty( $this->data['titles'][ $prefix . '_slack_enhanced_sharing' ] ),
				'hasSeoControls'          => ! empty( $this->data['titles'][ $prefix . '_add_meta_box' ] ),
				'hasSnippetData'          => empty( $this->data['titles'][ 'remove_' . $taxonomy . '_snippet_data' ] ),
			];
		}

		return $fields ?: null;
	}

	/**
	 * Resolve the titles and meta for post type fields.
	 *
	 * @return ?array<string, array<string,mixed>>
	 */
	private function meta_content_type_fields(): ?array {
		/** @var string[] $post_types */
		$post_types = \WPGraphQL::get_allowed_post_types();

		$fields = [];

		foreach ( $post_types as $post_type ) {
			$prefix = 'pt_' . $post_type;

			$fields[ $post_type ] = [
				'title'                   => ! empty( $this->data['titles'][ $prefix . '_title' ] ) ? $this->data['titles'][ $prefix . '_title' ] : null,
				'description'             => ! empty( $this->data['titles'][ $prefix . '_description' ] ) ? $this->data['titles'][ $prefix . '_description' ] : null,
				'archiveTitle'            => ! empty( $this->data['titles'][ $prefix . '_archive_title' ] ) ? $this->data['titles'][ $prefix . '_archive_title' ] : null,
				'archiveDescription'      => ! empty( $this->data['titles'][ $prefix . '_archive_description' ] ) ? $this->data['titles'][ $prefix . '_archive_description' ] : null,
				'snippetType'             => ! empty( $this->data['titles'][ $prefix . '_default_rich_snippet' ] ) ? $this->data['titles'][ $prefix . '_default_rich_snippet' ] : null,
				'snippetHeadline'         => ! empty( $this->data['titles'][ $prefix . '_default_snippet_name' ] ) ? $this->data['titles'][ $prefix . '_default_snippet_name' ] : null,
				'snippetDescription'      => ! empty( $this->data['titles'][ $prefix . '_default_snippet_desc' ] ) ? $this->data['titles'][ $prefix . '_default_snippet_desc' ] : null,
				'articleType'             => ! empty( $this->data['titles'][ $prefix . '_default_article_type' ] ) ? $this->data['titles'][ $prefix . '_default_article_type' ] : null,
				'hasCustomRobotsMeta'     => ! empty( $this->data['titles'][ $prefix . '_custom_robots' ] ) ? $this->data['titles'][ $prefix . '_custom_robots' ] : null,
				'robotsMeta'              => ! empty( $this->data['titles'][ $prefix . '_robots' ] ) ? $this->data['titles'][ $prefix . '_robots' ] : null,
				'advancedRobotsMeta'      => $this->advanced_robots_meta( $prefix . '_advanced_robots' ),
				'hasLinkSuggestions'      => ! empty( $this->data['titles'][ $prefix . '_link_suggestions' ] ),
				'shouldUseFocusKeyword'   => ! empty( $this->data['titles'][ $prefix . '_ls_use_fk' ] ),
				'socialImage'             => ! empty( $this->data['titles'][ $prefix . '_facebook_image_id' ] ),
				'hasBulkEditing'          => ! empty( $this->data['titles'][ $prefix . '_bulk_editing' ] ) ? $this->data['titles'][ $prefix . '_bulk_editing' ] : null,
				'hasSlackEnhancedSharing' => ! empty( $this->data['titles'][ $prefix . '_slack_enhanced_sharing' ] ),
				'hasSeoControls'          => ! empty( $this->data['titles'][ $prefix . '_add_meta_box' ] ),
				'analyzedFields'          => ! empty( $this->data['titles'][ $prefix . '_analyze_fields' ] ) ? $this->data['titles'][ $prefix . '_analyze_fields' ] : null,
				'primaryTaxonomy'         => ! empty( $this->data['titles'][ $prefix . '_primary_taxonomy' ] ) ? $this->data['titles'][ $prefix . '_primary_taxonomy' ] : null,
			];
		}

		return $fields ?: null;
	}

	/**
	 * Resolve the sitemap general settings.
	 *
	 * @return array<string, mixed>
	 */
	private function sitemap_general_fields(): array {
		return [
			'canPingSearchEngines'    => ! empty( $this->data['sitemap']['ping_search_engines'] ),
			'excludedPostDatabaseIds' => ! empty( $this->data['sitemap']['exclude_posts'] ) ? array_map( 'absint', explode( ',', $this->data['sitemap']['exclude_posts'] ) ) : null,
			'excludedTermDatabaseIds' => ! empty( $this->data['sitemap']['exclude_terms'] ) ? array_map( 'absint', explode( ',', $this->data['sitemap']['exclude_terms'] ) ) : null,
			'hasFeaturedImage'        => ! empty( $this->data['sitemap']['include_featured_image'] ),
			'hasImages'               => ! empty( $this->data['sitemap']['include_images'] ),
			'linksPerSitemap'         => ! empty( $this->data['sitemap']['items_per_page'] ) ? absint( $this->data['sitemap']['items_per_page'] ) : null,
		];
	}

	/**
	 * Resolve the sitemap general settings.
	 *
	 * @return array<string, mixed>
	 */
	private function sitemap_author_fields(): ?array {
		if ( ! Helper::is_author_archive_indexable() ) {
			return null;
		}

		return [
			'excludedRoles'           => function () {
				if ( empty( $this->data['sitemap']['exclude_roles'] ) ) {
					return null;
				}

				$roles = array_keys( $this->data['sitemap']['exclude_roles'] );

				if ( ! is_string( $roles[0] ) ) {
					$roles = array_values( $this->data['sitemap']['exclude_roles'] );
				}

				return ! empty( $roles ) ? $roles : null;
			},
			'excludedUserDatabaseIds' => ! empty( $this->data['sitemap']['exclude_users'] ) ? array_map( 'absint', explode( ',', $this->data['sitemap']['exclude_users'] ) ) : null,
			'sitemapUrl'              => Router::get_base_url( 'author-sitemap.xml' ),
		];
	}

	/**
	 * Resolve the sitemap post type settings.
	 *
	 * @return ?array<string, mixed>
	 */
	private function sitemap_content_type_fields(): ?array {
		/** @var string[] $post_types */
		$post_types = \WPGraphQL::get_allowed_post_types();

		$fields = [];

		foreach ( $post_types as $post_type ) {
			$prefix = 'pt_' . $post_type;

			$fields[ $post_type ] = [
				'customImageMetaKeys' => ! empty( $this->data['sitemap'][ $prefix . '_image_customfields' ] ) ? preg_split( '/\r\n|\r|\n/', $this->data['sitemap'][ $prefix . '_image_customfields' ] ) : null,
				'isInSitemap'         => ! empty( $this->data['sitemap'][ $prefix . '_sitemap' ] ),
				'sitemapUrl'          => ! empty( $this->data['sitemap'][ $prefix . '_sitemap' ] ) ? Router::get_base_url( $post_type . '-sitemap.xml' ) : null,
				'type'                => $post_type,
			];
		}

		return $fields ?: null;
	}

	/**
	 * Resolve the sitemap taxonomy settings.
	 *
	 * @return array<string, array<string,mixed>>
	 */
	private function sitemap_taxonomy_fields(): ?array {
		/** @var string[] $taxonomies */
		$taxonomies = \WPGraphQL::get_allowed_taxonomies();

		$fields = [];

		foreach ( $taxonomies as $taxonomy ) {
			$prefix = 'tax_' . $taxonomy;

			$fields[ $taxonomy ] = [
				'hasEmptyTerms' => ! empty( $this->data['sitemap'][ $prefix . '_include_empty' ] ),
				'isInSitemap'   => ! empty( $this->data['sitemap'][ $prefix . '_sitemap' ] ),
				'sitemapUrl'    => ! empty( $this->data['sitemap'][ $prefix . '_sitemap' ] ) ? Router::get_base_url( $taxonomy . '-sitemap.xml' ) : null,
				'type'          => $taxonomy,

			];
		}

		return $fields ?: null;
	}
}
