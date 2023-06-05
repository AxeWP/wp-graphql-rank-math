<?php

use RankMath\Helper;
use RankMath\Redirections\DB;
use WPGraphQL\RankMath\Modules\Redirection\CoreSchemaFilters;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionComparisonTypeEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionStatusEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionTypeEnum;
use WPGraphQL\RankMath\Modules\Redirection\TypeRegistry;
use WPGraphQL\RankMath\TypeRegistry as ParentTypeRegistry;

class RedirectionQueriesTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	protected \WpunitTester $tester;
	public $admin;

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

		$this->clearSchema();


		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.
		Helper::update_modules( [ 'redirections' => 'off' ] );
		$this->clearSchema();

		// Then...
		parent::tearDown();
	}

	public function query() : string {
		return '
			query Redirection( $id: ID! ) {
				redirection( id: $id ) {
					databaseId
					dateCreated
					dateCreatedGmt
					dateModified
					dateModifiedGmt
					dateLastAccessed
					dateLastAccessedGmt
					hits
					id
					redirectToUrl
					sources {
						comparison
						pattern
						ignore
					}
					status
					type
				}
			}
		';
	}

	// Tests
	public function testRedirection() {
		// Create redirection
		$database_id = DB::add(
			[
				'url_to'        => 'https://example.com',
				'sources'       => [
					[
						'ignore'     => 'case',
						'comparison' => 'contains',
						'pattern'    => 'test',
					],
				],
				'header_code'   => 301,
				'hits'          => wp_rand( 0, 100 ),
				'last_accessed' => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
			]
		);


		$query = $this->query();


		$variables = [
			'id' => $database_id,
		];

		// Test as logged out.

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$expected = DB::get_redirection_by_id( $database_id );

		codecept_debug( $expected );

		$this->assertArrayNotHasKey( 'errors', $actual );
		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'redirection',
					[
						$this->expectedField( 'databaseId', (int) $expected['id'] ),
						$this->expectedField( 'dateCreated', static::IS_NULL ),
						$this->expectedField( 'dateCreatedGmt', static::IS_NULL ),
						$this->expectedField( 'dateModified', static::IS_NULL ),
						$this->expectedField( 'dateModifiedGmt', static::IS_NULL ),
						$this->expectedField( 'dateLastAccessed', static::IS_NULL ),
						$this->expectedField( 'dateLastAccessedGmt', static::IS_NULL ),
						$this->expectedField( 'hits', static::IS_NULL ),
						$this->expectedField( 'redirectToUrl', $expected['url_to'] ),
						$this->expectedNode(
							'sources',
							[
								$this->expectedField( 'comparison', $this->tester->get_enum_for_value( RedirectionComparisonTypeEnum::get_type_name(), $expected['sources'][0]['comparison'] ) ),
								$this->expectedField( 'pattern', $expected['sources'][0]['pattern'] ),
								$this->expectedField( 'ignore', $expected['sources'][0]['ignore'] ),
							],
							0
						),
						$this->expectedField( 'status', $this->tester->get_enum_for_value( RedirectionStatusEnum::get_type_name(), $expected['status'] ) ),
						$this->expectedField( 'type', $this->tester->get_enum_for_value( RedirectionTypeEnum::get_type_name(), $expected['header_code'] ) ),
					]
				),
			]
		);

		// Test as logged in.
		wp_set_current_user( $this->admin );

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );
		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'redirection',
					[
						$this->expectedField( 'databaseId', (int) $expected['id'] ),
						$this->expectedField( 'dateCreated', $expected['created'] ),
						$this->expectedField( 'dateCreatedGmt', get_gmt_from_date( $expected['created'] ) ),
						$this->expectedField( 'dateModified', $expected['updated'] ),
						$this->expectedField( 'dateModifiedGmt', get_gmt_from_date( $expected['updated'] ) ),
						$this->expectedField( 'dateLastAccessed', $expected['last_accessed'] ),
						$this->expectedField( 'dateLastAccessedGmt', get_gmt_from_date( $expected['last_accessed'] ) ),
						$this->expectedField( 'hits', (int) $expected['hits'] ),
						$this->expectedField( 'redirectToUrl', $expected['url_to'] ),
						$this->expectedNode(
							'sources',
							[
								$this->expectedField( 'comparison', $this->tester->get_enum_for_value( RedirectionComparisonTypeEnum::get_type_name(), $expected['sources'][0]['comparison'] ) ),
								$this->expectedField( 'pattern', $expected['sources'][0]['pattern'] ),
								$this->expectedField( 'ignore', $expected['sources'][0]['ignore'] ),
							],
							0
						),
						$this->expectedField( 'status', $this->tester->get_enum_for_value( RedirectionStatusEnum::get_type_name(), $expected['status'] ) ),
						$this->expectedField( 'type', $this->tester->get_enum_for_value( RedirectionTypeEnum::get_type_name(), $expected['header_code'] ) ),
					]
				),
			]
		);

		// cleanup
		DB::delete( $database_id );
	}

	public function testWithStatus() {
		// Create redirection
		$database_id = DB::add(
			[
				'url_to'        => 'https://example.com',
				'sources'       => [
					[
						'ignore'     => '',
						'comparison' => 'exact',
						'pattern'    => 'test',
					],
				],
				'header_code'   => 302,
				'hits'          => wp_rand( 0, 100 ),
				'last_accessed' => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
				'status' => 'inactive'
			]
		);


		$query = $this->query();


		$variables = [
			'id' => $database_id,
		];

		// Test as logged out.

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );
		$this->assertNull( $actual['data']['redirection'] );

		// Test as logged in.
		wp_set_current_user( $this->admin );

		$actual = $this->graphql( compact( 'query', 'variables' ) );

		$expected = DB::get_redirection_by_id( $database_id );

		$this->assertArrayNotHasKey( 'errors', $actual );
		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'redirection',
					[
						$this->expectedField( 'databaseId', (int) $expected['id'] ),
						$this->expectedField( 'dateCreated', $expected['created'] ),
						$this->expectedField( 'dateCreatedGmt', get_gmt_from_date( $expected['created'] ) ),
						$this->expectedField( 'dateModified', $expected['updated'] ),
						$this->expectedField( 'dateModifiedGmt', get_gmt_from_date( $expected['updated'] ) ),
						$this->expectedField( 'dateLastAccessed', $expected['last_accessed'] ),
						$this->expectedField( 'dateLastAccessedGmt', get_gmt_from_date( $expected['last_accessed'] ) ),
						$this->expectedField( 'hits', (int) $expected['hits'] ),
						$this->expectedField( 'redirectToUrl', $expected['url_to'] ),
						$this->expectedNode(
							'sources',
							[
								$this->expectedField( 'comparison', $this->tester->get_enum_for_value( RedirectionComparisonTypeEnum::get_type_name(), $expected['sources'][0]['comparison'] ) ),
								$this->expectedField( 'pattern', $expected['sources'][0]['pattern'] ),
								$this->expectedField( 'ignore', $expected['sources'][0]['ignore'] ),
							],
							0
						),
						$this->expectedField( 'status', $this->tester->get_enum_for_value( RedirectionStatusEnum::get_type_name(), $expected['status'] ) ),
						$this->expectedField( 'type', $this->tester->get_enum_for_value( RedirectionTypeEnum::get_type_name(), $expected['header_code'] ) ),
					]
				),
			]
		);

		// cleanup
		DB::delete( $database_id );
	}
}
