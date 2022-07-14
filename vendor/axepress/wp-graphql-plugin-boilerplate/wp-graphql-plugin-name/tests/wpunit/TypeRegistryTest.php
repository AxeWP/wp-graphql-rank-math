<?php

use WPGraphQL\PluginName\TypeRegistry;

/**
 * Tests TypeRegistry.
 */
class TypeRegistryTest extends \Codeception\TestCase\WPTestCase {

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
	 * Tests TypeRegistry::get_registered_types()
	 *
	 * @uses WPGraphQL\RankMath\TypeRegistry::init()
	 */
	public function test_get_registered_types() {
		$registry = TypeRegistry::get_registered_types();

		$this->assertNotEmpty( $registry );
	}

}
