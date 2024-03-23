<?php

/**
 * Tests User seo queries.
 */
class UserSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {

	public $admin;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		self::set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );

		rank_math()->settings->set( 'general', 'breadcrumbs', true );
		rank_math()->settings->set( 'general', 'headless_support', true );
		rank_math()->settings->set( 'titles', 'disable_author_archives', false );

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
		rank_math()->settings->set( 'titles', 'disable_author_archives', true );

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
						jsonLd {
							raw
						}
						robots
						title
						... on RankMathUserSeo {
							additionalProfiles
							facebookProfileUrl
							twitterUserName
						}
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
								$this->expectedField( 'description', static::IS_FALSY ),
								$this->expectedField( 'focusKeywords', static::IS_NULL ),
								$this->expectedField(
									'robots',
									[
										'follow',
										'noindex',
									]
								),
								$this->expectedField( 'title', 'display - Test' ),
								$this->expectedField( 'additionalProfiles', static::IS_NULL ),
								$this->expectedField( 'facebookProfileUrl', static::IS_NULL ),
								$this->expectedField( 'twitterUserName', static::IS_NULL ),
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
