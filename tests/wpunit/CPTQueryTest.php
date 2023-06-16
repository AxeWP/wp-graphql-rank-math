<?php


/**
 * Tests Custom Post Types and Taxonomy Term seo queries.
 */
class CPTQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	public $admin;
	public $term_id;
	public $post_id;

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		// Changing the permalink structure requires the taxonomies to be reregistered.
		$this->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
		register_post_type(
			'test_custom_tax_cpt',
			[
				'show_in_graphql'     => true,
				'graphql_single_name' => 'customTaxCpt',
				'graphql_plural_name' => 'customTaxCpts',
				'hierarchical'        => true,
				'taxonomies'          => [ 'test_custom_tax' ],
			]
		);
		register_taxonomy(
			'test_custom_tax',
			[ 'test_custom_tax_cpt' ],
			[
				'show_in_graphql'     => true,
				'graphql_single_name' => 'customTaxTerm',
				'graphql_plural_name' => 'customTaxTerms',
				'hierarchical'        => true,
			]
		);

		// Set Rank Math Settings
		rank_math()->settings->set( 'general', 'breadcrumbs', true );
		rank_math()->settings->set( 'general', 'headless_support', true );
		rank_math()->settings->set( 'titles', 'tax_test_custom_tax_description', '%term_description%' );
		rank_math()->settings->set( 'titles', 'tax_test_custom_tax_title', ' %term% %sep% %sitename%' );

		// Create data
		$this->admin = $this->factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		$this->term_id = $this->factory()->term->create(
			[
				'taxonomy'    => 'test_custom_tax',
				'name'        => 'Test term',
				'description' => 'Test term description',
			]
		);

		$this->post_id = $this->factory()->post->create(
			[
				'post_title'  => 'Test post',
				'post_type'   => 'test_custom_tax_cpt',
				'post_status' => 'publish',
				'tax_input'   => [
					'test_custom_tax' => [
						$this->term_id,
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
		wp_delete_term( $this->term_id, 'test_custom_tax', true );

		parent::tearDown();
	}

	/**
	 * Tests seo as term node.
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
						openGraph {
							alternateLocales
							description
							locale
							siteName
							title
							type
							url
							
							
							
						}
					}
				}
			}
		';

		$variables = [ 'id' => $this->term_id ];

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
										$this->expectedField( 'url', get_term_link( $this->term_id ) ),
										$this->expectedField( 'isHidden', false ),
									],
									2
								),
								$this->expectedField( 'breadcrumbTitle', 'Test term' ),
								$this->expectedField( 'description', 'Test term description' ),
								$this->expectedField( 'focusKeywords', self::IS_NULL ),
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
