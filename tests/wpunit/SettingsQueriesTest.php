<?php

use RankMath\Helper;

/**
 * Tests Settings queries.
 */
class SettingsQueriesTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {

	public $admin;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		$this->admin = $this->factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		$page_id = $this->factory()->post->create(
			[
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Post Title',
				'post_content' => 'Post Content',
			]
		);

		update_option( 'show_on_front', 'page' );
		update_option( 'page_for_posts', $page_id );

		Helper::update_modules( [ 'sitemap' => 'on' ] );


		$this->clearSchema();
	}

	/**
	 * Tests rankMathSettings.general
	 */
	public function testGeneralSettings() {
		$query = '
			query GeneralSettings {
				rankMathSettings {
					general {
						breadcrumbs {
							archiveFormat
							hasBlogPage
							hasAncestorCategories
							hasHome
							hasPostTitle
							hasTaxonomyName
							homeLabel
							homeUrl
							notFoundLabel
							prefix
							searchFormat
							separator
						}
						frontendSeoScore {
							enabledPostTypes
							hasRankMathBacklink
							position
							template
						}
						hasBreadcrumbs
						hasFrontendSeoScore
						links {
							defaultAttachmentRedirectUrl
							hasCategoryBase
							nofollowDomains
							nofollowExcludedDomains
							shouldNofollowImageLinks
							shouldNofollowLinks
							shouldOpenInNewWindow
							shouldRedirectAttachments
						}
						rssAfterContent
						rssBeforeContent
						webmaster {
							baidu
							bing
							google
							norton
							pinterest
							yandex
						}
					}
				}
			}
		';

		$actual = $this->graphql( compact( 'query' ) );

		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'rankMathSettings',
					[
						$this->expectedObject(
							'general',
							[
								$this->expectedObject(
									'breadcrumbs',
									[
										$this->expectedField( 'archiveFormat', 'Archives for %s' ),
										$this->expectedField( 'hasBlogPage', false ),
										$this->expectedField( 'hasAncestorCategories', false ),
										$this->expectedField( 'hasHome', true ),
										$this->expectedField( 'hasPostTitle', true ),
										$this->expectedField( 'hasTaxonomyName', true ),
										$this->expectedField( 'homeLabel', 'Home' ),
										$this->expectedField( 'homeUrl', static::IS_NULL ),
										$this->expectedField( 'notFoundLabel', '404 Error: page not found' ),
										$this->expectedField( 'prefix', static::IS_NULL ),
										$this->expectedField( 'searchFormat', 'Results for %s' ),
										$this->expectedField( 'separator', '-' ),
									]
								),
								$this->expectedObject(
									'frontendSeoScore',
									[
										$this->expectedField( 'enabledPostTypes', [ 'POST' ] ),
										$this->expectedField( 'hasRankMathBacklink', false ),
										$this->expectedField( 'position', 'TOP' ),
										$this->expectedField( 'template', static::IS_NULL ),

									]
								),
								$this->expectedField( 'hasBreadcrumbs', false ),
								$this->expectedField( 'hasFrontendSeoScore', false ),
								$this->expectedObject(
									'links',
									[
										$this->expectedField( 'defaultAttachmentRedirectUrl', home_url() ),
										$this->expectedField( 'hasCategoryBase', true ),
										$this->expectedField( 'nofollowDomains', static::IS_NULL ),
										$this->expectedField( 'shouldNofollowImageLinks', false ),
										$this->expectedField( 'shouldNofollowLinks', false ),
										$this->expectedField( 'shouldOpenInNewWindow', true ),
										$this->expectedField( 'shouldRedirectAttachments', true ),
									]
								),
								$this->expectedField( 'rssAfterContent', static::IS_NULL ),
								$this->expectedField( 'rssBeforeContent', static::IS_NULL ),
								$this->expectedObject(
									'webmaster',
									[
										$this->expectedField( 'baidu', static::IS_NULL ),
										$this->expectedField( 'bing', static::IS_NULL ),
										$this->expectedField( 'google', static::IS_NULL ),
										$this->expectedField( 'norton', static::IS_NULL ),
										$this->expectedField( 'pinterest', static::IS_NULL ),
										$this->expectedField( 'yandex', static::IS_NULL ),
									]
								),
							]
						),
					]
				),
			]
		);
	}

	/**
	 * Tests rankMathSettings.meta
	 */
	public function testMetaSettings() {
		$query = '
		{
			rankMathSettings {
				meta {
					authorArchives {
						advancedRobotsMeta {
							hasImagePreview
							hasSnippet
							hasVideoPreview
							imagePreviewSize
							snippetLength
							videoDuration
						}
						archiveDescription
						archiveTitle
						baseSlug
						hasArchives
						hasCustomRobotsMeta
						hasSeoControls
						hasSlackEnhancedSharing
						robotsMeta
					}
					contentTypes {
						page {
							advancedRobotsMeta {
								hasImagePreview
								hasSnippet
								hasVideoPreview
								imagePreviewSize
								snippetLength
								videoDuration
							}
							analyzedFields
							articleType
							description
							hasBulkEditing
							hasCustomRobotsMeta
							hasLinkSuggestions
							hasSeoControls
							hasSlackEnhancedSharing
							robotsMeta
							shouldUseFocusKeyword
							snippetDescription
							snippetHeadline
							snippetType
							title
							socialImage {
								databaseId
							}
						}
					}
					dateArchives {
						advancedRobotsMeta {
							hasImagePreview
							hasSnippet
							hasVideoPreview
							imagePreviewSize
							snippetLength
							videoDuration
						}
						archiveDescription
						archiveTitle
						hasArchives
						robotsMeta
					}
					global {
						advancedRobotsMeta {
							hasImagePreview
							hasSnippet
							hasVideoPreview
							imagePreviewSize
							snippetLength
							videoDuration
						}
						openGraphImage {
							databaseId
						}
						robotsMeta
						separator
						shouldCapitalizeTitles
						shouldIndexEmptyTaxonomies
						twitterCardType
					}
					homepage {
						advancedRobotsMeta {
							hasImagePreview
							hasSnippet
							hasVideoPreview
							imagePreviewSize
							snippetLength
							videoDuration
						}
						description
						hasCustomRobotsMeta
						robotsMeta
						socialDescription
						socialTitle
						title
					}
					local {
						logo {
							databaseId
						}
						name
						type
						url
					}
					notFoundTitle
					searchTitle
					shouldIndexArchiveSubpages
					shouldIndexPaginatedPages
					shouldIndexPasswordProtected
					shouldIndexSearch
					social {
						facebookAdminId
						facebookAppId
						facebookAuthorUrl
						facebookPageUrl
						twitterAuthorName
					}
					taxonomies {
						category {
							advancedRobotsMeta {
								hasImagePreview
								hasSnippet
								hasVideoPreview
								imagePreviewSize
								snippetLength
								videoDuration
							}
							archiveDescription
							archiveTitle
							hasCustomRobotsMeta
							hasSeoControls
							hasSlackEnhancedSharing
							hasSnippetData
							robotsMeta
						}
					}
				}
			}
		}
		';

		$actual = $this->graphql( compact( 'query' ) );

		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'rankMathSettings',
					[
						$this->expectedObject(
							'meta',
							[
								$this->expectedObject(
									'authorArchives',
									[
										$this->expectedField( 'advancedRobotsMeta', static::IS_NULL ),
										$this->expectedField( 'archiveDescription', static::IS_NULL ),
										$this->expectedField( 'archiveTitle', '%name% %sep% %sitename% %page%' ),
										$this->expectedField( 'baseSlug', 'author' ),
										$this->expectedField( 'hasArchives', true ),
										$this->expectedField( 'hasCustomRobotsMeta', true ),
										$this->expectedField( 'hasSeoControls', true ),
										$this->expectedField( 'hasSlackEnhancedSharing', true ),
										$this->expectedField( 'robotsMeta', [ 'NOINDEX' ] ),
									]
								),
								$this->expectedObject(
									'contentTypes',
									[
										$this->expectedObject(
											'page',
											[
												$this->expectedField( 'advancedRobotsMeta', static::IS_NULL ),
												$this->expectedField( 'analyzedFields', static::IS_NULL ),
												$this->expectedField( 'articleType', 'ARTICLE' ),
												$this->expectedField( 'description', '%excerpt%' ),
												$this->expectedField( 'hasBulkEditing', 'ENABLED' ),
												$this->expectedField( 'hasCustomRobotsMeta', static::IS_NULL ),
												$this->expectedField( 'hasLinkSuggestions', true ),
												$this->expectedField( 'hasSeoControls', true ),
												$this->expectedField( 'hasSlackEnhancedSharing', true ),
												$this->expectedField( 'robotsMeta', static::IS_NULL ),
												$this->expectedField( 'shouldUseFocusKeyword', true ),
												$this->expectedField( 'snippetDescription', '%seo_description%' ),
												$this->expectedField( 'snippetHeadline', '%seo_title%' ),
												$this->expectedField( 'snippetType', 'ARTICLE' ),
												$this->expectedField( 'title', '%title% %sep% %sitename%' ),
												$this->expectedField( 'socialImage', static::IS_NULL ),
											]
										),
									]
								),
								$this->expectedObject(
									'dateArchives',
									[
										$this->expectedField( 'advancedRobotsMeta', static::IS_NULL ),
										$this->expectedField( 'archiveDescription', static::IS_NULL ),
										$this->expectedField( 'archiveTitle', static::IS_NULL ),
										$this->expectedField( 'hasArchives', false ),
										$this->expectedField( 'robotsMeta', static::IS_NULL ),
									]
								),
								$this->expectedObject(
									'global',
									[
										$this->expectedField( 'advancedRobotsMeta', static::IS_NULL ),
										$this->expectedField( 'openGraphImage', static::IS_NULL ),
										$this->expectedField( 'robotsNeta', static::IS_NULL ),
										$this->expectedField( 'separator', '-' ),
										$this->expectedField( 'shouldCapitalizeTitles', false ),
										$this->expectedField( 'shouldIndexEmptyTaxonomies', false ),
										$this->expectedField( 'twitterCardType', 'SUMMARY_LARGE_IMAGE' ),
									]
								),
								$this->expectedField( 'homepage', static::IS_NULL ),
								$this->expectedObject(
									'local',
									[
										$this->expectedField( 'logo', static::IS_NULL ),
										$this->expectedField( 'name', 'Test' ),
										$this->expectedField( 'type', 'PERSON' ),
										$this->expectedField( 'url', static::IS_NULL ),
									]
								),
								$this->expectedField( 'notFoundTitle', 'Page Not Found %sep% %sitename%' ),
								$this->expectedField( 'searchTitle', '%search_query% %page% %sep% %sitename%' ),
								$this->expectedField( 'shouldIndexArchiveSubpages', true ),
								$this->expectedField( 'shouldIndexPaginatedPages', true ),
								$this->expectedField( 'shouldIndexPasswordProtected', true ),
								$this->expectedField( 'shouldIndexSearch', false ),
								$this->expectedObject(
									'social',
									[
										$this->expectedField( 'facebookAdminId', static::IS_NULL ),
										$this->expectedField( 'facebookAppId', static::IS_NULL ),
										$this->expectedField( 'faceboookAuthorUrl', static::IS_NULL ),
										$this->expectedField( 'facebookPageUrl', static::IS_NULL ),
										$this->expectedField( 'twitterAuthorName', static::IS_NULL ),
									]
								),
								$this->expectedObject(
									'taxonomies',
									[
										$this->expectedObject(
											'category',
											[
												$this->expectedField( 'advancedRobotsMeta', static::IS_NULL ),
												$this->expectedField( 'archiveDescription', static::IS_NULL ),
												$this->expectedField( 'archiveTitle', static::IS_NULL ),
												$this->expectedField( 'hasCustomRobotsMeta', static::IS_NULL ),
												$this->expectedField( 'hasSeoControls', true ),
												$this->expectedField( 'hasSlackEnhancedSharing', true ),
												$this->expectedField( 'hasSnippetData', true ),
												$this->expectedField( 'robotsMeta', static::IS_NULL ),
											]
										),
									]
								),
							]
						),
					]
				),
			]
		);
	}

	/**
	 * Tests rankMathSettings.sitemap
	 */
	public function testSitemapSettings() {
		// Configure settings.
		$title_options = get_option( 'rank-math-options-titles', [] );

		$title_options['disable_author_archives'] = 'off';
		rank_math()->settings->set( 'titles', 'disable_author_archives', false );

		$title_options['author_custom_robots'] = 'off';
		rank_math()->settings->set( 'titles', 'author_custom_robots', false );

		update_option( 'rank-math-options-titles', $title_options );

		$query = '{
			rankMathSettings {
				sitemap {
					author {
						excludedRoles
						excludedUserDatabaseIds
						sitemapUrl
					}
					contentTypes {
						customImageMetaKeys
						isInSitemap
						sitemapUrl
						type
					}
					general {
						canPingSearchEngines
						excludedPostDatabaseIds
						excludedTermDatabaseIds
						hasFeaturedImage
						hasImages
						linksPerSitemap
					}
					indexUrl
					taxonomies {
						hasEmptyTerms
						isInSitemap
						sitemapUrl
						type
					}
				}
			}
		}';

		$actual = $this->graphql( compact( 'query' ) );

		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'rankMathSettings',
					[
						$this->expectedObject(
							'sitemap',
							[
								$this->expectedObject(
									'author',
									[
										$this->expectedField( 'excludedRoles', [ 'CONTRIBUTOR', 'SUBSCRIBER' ] ),
										$this->expectedField( 'excludedUserDatabaseIds', static::IS_NULL ),
										$this->expectedField( 'sitemapUrl', get_home_url() . '/author-sitemap.xml' ),
									]
								),
								$this->expectedNode(
									'contentTypes',
									[
										$this->expectedField( 'customImageMetaKeys', static::IS_NULL ),
										$this->expectedField( 'isInSitemap', true ),
										$this->expectedField( 'sitemapUrl', get_home_url() . '/post-sitemap.xml' ),
										$this->expectedField( 'type', 'POST' ),
									],
									0
								),
								$this->expectedObject(
									'general',
									[
										$this->expectedField( 'canPingSearchEngines', true ),
										$this->expectedField( 'excludedPostDatabaseIds', static::IS_NULL ),
										$this->expectedField( 'excludedTermDatabaseIds', static::IS_NULL ),
										$this->expectedField( 'hasFeaturedImage', false ),
										$this->expectedField( 'hasImages', true ),
										$this->expectedField( 'linksPerSitemap', 200 ),
									]
								),
								$this->expectedField( 'indexUrl', get_home_url() . '/sitemap_index.xml' ),
								$this->expectedNode(
									'taxonomies',
									[
										$this->expectedField( 'hasEmptyTerms', false ),
										$this->expectedField( 'isInSitemap', true ),
										$this->expectedField( 'sitemapUrl', get_home_url() . '/category-sitemap.xml' ),
										$this->expectedField( 'type', 'CATEGORY' ),
									],
									0
								),
							]
						),
					]
				),
			]
		);
	}

}
