<?php

use RankMath\Helper;
use RankMath\Redirections\DB;
use WPGraphQL\RankMath\Modules\Redirection\CoreSchemaFilters;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionComparisonTypeEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionStatusEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionTypeEnum;
use WPGraphQL\RankMath\Modules\Redirection\TypeRegistry;
use WPGraphQL\RankMath\TypeRegistry as ParentTypeRegistry;

class RedirectionConnectionQueriesTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	protected \WpunitTester $tester;
	public $admin;
	public $database_ids;

	public function setUp(): void {
		// Before...
		parent::setUp();

		$this->admin = $this->factory()->user->create(
			[
				'role' => 'administrator',
			]
		);

		Helper::update_modules( [ 'redirections' => 'on' ] );
		
		// Unset the default type registry
		$registry = new ReflectionProperty( ParentTypeRegistry::class, 'registry' );
		$registry->setAccessible( true );
		$registry->setValue( null, [] );

		TypeRegistry::init();
		CoreSchemaFilters::init();
		ParentTypeRegistry::init();

		rank_math()->settings->set( 'general', 'redirect_debug', true );
		rank_math()->settings->set( 'general', 'redirections_fallback', 'custom' );
		rank_math()->settings->set( 'general', 'redirections_custom_url', 'https://example.com' );

		$this->database_ids = $this->create_redirections( [], 6 );

		$this->clearSchema();


		// Your set up methods here.
	}

	public function tearDown(): void {
		DB::delete( $this->database_ids );

		// Your tear down methods here.
		Helper::update_modules( [ 'redirections' => 'off' ] );
		$this->clearSchema();

		// Then...
		parent::tearDown();
	}

	protected function create_redirection_object( $args ) {
		$defaults = [
			'url_to'      => 'https://example.com',
			'sources'     => [
				[
					'ignore'     => 'case',
					'comparison' => 'contains',
				],
			],
			'header_code' => 301,
			'hits'        => wp_rand( 0, 100 ),
		];

		$args = array_merge( $defaults, $args );

		return DB::add( $args );
	}

	protected function create_redirections( array $args = [], int $count = 6 ) {
		$redirections = [];

		for ( $i = 0; $i < $count; $i++ ) {
			$args['url_to']                = 'https://example.com/' . $i;
			$args['sources'][0]['pattern'] = 'post-' . $i;
			$args['created']               = date( 'Y-m-d H:i:s', strtotime( "+{$i} days" ) );
			$args['updated']               = date( 'Y-m-d H:i:s', strtotime( "+{$i} days" ) );
			$args['last_accessed']         = date( 'Y-m-d H:i:s', strtotime( "+{$i} days" ) );
			$redirections[]                = $this->create_redirection_object( $args );
		}

		return $redirections;
	}


	public function query() : string {
		return '
			query redirectionQuery($first:Int $last:Int $after:String $before:String $where:RootQueryToRankMathRedirectionConnectionWhereArgs ) {
				redirections( first:$first last:$last after:$after before:$before where:$where ) {
					pageInfo {
						hasNextPage
						hasPreviousPage
						startCursor
						endCursor
					}
					edges {
						cursor
						node {
							id
							databaseId
							dateCreated
							hits
							redirectToUrl
						}
					}
					nodes {
						id
						databaseId
					}
				}
			}
		';
	}

	public function forwardPagination( $graphql_args = [], $query_args = [] ) {
		$query = $this->query();
		// Set the variables to use in the query.
		$query_args = array_merge(
			[
				'limit' => 6,
			],
			$query_args
		);

		$wp_query = DB::get_redirections( $query_args )['redirections'];
		
		// Set the variables to use in the GraphQL query.
		$variables = array_merge(
			[
				'first' => 2,
			],
			$graphql_args
		);


		// Run the GraphQL query.
		$expected = array_slice( $wp_query, 0, 2 );
		$page_1   = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertValidPagination( $expected, $page_1 );
		$this->assertEquals( false, $page_1['data']['redirections']['pageInfo']['hasPreviousPage'] );
		$this->assertEquals( true, $page_1['data']['redirections']['pageInfo']['hasNextPage'] );

		/**
		 * Test with empty offset.
		 */
		$variables['after'] = '';
		$expected           = $page_1;

		$page_1 = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertEqualSets( $expected, $page_1 );

		/**
		 * Test the next two results.
		 */

		// Set the variables to use in the GraphQL query.
		$variables['after'] = $page_1['data']['redirections']['pageInfo']['endCursor'];

		// Set the variables to use in the WP query.
		$query_args['offset'] = 2;

		// Run the GraphQL Query.
		$expected = array_slice( $wp_query, 2, 2 );

		$page_2 = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertValidPagination( $expected, $page_2 );
		$this->assertEquals( true, $page_2['data']['redirections']['pageInfo']['hasPreviousPage'] );
		$this->assertEquals( true, $page_2['data']['redirections']['pageInfo']['hasNextPage'] );

		/**
		 * Test the last two results.
		 */

		// Set the variables to use in the GraphQL query.
		$variables['after'] = $page_2['data']['redirections']['pageInfo']['endCursor'];

		// Set the variables to use in the WP query.
		$query_args['offset'] = 4;

		// Run the GraphQL Query
		$expected = array_slice( $wp_query, 4, 2 );
		$page_3   = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertValidPagination( $expected, $page_3 );
		$this->assertEquals( true, $page_3['data']['redirections']['pageInfo']['hasPreviousPage'] );
		$this->assertEquals( false, $page_3['data']['redirections']['pageInfo']['hasNextPage'] );

		/**
		 * Test the last two results are equal to `last:2`.
		 */
		$variables = array_merge(
			[
				'last' => 2,
			],
			$graphql_args
		);
		unset( $variables['first'] );

		$last_page = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertEqualSets( $page_3, $last_page );
	}

	public function backwardPagination( $graphql_args = [], $query_args = [] ) {
		// Set the variables to use in the query.
		$query_args = array_merge(
			[
				'limit' => 6,
				'order' => 'ASC',
			],
			$query_args
		);

		$wp_query = DB::get_redirections( $query_args )['redirections'];

		$query = $this->query();
		
		// Set the variables to use in the GraphQL query.
		$variables = array_merge(
			[
				'last' => 2,
			],
			$graphql_args
		);


		// Run the GraphQL query.
		$expected = array_slice( $wp_query, 0, 2 );
		$expected = array_reverse( $expected );
		$page_1   = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertValidPagination( $expected, $page_1 );
		$this->assertEquals( true, $page_1['data']['redirections']['pageInfo']['hasPreviousPage'] );
		$this->assertEquals( false, $page_1['data']['redirections']['pageInfo']['hasNextPage'] );

		/**
		 * Test with empty offset.
		 */
		$variables['before'] = '';
		$expected            = $page_1;

		$page_1 = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertEqualSets( $expected, $page_1 );

		/**
		 * Test the next two results.
		 */

		// Set the variables to use in the GraphQL query.
		$variables['before'] = $page_1['data']['redirections']['pageInfo']['startCursor'];

		// Run the GraphQL Query.
		$expected = array_slice( $wp_query, 2, 2 );
		$expected = array_reverse( $expected );

		$page_2 = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertValidPagination( $expected, $page_2 );
		$this->assertEquals( true, $page_2['data']['redirections']['pageInfo']['hasPreviousPage'] );
		$this->assertEquals( true, $page_2['data']['redirections']['pageInfo']['hasNextPage'] );

		/**
		 * Test the last two results.
		 */

		// Set the variables to use in the GraphQL query.
		$variables['before'] = $page_2['data']['redirections']['pageInfo']['startCursor'];

		// Run the GraphQL Query
		$expected = array_slice( $wp_query, 4, 2 );
		$expected = array_reverse( $expected );
		$page_3   = $this->graphql( compact( 'query', 'variables' ) );
		

		$this->assertValidPagination( $expected, $page_3 );
		$this->assertEquals( false, $page_3['data']['redirections']['pageInfo']['hasPreviousPage'] );
		$this->assertEquals( true, $page_3['data']['redirections']['pageInfo']['hasNextPage'] );

		/**
		 * Test the first two results are equal to `first:2`.
		 */
		$variables = array_merge(
			[
				'first' => 2,
			],
			$graphql_args
		);
		unset( $variables['last'] );

		$last_page = $this->graphql( compact( 'query', 'variables' ) );
		$this->assertEqualSets( $page_3, $last_page );
	}

	public function testForwardPaginationOrderedByDefault() {
		$this->forwardPagination();
	}

	public function testBackwardPaginationOrderedByDefault() {
		$this->backwardPagination();
	}

	public function testForwardPaginationOrderedByDateCreated() {
		$this->forwardPagination(
			[
				'where' => [
					'orderby' => [
						'field' => 'DATE_CREATED',
						'order' => 'ASC',
					],
				],
			],
			[
				'order_by' => 'created',
				'order'    => 'ASC',
			]
		);
	}

	public function testBackwardPaginationOrderedByDateCreated() {
		$this->backwardPagination(
			[
				'orderby' => [
					'field' => 'DATE_CREATED',
					'order' => 'ASC',
				],
			],
			[
				'order_by' => 'created',
				'order'    => 'ASC',
			]
		);
	}

	public function testForwardPaginationOrderedByRedirectUrl() {
		$this->forwardPagination(
			[
				'where' => [
					'orderby' => [
						'field' => 'REDIRECT_TO_URL',
					],
				],
			],
			[
				'order_by' => 'url_to',
			]
		);
	}

	public function testBackwardPaginationOrderedByRedirectUrl() {
		$this->backwardPagination(
			[
				'orderby' => [
					'field' => 'REDIRECT_TO_URL',
				],
			],
			[
				'order_by' => 'url_to',
			]
		);
	}

	/**
	 * Common asserts for testing pagination.
	 *
	 * @param array $expected An array of the results from WordPress. When testing backwards pagination, the order of this array should be reversed.
	 * @param array $actual The GraphQL results.
	 */
	public function assertValidPagination( $expected, $actual ) {
		$this->assertResponseIsValid( $actual );
		$this->assertArrayNotHasKey( 'errors', $actual );

		$this->assertEquals( 2, count( $actual['data']['redirections']['edges'] ) );

		$first_id  = $expected[0]['id'];
		$second_id = $expected[1]['id'];

		$start_cursor = $this->toRelayId( 'arrayconnection', $first_id );
		$end_cursor   = $this->toRelayId( 'arrayconnection', $second_id );

		$this->assertEquals( $first_id, $actual['data']['redirections']['edges'][0]['node']['databaseId'] );
		$this->assertEquals( $first_id, $actual['data']['redirections']['nodes'][0]['databaseId'] );
		$this->assertEquals( $start_cursor, $actual['data']['redirections']['edges'][0]['cursor'] );
		$this->assertEquals( $second_id, $actual['data']['redirections']['edges'][1]['node']['databaseId'] );
		$this->assertEquals( $second_id, $actual['data']['redirections']['nodes'][1]['databaseId'] );
		$this->assertEquals( $end_cursor, $actual['data']['redirections']['edges'][1]['cursor'] );
		$this->assertEquals( $start_cursor, $actual['data']['redirections']['pageInfo']['startCursor'] );
		$this->assertEquals( $end_cursor, $actual['data']['redirections']['pageInfo']['endCursor'] );
	}
}
