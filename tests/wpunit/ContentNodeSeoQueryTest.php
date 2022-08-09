<?php

use WPGraphQL\RankMath\Type\Enum\OpenGraphLocaleEnum;
use WPGraphQL\RankMath\Type\Enum\TwitterCardTypeEnum;

/**
 * Tests ContentNode seo queries.
 */
class ContentNodeSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	public $admin;
	public $database_id;
	public $tester;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		self::set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );

		rank_math()->settings->set( 'general', 'breadcrumbs', true );
		rank_math()->settings->set( 'general', 'headless_support', true );
		

		$this->admin = $this->factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		$this->database_id = $this->factory()->post->create(
			[
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => 'Post Title',
				'post_content' => 'Post Content',
			]
		);

		WPGraphQL::clear_schema();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		rank_math()->settings->set( 'general', 'breadcrumbs', false );

		wp_delete_post( $this->database_id, true );

		parent::tearDown();
	}

	/**
	 * Tests rankMathSettings.general
	 */
	public function testContentNodeSeo() {
		$query = '
			query contentNode( $id: ID!, $idType: ContentNodeIdTypeEnum ) {
				contentNode( id: $id, idType: $idType ){ 
					databaseId
					seo {
						breadcrumbs {
							text
							url
							isHidden
						}
						breadcrumbTitle
						canonicalUrl
						description
						focusKeywords
						fullHead
						isPillarContent
						robots
						title
						jsonLd {
							raw
						}
						openGraph {
							articleMeta {
								section
							}
							description
							locale
							siteName
							title
							type
							url
							slackEnhancedData {
								data
								label
							}
							twitterMeta {
								card
								description
								title
							}
						}
						seoScore {
							badgeHtml
							hasFrontendScore
							rating
							score
						}
					}
				}
			}
		';

		$variables = [
			'id'     => $this->database_id,
			'idType' => 'DATABASE_ID',
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $actual );


		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'contentNode',
					[
						$this->expectedField( 'databaseId', $this->database_id ),
						$this->expectedObject(
							'seo',
							[
								$this->expectedNode(
									'breadcrumbs',
									[
										$this->expectedField( 'text', 'Post Title' ),
										$this->expectedField( 'url', get_permalink( $this->database_id ) ),
										$this->expectedField( 'isHidden', false ),
									],
									2
								),
								$this->expectedField( 'breadcrumbTitle', 'Post Title' ),
								$this->expectedField( 'description', get_the_excerpt( $this->database_id ) ),
								$this->expectedField( 'focusKeywords', static::IS_NULL ),
								$this->expectedField( 'fullHead', static::NOT_FALSY ),
								$this->expectedField( 'isPillarContent', false ),
								$this->expectedField(
									'robots',
									[
										'index',
										'follow',
										'max-snippet:-1',
										'max-video-preview:-1',
										'max-image-preview:large',
									]
								),
								$this->expectedField( 'title', 'Post Title - Test' ),
								$this->expectedObject(
									'openGraph',
									[
										$this->expectedObject(
											'articleMeta',
											[
												$this->expectedField( 'section', 'Uncategorized' ),
											]
										),
										$this->expectedField( 'description', get_the_excerpt( $this->database_id ) ),
										$this->expectedField( 'locale', $this->tester->get_enum_for_value( OpenGraphLocaleEnum::get_type_name(), 'en_US' ) ),
										$this->expectedField( 'siteName', 'Test' ),
										$this->expectedField( 'title', 'Post Title - Test' ),
										$this->expectedField( 'type', 'article' ),
										$this->expectedField( 'url', get_permalink( $this->database_id ) ),
										$this->expectedNode(
											'slackEnhancedData',
											[
												$this->expectedField( 'data', 'Less than a minute' ),
												$this->expectedField( 'label', 'Time to read' ),
											],
											0
										),
										$this->expectedObject(
											'twitterMeta',
											[
												$this->expectedField( 'card', $this->tester->get_enum_for_value( TwitterCardTypeEnum::get_type_name(), 'summary_large_image' ) ),
												$this->expectedField( 'description', get_the_excerpt( $this->database_id ) ),
												$this->expectedField( 'title', 'Post Title - Test' ),
											]
										),
									]
								),
								$this->expectedObject(
									'seoScore',
									[
										$this->expectedField( 'badgeHtml', static::IS_NULL ),
										$this->expectedField( 'hasFrontendScore', false ),
										$this->expectedField( 'rating', 'UNKNOWN' ),
										$this->expectedField( 'score', 0 ),
									]
								),
							]
						),
					]
				),
			]
		);

		// Test individual values:
		$this->assertStringContainsString( home_url(), $actual['data']['contentNode']['seo']['canonicalUrl'] );
		$this->assertStringContainsString( '<script type="application/ld+json" class="rank-math-schema">', $actual['data']['contentNode']['seo']['jsonLd']['raw'] );
	}
}
