<?php

$I = new FunctionalTester( $scenario );
$I->wantTo( 'Query content type seo data' );

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
			'query' => '
				query ContentTypeSeo {
					contentType( id: "post", idType: NAME ){
						name
						seo {
							fullHead
							openGraph {
								locale
								siteName
								type
								url
								twitterMeta {
									card
								}
							}
						}
					}
				}
			',
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
$I->assertEquals( 'post', $response_array['data']['contentType']['name'] );
$I->assertNotEmpty( $response_array['data']['contentType']['seo']['fullHead'] );
$I->assertEquals( 'EN_US', $response_array['data']['contentType']['seo']['openGraph']['locale'] );
$I->assertEquals( 'Test', $response_array['data']['contentType']['seo']['openGraph']['siteName'] );
$I->assertEquals( 'website', $response_array['data']['contentType']['seo']['openGraph']['type'] );
$I->assertEquals( trailingslashit( get_post_type_archive_link( 'post' ) ), $response_array['data']['contentType']['seo']['openGraph']['url'] );
