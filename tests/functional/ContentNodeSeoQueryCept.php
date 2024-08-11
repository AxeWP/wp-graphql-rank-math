<?php

namespace Tests\WPGraphQL\RankMath;

$I = new FunctionalTester( $scenario );
$I->wantTo( 'Query content node seo data' );

// Create post category
$cat_id = $I->haveTermInDatabase(
	'Category',
	'category',
	[
		'description' => 'Category description',
		'name'        => 'Test Term',
	]
);

$post_id = $I->havePostInDatabase(
	[
		'post_title'    => 'Test Post',
		'post_type'     => 'post',
		'post_status'   => 'publish',
		'post_content'  => 'Post Content',
		'tax_input'   => [
			'category' => [
				$cat_id[0],
			],
		],
	]
);

// Enable Headless support and breadcrumbs in rank math general
$I->haveOptionInDatabase( 'rank_math_general', 'headless_support', true );
$I->haveOptionInDatabase( 'rank_math_general', 'breadcrumbs', true );

// Set the content-type so we get a proper response from the API.
$I->haveHttpHeader( 'Content-Type', 'application/json' );
$I->sendPOST(
	// Use site url.
	get_site_url( null, '/graphql' ),
	json_encode(
		[
			'query'     => '
				query contentNode( $id: ID!, $idType: ContentNodeIdTypeEnum ) {
					contentNode( id: $id, idType: $idType ){
						databaseId
						seo {
							fullHead
							openGraph {
								articleMeta {
									section
								}
								description
								locale
								siteName
								title
								type
								url
								slackEnhancedData {
									data
									label
								}
								twitterMeta {
									card
									description
									title
								}
							}
						}
					}
				}
			',
			'variables' => [
				'id'     => $post_id,
				'idType' => 'DATABASE_ID',
			],
		]
	)
);

// Check response.
$I->seeResponseCodeIs( 200 );
$I->seeResponseIsJson();
$response       = $I->grabResponse();
$response_array = json_decode( $response, true );


// The query is valid and has no errors.
$I->assertArrayNotHasKey( 'errors', $response_array );

// The response is properly returning data as expected.
$I->assertArrayHasKey( 'data', $response_array );
$I->assertEquals( $post_id, $response_array['data']['contentNode']['databaseId'] );
$I->assertNotEmpty( $response_array['data']['contentNode']['seo']['fullHead'] );
$I->assertArrayHasKey( 'section', $response_array['data']['contentNode']['seo']['openGraph']['articleMeta'] );
$I->assertEquals( get_the_excerpt( $post_id ), $response_array['data']['contentNode']['seo']['openGraph']['description'] );
$I->assertEquals( 'EN_US', $response_array['data']['contentNode']['seo']['openGraph']['locale'] );
$I->assertEquals( get_bloginfo( 'name' ), $response_array['data']['contentNode']['seo']['openGraph']['siteName'] );
$I->assertEquals( get_the_title( $post_id ) . ' - Test', $response_array['data']['contentNode']['seo']['openGraph']['title'] );
$I->assertEquals( 'article', $response_array['data']['contentNode']['seo']['openGraph']['type'] );
$I->assertEquals( get_permalink( $post_id ), $response_array['data']['contentNode']['seo']['openGraph']['url'] );
$I->assertEquals( 'Less than a minute', $response_array['data']['contentNode']['seo']['openGraph']['slackEnhancedData'][1]['data'] );
$I->assertEquals( 'Time to read', $response_array['data']['contentNode']['seo']['openGraph']['slackEnhancedData'][1]['label'] );
$I->assertEquals( 'SUMMARY_LARGE_IMAGE', $response_array['data']['contentNode']['seo']['openGraph']['twitterMeta']['card'] );
$I->assertEquals( get_the_excerpt( $post_id ), $response_array['data']['contentNode']['seo']['openGraph']['twitterMeta']['description'] );
$I->assertEquals( get_the_title( $post_id ) . ' - Test', $response_array['data']['contentNode']['seo']['openGraph']['twitterMeta']['title'] );




codecept_debug( $response_array );
