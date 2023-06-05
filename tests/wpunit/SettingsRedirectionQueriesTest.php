<?php

use RankMath\Helper;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionBehaviorEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionTypeEnum;
use WPGraphQL\RankMath\Modules\Redirection\TypeRegistry;
use WPGraphQL\RankMath\TypeRegistry as ParentTypeRegistry;

class SettingsRedirectionQueriesTest extends \Tests\WPGraphQL\TestCase\WPGraphQLTestCase {
	protected \WpunitTester $tester;
	public $admin;
	
	
	public function setUp(): void {
		// Before...
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
		ParentTypeRegistry::init();

		rank_math()->settings->set( 'general', 'redirect_debug', true );
		rank_math()->settings->set( 'general', 'redirections_fallback', 'custom' );
		rank_math()->settings->set( 'general', 'redirections_custom_url', 'https://example.com' );


		$this->clearSchema();

		parent::setUp();

		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.
		Helper::update_modules( [ 'redirections' => 'off' ] );
		$this->clearSchema();

		// Then...
		parent::tearDown();
	}

	// Tests
	public function testRedirectionSettings() {
		$query = '
			query RedirectionSettings {
				rankMathSettings {
					general {
						redirections {
							fallbackBehavior
							fallbackCustomUrl
							hasAutoPostRedirect
							hasDebug
							redirectionType
						}
					}
				}
			}
		';

		$actual = $this->graphql( compact( 'query' ) );

		$this->assertArrayNotHasKey( 'errors', $actual );

		$this->assertQuerySuccessful(
			$actual,
			[
				$this->expectedObject(
					'rankMathSettings',
					[
						$this->expectedObject(
							'general',
							[
								$this->expectedObject(
									'redirections',
									[
										$this->expectedField( 'fallbackBehavior', $this->tester->get_enum_for_value( RedirectionBehaviorEnum::get_type_name(), 'custom' ) ),
										$this->expectedField( 'fallbackCustomUrl', 'https://example.com' ),
										$this->expectedField( 'hasAutoPostRedirect', static::IS_FALSY ),
										$this->expectedField( 'hasDebug', true ),
										$this->expectedField( 'redirectionType', $this->tester->get_enum_for_value( RedirectionTypeEnum::get_type_name(), 301 ) ),
									]
								),
							]
						),
					]
				),
			]
		);
	}
}
