<?php

use WPGraphQL\RankMath\Utils\Utils;

/**
 * Tests Utils.
 */
class UtilsTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * {@inheritDoc}
	 */
	public function setUp(): void {
		parent::setUp();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown(): void {
		parent::tearDown();
	}

	/**
	 * Tests Utils::truncate()
	 */
	public function testTruncate() {
		$string = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis nat';
		$actual = Utils::truncate( $string, 110, '...' );
		codecept_debug( $actual );

		$this->assertStringEndsWith( '...', $actual );
		$this->assertStringContainsString( substr( $actual, 0, -3 ), $string );
	}

	/**
	 * Tests Utils::is_url_relative()
	 */
	public function testIsUrlRelative() {
		$expected = Utils::is_url_relative( 'https://www.test.test/test' );
		$this->assertFalse( $expected );

		$expected = Utils::is_url_relative( '/home' );
		$this->assertTrue( $expected );
	}

	/**
	 * Tests Utils::base_url()
	 */
	public function testBaseUrl() {
		$uri    = '/about/';
		$actual = Utils::base_url( $uri );

		$this->assertStringStartsWith( 'http', $actual );
		$this->assertStringEndsWith( $uri, $actual );
	}

}
