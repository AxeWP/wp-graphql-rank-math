<?php

use WPGraphQL\RankMath\Main;

/**
 * Tests Main.
 */
class MainTest extends \Codeception\TestCase\WPTestCase {

	public $instance;

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
		unset( $this->instance );

		parent::tearDown();
	}

	/**
	 * Tests graphql_seo_get_setting
	 */
	public function testInstanceBeforeInstantiation() {
		$instance = Main::instance();
		$this->assertTrue( $instance instanceof Main );
	}

	/**
	 * @covers \WPGraphQL\RankMath\Main::__wakeup
	 * @covers \WPGraphQL\RankMath\Main::__clone
	 */
	public function testClone() {
		$actual = Main::instance();
		$rc     = new ReflectionClass( $actual );
		$this->assertTrue( $rc->hasMethod( '__clone' ) );
		$this->assertTrue( $rc->hasMethod( '__wakeup' ) );
	}

	public function testConstants() {
		do_action( 'init' );
		$this->assertTrue( defined( 'WPGRAPHQL_SEO_VERSION' ) );
		$this->assertTrue( defined( 'WPGRAPHQL_SEO_PLUGIN_DIR' ) );
		$this->assertTrue( defined( 'WPGRAPHQL_SEO_PLUGIN_URL' ) );
		$this->assertTrue( defined( 'WPGRAPHQL_SEO_PLUGIN_FILE' ) );
	}

}
