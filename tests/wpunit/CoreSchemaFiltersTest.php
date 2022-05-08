<?php

use WPGraphQL\RankMath\CoreSchemaFilters;

/**
 * Tests CoreSchemaFilters.
 */
class CoreSchemaFiltersTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * Tests graphql_seo_get_setting
	 */
	public function testGetTypePrefix() {
		$actual = CoreSchemaFilters::get_type_prefix();
		$this->assertEquals( 'RankMath', $actual );

		$expected = 'somePrefix';
		$actual   = CoreSchemaFilters::get_type_prefix( $expected );
		$this->assertEquals( $expected, $actual );
	}

}
