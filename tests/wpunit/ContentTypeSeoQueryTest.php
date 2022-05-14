<?php

/**
 * Tests ContentType seo queries.
 */
class ContentTypeSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {

	public $admin;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		rank_math()->variables          = new \RankMath\Replace_Variables\Manager();

		$this->admin = $this->factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		WPGraphQL::clear_schema();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * Tests rankMathSettings.general
	 */
	public function testContentTypeSeo() {
		$query = '
			query ContentTypeSeo {
				contentType( id: "post", idType: NAME ){ 
					seo {
						breadcrumbTitle
						canonicalUrl
						description
						focusKeywords
						# fullHead
						jsonLd {
							raw
						}
						robots
						title
					}
				}
			}
		';

		$actual = $this->graphql( compact( 'query' ) );

		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'contentType',
					[
						$this->expectedObject(
							'seo',
							[
								$this->expectedField( 'breadcrumbTitle', 'Post' ),
								$this->expectedField( 'canonicalUrl', static::IS_NULL ),
								$this->expectedField( 'description', static::IS_NULL ),
								$this->expectedField( 'focusKeywords', static::IS_NULL ),
								// $this->expectedField( 'fullHead', static::IS_NULL ),
								$this->expectedField(
									'robots',
									[
										'index',
										'follow',
									]
								),
								$this->expectedField( 'title', static::IS_NULL ),
							]
						),
					]
				),
			]
		);

		// Test individual values:
		$this->assertStringContainsString( '<script type="application/ld+json" class="rank-math-schema">', $actual['data']['contentType']['seo']['jsonLd']['raw'] );
	}
}
