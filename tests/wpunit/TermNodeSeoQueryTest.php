<?php

use WPGraphQL\RankMath\Type\Enum\OpenGraphLocaleEnum;
use WPGraphQL\RankMath\Type\Enum\TwitterCardTypeEnum;

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

		self::set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );

		rank_math()->settings->set( 'general', 'breadcrumbs', true );
		rank_math()->settings->set( 'general', 'headless_support', true );

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
		wp_delete_post( $this->database_id, true );

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
						fullHead
						jsonLd {
							raw
						}
						openGraph {
							locale
							siteName
							title
							type
							url
							twitterMeta {
								card
								title
							}
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
								$this->expectedField( 'fullHead', static::NOT_FALSY ),
								$this->expectedObject(
									'openGraph',
									[
										$this->expectedField( 'locale', $this->tester->get_enum_for_value( OpenGraphLocaleEnum::get_type_name(), 'en_US' ) ),
										$this->expectedField( 'siteName', 'Test' ),
										$this->expectedField( 'title', 'Test term - Test' ),
										$this->expectedField( 'type', 'article' ),
										$this->expectedField( 'url', get_term_link( $this->database_id ) ),
										$this->expectedObject(
											'twitterMeta',
											[
												$this->expectedField( 'card', $this->tester->get_enum_for_value( TwitterCardTypeEnum::get_type_name(), 'summary_large_image' ) ),
												$this->expectedField( 'title', 'Test term - Test' ),
											]
										),
									]
								),
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
