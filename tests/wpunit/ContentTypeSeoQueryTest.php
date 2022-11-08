<?php

use WPGraphQL\RankMath\Type\Enum\OpenGraphLocaleEnum;
use WPGraphQL\RankMath\Type\Enum\TwitterCardTypeEnum;

/**
 * Tests ContentType seo queries.
 */
class ContentTypeSeoQueryTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {

	public $admin;
	public $tester;

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

		WPGraphQL::clear_schema();
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
								$this->expectedField(
									'robots',
									[
										'index',
										'follow',
									]
								),
								$this->expectedField( 'title', 'Test -' ),
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
