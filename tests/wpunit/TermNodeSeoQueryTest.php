<?php

/**
 * Tests TermNode seo queries.
 */
class TermNodeSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {

	public $admin;
	public $database_id;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		rank_math()->variables = new \RankMath\Replace_Variables\Manager();
		rank_math()->settings->set( 'general', 'breadcrumbs', true );

		$this->admin = $this->factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		$this->database_id = $this->factory()->term->create_object(
			[
				'taxonomy' => 'category',
				'name'     => 'Test term',
			]
		);

		WPGraphQL::clear_schema();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		rank_math()->settings->set( 'general', 'breadcrumbs', false );

		parent::tearDown();
	}

	/**
	 * Tests rankMathSettings.general
	 */
	public function testTermNodeSeo() {
		wp_set_current_user( $this->admin );

		$query = '
			query TermNodeSeo( $id: ID! ) {
				termNode( id: $id, idType: DATABASE_ID ){ 
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

		$variables = [ 'id' => $this->database_id ];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'termNode',
					[
						$this->expectedObject(
							'seo',
							[
								$this->expectedNode(
									'breadcrumbs',
									[
										$this->expectedField( 'text', 'Test term' ),
										$this->expectedField( 'url', get_term_link( $this->database_id ) ),
										$this->expectedField( 'isHidden', false ),
									],
									1
								),
								$this->expectedField( 'breadcrumbTitle', 'Test term' ),
								$this->expectedField( 'description', static::IS_NULL ),
								$this->expectedField( 'focusKeywords', static::IS_NULL ),
								// $this->expectedField( 'fullHead', static::IS_NULL ),
								$this->expectedField(
									'robots',
									[
										'follow',
										'noindex',
									]
								),
								$this->expectedField( 'title', 'Test term - Test' ),
							]
						),
					]
				),
			]
		);

		// Test individual values:
		$this->assertStringContainsString( home_url(), $actual['data']['termNode']['seo']['canonicalUrl'] );

		$this->assertStringContainsString( '<script type="application/ld+json" class="rank-math-schema">', $actual['data']['termNode']['seo']['jsonLd']['raw'] );
	}
}
