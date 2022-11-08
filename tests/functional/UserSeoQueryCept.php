<?php

$I = new FunctionalTester( $scenario );
$I->wantTo( 'Query content node seo data' );

// Create a user.
$user_id = $I->haveUserInDatabase( 'testuser', 'editor' );

$post_id = $I->havePostInDatabase(
	[
		'post_title'   => 'Test Post',
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'post_content' => 'Post Content',
		'post_author'  => $user_id,
	]
);

// Enable Headless support and breadcrumbs in rank math general
$I->haveOptionInDatabase( 'rank_math_general', 'headless_support', true );
$I->haveOptionInDatabase( 'rank_math_general', 'breadcrumbs', true );

// Set the content-type so we get a proper response from the API.
$I->haveHttpHeader( 'Content-Type', 'application/json' );
// Login as administrator
$I->loginAsAdmin();

$I->sendPOST(
	// Use site url.
	get_site_url( null, '/graphql' ),
	json_encode(
		[
			'query'     => '
				query UserSeo( $id: ID! ) {
					user( id: $id, idType: DATABASE_ID ){ 
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
				'id'     => $user_id,
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
$I->assertEquals( $user_id, $response_array['data']['user']['databaseId'] );
$I->assertNotEmpty( $response_array['data']['user']['seo']['fullHead'] );
$I->assertEmpty( $response_array['data']['user']['seo']['openGraph']['articleMeta'] );
$I->assertEmpty( $response_array['data']['user']['seo']['openGraph']['description'] );
$I->assertEquals( 'EN_US', $response_array['data']['user']['seo']['openGraph']['locale'] );
$I->assertEquals( get_bloginfo( 'name' ), $response_array['data']['user']['seo']['openGraph']['siteName'] );
$I->assertEquals( 'testuser - Test', $response_array['data']['user']['seo']['openGraph']['title'] );
$I->assertEquals( 'profile', $response_array['data']['user']['seo']['openGraph']['type'] );
$I->assertEquals( get_author_posts_url( $user_id ), $response_array['data']['user']['seo']['openGraph']['url'] );
$I->assertEquals( 'testuser', $response_array['data']['user']['seo']['openGraph']['slackEnhancedData'][0]['data'] );
$I->assertEquals( 'Name', $response_array['data']['user']['seo']['openGraph']['slackEnhancedData'][0]['label'] );
$I->assertEquals( '1', $response_array['data']['user']['seo']['openGraph']['slackEnhancedData'][1]['data'] );
$I->assertEquals( 'Posts', $response_array['data']['user']['seo']['openGraph']['slackEnhancedData'][1]['label'] );
$I->assertEquals( 'SUMMARY_LARGE_IMAGE', $response_array['data']['user']['seo']['openGraph']['twitterMeta']['card'] );
$I->assertEmpty( $response_array['data']['user']['seo']['openGraph']['twitterMeta']['description'] );
$I->assertEquals( 'testuser - Test', $response_array['data']['user']['seo']['openGraph']['twitterMeta']['title'] );




codecept_debug( $response_array );
