<?php

use WPGraphQL\PluginName\Main;

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
	 * Tests instance.
	 */
	public function testInstance() {
		$this->instance = new Main();
		$this->assertTrue( $this->instance instanceof Main );

		$actual = graphql_pb_init();
		$this->assertEquals( $this->instance, $actual );
	}

	/**
	 * Tests instance before instantiation.
	 */
	public function testInstanceBeforeInstantiation() {
		$instance = Main::instance();
		$this->assertTrue( $instance instanceof Main );
	}

	/**
	 * @covers \WPGraphQL\PluginName\Main::__wakeup
	 * @covers \WPGraphQL\PluginName\Main::__clone
	 */
	public function testClone() {
		$actual = Main::instance();
		$rc     = new ReflectionClass( $actual );
		$this->assertTrue( $rc->hasMethod( '__clone' ) );
		$this->assertTrue( $rc->hasMethod( '__wakeup' ) );
	}

	public function testConstants() {
		do_action( 'init' );
		$this->assertTrue( defined( 'WPGRAPHQL_PB_VERSION' ) );
		$this->assertTrue( defined( 'WPGRAPHQL_PB_PLUGIN_DIR' ) );
		$this->assertTrue( defined( 'WPGRAPHQL_PB_PLUGIN_URL' ) );
		$this->assertTrue( defined( 'WPGRAPHQL_PB_PLUGIN_FILE' ) );
	}

}
