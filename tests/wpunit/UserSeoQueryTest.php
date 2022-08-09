<?php

/**
 * Tests User seo queries.
 */
class UserSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {

	public $admin;
	public $database_id;

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
				'role'         => 'administrator',
				'display_name' => 'display',
				'first_name'   => 'first_name',
				'last_name'    => 'last_name',
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
	public function testUserSeo() {
		wp_set_current_user( $this->admin );

		$query = '
			query UserSeo( $id: ID! ) {
				user( id: $id, idType: DATABASE_ID ){ 
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
						# fullHead @todo
						jsonLd {
							raw
						}
						robots
						title
					}
				}
			}
		';

		$variables = [ 'id' => $this->admin ];

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'user',
					[
						$this->expectedObject(
							'seo',
							[
								$this->expectedNode(
									'breadcrumbs',
									[
										$this->expectedField( 'text', 'Archives for display' ),
										$this->expectedField( 'url', '' ),
										$this->expectedField( 'isHidden', false ),
									],
									1
								),
								$this->expectedField( 'breadcrumbTitle', 'display' ),
								$this->expectedField( 'description', static::IS_NULL ),
								$this->expectedField( 'focusKeywords', static::IS_NULL ),
								// $this->expectedField( 'fullHead', static::NOT_FALSY ),
								$this->expectedField(
									'robots',
									[
										'follow',
										'noindex',
									]
								),
								$this->expectedField( 'title', '- Test' ),
							]
						),
					]
				),
			]
		);

		// Test individual values:
		$this->assertStringContainsString( home_url(), $actual['data']['user']['seo']['canonicalUrl'] );

		$this->assertStringContainsString( '<script type="application/ld+json" class="rank-math-schema">', $actual['data']['user']['seo']['jsonLd']['raw'] );
	}
}
