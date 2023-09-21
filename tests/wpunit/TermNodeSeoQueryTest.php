<?php

use WPGraphQL\RankMath\Type\Enum\OpenGraphLocaleEnum;
use WPGraphQL\RankMath\Type\Enum\TwitterCardTypeEnum;

/**
 * Tests TermNode seo queries.
 */
class TermNodeSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {

	public $admin;
	public $database_id;
	public $post_id;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		// Changing the permalink structure requires the taxonomies to be reregistered.
		$this->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
		global $wp_taxonomies;

		create_initial_taxonomies();


		$wp_taxonomies['category']->graphql_single_name = 'Category';
		$wp_taxonomies['category']->graphql_plural_name = 'Categories';
		$wp_taxonomies['category']->show_in_graphql     = true;

		// Set Rank Math Settings
		rank_math()->settings->set( 'general', 'breadcrumbs', true );
		rank_math()->settings->set( 'general', 'headless_support', true );

		// Create data
		$this->admin = $this->factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		$this->database_id = $this->factory()->term->create(
			[
				'taxonomy'    => 'category',
				'name'        => 'Test term',
				'description' => 'Test term description',
			]
		);

		$this->post_id = $this->factory()->post->create(
			[
				'post_title'  => 'Test post',
				'post_type'   => 'post',
				'post_status' => 'publish',
				'tax_input'   => [
					'category' => [
						$this->database_id,
					],
				],
			]
		);

		$this->clearSchema();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		rank_math()->settings->set( 'general', 'breadcrumbs', false );
		wp_delete_post( $this->post_id, true );
		wp_delete_term( $this->database_id, 'category', true );

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
								$this->expectedField( 'description', 'Test term description' ),
								$this->expectedField( 'focusKeywords', static::IS_NULL ),
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
