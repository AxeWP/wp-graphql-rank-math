<?php


/**
 * Tests ContentType seo queries.
 */
class ContentTypeSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	public $admin;
	public $tester;
	public $database_ids = [];

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();

		// Changing the permalink structure requires the taxonomies to be reregistered.
		$this->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
		create_initial_post_types();

		global $wp_post_types;
		$wp_post_types['post']->graphql_single_name       = 'Post';
		$wp_post_types['post']->graphql_plural_name       = 'Posts';
		$wp_post_types['post']->show_in_graphql           = true;
		$wp_post_types['attachment']->graphql_single_name = 'MediaItem';
		$wp_post_types['attachment']->graphql_plural_name = 'MediaItems';
		$wp_post_types['attachment']->show_in_graphql     = true;


		rank_math()->settings->set( 'general', 'breadcrumbs', true );
		rank_math()->settings->set( 'general', 'headless_support', true );

		$this->admin = $this->factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		// Set site subtitle to Just another WordPress site. Since WP 6.1 removes it
		update_option( 'blogdescription', 'Just another WordPress site' );

		for( $i = 0; $i < 5; $i++ ) {
			$this->database_ids[] = $this->factory()->post->create(
				[
					'post_type' => 'post',
					'post_date' => date( 'Y-m-d H:i:s', strtotime( "-{$i} days" ) ),
				]
			);
		}

		WPGraphQL::clear_schema();
	}

		/**
		 * {@inheritDoc}
		 */
	public function tearDown(): void {
		rank_math()->settings->set( 'general', 'breadcrumbs', false );

		foreach( $this->database_ids as $id ) {
			wp_delete_post( $id, true );
		}

		parent::tearDown();

		$this->clearSchema();
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
						jsonLd {
							raw
						}
						robots
						title
					}
					contentNodes {
						nodes {
							... on Post {
								title
								date
							}
							seo {
								breadcrumbTitle
								title
								openGraph {
									articleMeta {
										publishedTime
									}
								}
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
					'contentType',
					[
						$this->expectedObject(
							'seo',
							[
								$this->expectedField( 'breadcrumbTitle', 'Post' ),
								$this->expectedField( 'canonicalUrl', self::IS_NULL ),
								$this->expectedField( 'description', 'Just another WordPress site' ),
								$this->expectedField( 'focusKeywords', self::IS_NULL ),
								$this->expectedField(
									'robots',
									[
										'index',
										'follow',
									]
								),
								$this->expectedField( 'title', 'Test - Just another WordPress site' ),
							]
						),
					]
				),
			]
		);

		// Test the content nodes keep their state.
		$actual_content_nodes = $actual['data']['contentType']['contentNodes']['nodes'];
		$this->assertNotEmpty( $actual_content_nodes );
		foreach( $actual_content_nodes as $node ) {
			$breadcrumb = $node['seo']['breadcrumbTitle'] ?? null;
			$date = $node['seo']['openGraph']['articleMeta']['publishedTime'] ?? null;
			$this->assertNotEmpty( $breadcrumb );
			$this->assertNotEmpty( $date );
			$this->assertStringStartsWith( $breadcrumb, $node['title'] );
			$this->assertStringStartsWith( $node['date'], $date );
		}

		// Test individual values:
		$this->assertStringContainsString( '<script type="application/ld+json" class="rank-math-schema">', $actual['data']['contentType']['seo']['jsonLd']['raw'] );
	}
}
