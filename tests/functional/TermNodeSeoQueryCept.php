<?php

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
				query TermNodeSeo( $id: ID! ) {
				termNode( id: $id, idType: DATABASE_ID ){
					databaseId
					seo {
						fullHead
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
					}
				}
			}
			',
			'variables' => [
				'id' => $cat_id[0],
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
$I->assertEquals( $cat_id[0], $response_array['data']['termNode']['databaseId'] );
$I->assertNotEmpty( $response_array['data']['termNode']['seo']['fullHead'] );
$I->assertEquals( 'EN_US', $response_array['data']['termNode']['seo']['openGraph']['locale'] );
$I->assertEquals( 'Test', $response_array['data']['termNode']['seo']['openGraph']['siteName'] );
$I->assertEquals( 'Category - Test', $response_array['data']['termNode']['seo']['openGraph']['title'] );
$I->assertEquals( 'article', $response_array['data']['termNode']['seo']['openGraph']['type'] );
$I->assertEquals( get_site_url( null, 'category/category/' ), $response_array['data']['termNode']['seo']['openGraph']['url'] );
$I->assertEquals( 'SUMMARY_LARGE_IMAGE', $response_array['data']['termNode']['seo']['openGraph']['twitterMeta']['card'] );
