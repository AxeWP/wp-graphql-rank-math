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

		self::set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );

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
						fullHead
						jsonLd {
							raw
						}
						openGraph {
							locale
							siteName
							type
							url
							twitterMeta {
								card
							}
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
								$this->expectedField( 'fullHead', static::NOT_FALSY ),
								$this->expectedField(
									'robots',
									[
										'index',
										'follow',
									]
								),
								$this->expectedField( 'title', static::IS_NULL ),
								$this->expectedObject(
									'openGraph',
									[
										$this->expectedField( 'locale', $this->tester->get_enum_for_value( OpenGraphLocaleEnum::get_type_name(), 'en_US' ) ),
										$this->expectedField( 'siteName', 'Test' ),
										$this->expectedField( 'type', 'website' ),
										$this->expectedField( 'url', trailingslashit( get_post_type_archive_link( 'post' ) ) ),
										
										$this->expectedObject(
											'twitterMeta',
											[
												$this->expectedField( 'card', $this->tester->get_enum_for_value( TwitterCardTypeEnum::get_type_name(), 'summary_large_image' ) ),
											]
										),
									]
								),
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
