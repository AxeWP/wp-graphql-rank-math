<?php
/**
 * Tests ContentNode seo queries.
 */
class ContentNodeSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	public $admin;
	public $database_id;
	public \WpunitTester $tester;

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

		$this->clearSchema();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		rank_math()->settings->set( 'general', 'breadcrumbs', false );

		wp_delete_post( $this->database_id, true );

		parent::tearDown();

		$this->clearSchema();
	}

	protected function get_query() : string {
		return '
			query contentNode( $id: ID!, $idType: ContentNodeIdTypeEnum, $asPreview: Boolean ) {
				contentNode( id: $id, idType: $idType, asPreview: $asPreview ){
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
						robots
						title
						jsonLd {
							raw
						}
						... on RankMathContentNodeSeo {
							isPillarContent
							seoScore {
								badgeHtml
								hasFrontendScore
								rating
								score
							}
						}
					}
				}
			}
		';
	}

	protected function assertValidSeo( array $actual ): void {
		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'contentNode',
					[
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
	}

	/**
	 * Tests seo on the contentNode.
	 */
	public function testContentNodeSeo() {
		$query = $this->get_query();

		$variables = [
			'id'        => $this->database_id,
			'idType'    => 'DATABASE_ID',
			'asPreview' => false,
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $actual );

		$this->assertEquals( $this->database_id, $actual['data']['contentNode']['databaseId'] );

		$this->assertValidSeo( $actual );

		// Test individual values:
		$this->assertStringContainsString( home_url(), $actual['data']['contentNode']['seo']['canonicalUrl'] );
		$this->assertStringContainsString( '<script type="application/ld+json" class="rank-math-schema">', $actual['data']['contentNode']['seo']['jsonLd']['raw'] );
	}

	/**
	 * Tests seo on the contentNode preview.
	 */
	public function testContentNodeSeoAsPreview() {
		// Test revision on existing post.
		wp_set_current_user( $this->admin );
		$revision_id = $this->factory()->post->create(
			[
				'post_type'   => 'revision',
				'post_status' => 'inherit',
				'post_title'  => 'Post Title',
				'post_author' => $this->admin,
				'post_parent' => $this->database_id,
			]
		);

		$query = $this->get_query();

		$variables = [
			'id'        => $revision_id,
			'idType'    => 'DATABASE_ID',
			'asPreview' => true,
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $actual );

		$this->assertEquals( $revision_id, $actual['data']['contentNode']['databaseId'] );

		$this->assertValidSeo( $actual );

		// Test revision on draft.
		$draft_id = $this->factory()->post->create(
			[
				'post_type'   => $this->with_post_type,
				'post_status' => 'draft',
				'post_title'  => 'Draft Title',
				'post_author' => $this->admin,
			]
		);

		$revision_id = $this->factory()->post->create(
			[
				'post_type'   => 'revision',
				'post_status' => 'inherit',
				'post_title'  => 'Draft Title',
				'post_author' => $this->admin,
				'post_parent' => $draft_id,
			]
		);

		$variables = [
			'id'        => $revision_id,
			'idType'    => 'DATABASE_ID',
			'asPreview' => true,
		];

		$actual = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertArrayNotHasKey( 'errors', $actual );

		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'contentNode',
					[
						$this->expectedObject(
							'seo',
							[
								$this->expectedNode(
									'breadcrumbs',
									[
										$this->expectedField( 'text', 'Draft Title' ),
										$this->expectedField( 'url', get_permalink( $draft_id ) ),
										$this->expectedField( 'isHidden', false ),
									],
									2
								),
								$this->expectedField( 'breadcrumbTitle', 'Draft Title' ),
								$this->expectedField( 'description', get_the_excerpt( $draft_id ) ),
								$this->expectedField( 'focusKeywords', static::IS_NULL ),
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
								$this->expectedField( 'title', 'Draft Title - Test' ),
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

		// cleanup.
		wp_delete_post( $draft_id, true );
		wp_delete_post( $revision_id, true );
	}
}
