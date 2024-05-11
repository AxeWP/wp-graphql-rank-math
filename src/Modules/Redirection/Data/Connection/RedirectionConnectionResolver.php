<?php
/**
 * The Rank Math redirection connection resolver
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Data\Connection;
 * @since 0.0.13
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Modules\Redirection\Data\Connection;

use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\RankMath\Modules\Redirection\Data\Loader\RedirectionsLoader;
use WPGraphQL\RankMath\Utils\RMUtils;

/**
 * Class RedirectionConnectionResolver
 *
 * @extends \WPGraphQL\Data\Connection\AbstractConnectionResolver<array<string,mixed>>
 */
class RedirectionConnectionResolver extends AbstractConnectionResolver {
	/**
	 * {@inheritDoc}
	 */
	protected function loader_name(): string {
		return RedirectionsLoader::$name;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function prepare_query_args( array $args ): array {
		/**
		 * Prepare for later use
		 */
		$last = ! empty( $args['last'] ) ? $args['last'] : null;

		$query_args = [];

		if ( ! empty( $args['where']['search'] ) ) {
			$query_args['search'] = $args['where']['search'];
		}

		$query_args['status'] = ! empty( $args['where']['status'] ) ? $args['where']['status'] : 'active';

		if ( ! empty( $args['where']['orderby']['field'] ) ) {
			$query_args['orderby'] = $args['where']['orderby']['field'];
		}

		$query_args['order'] = ! empty( $args['where']['orderby']['order'] ) ? $args['where']['orderby']['order'] : 'DESC';

		// If $last is set, we need to reverse the order.
		if ( ! empty( $last ) ) {
			$query_args['order'] = 'DESC' === $query_args['order'] ? 'ASC' : 'DESC';
		}

		/**
		 * Set limit the highest value of $first and $last, with a (filterable) max of 100
		 */
		$query_args['limit'] = $this->one_to_one ? 1 : $this->get_query_amount() + 1;

		/**
		 * Set the before and after cursors. This will modify the query in CoreSchemaFilters::add_redirection_pagination_support()
		 */
		$query_args['graphql_cursor_compare'] = ! empty( $last ) ? '>' : '<';

		if ( ! empty( $args['after'] ) ) {
			$query_args['graphql_after_cursor'] = $this->get_after_offset();
		}

		if ( ! empty( $args['before'] ) ) {
			$query_args['graphql_before_cursor'] = $this->get_before_offset();
		}

		return $query_args;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function query( array $query_args ) {
		$query = RMUtils::get_redirections( $query_args );

		// Prime the cache for each of the queried redirections.
		$loader = $this->get_loader();
		if ( isset( $query['redirections'] ) ) {
			foreach ( $query['redirections'] as $redirection ) {
				$loader->prime( $redirection['id'], $redirection );
			}
		}

		return $query;
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_execute() {
		$query_args = $this->get_query_args();

		if ( isset( $query_args['status'] ) && 'active' === $query_args['status'] ) {
			return true;
		}

		return RMUtils::has_cap( 'redirections' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_valid_offset( $offset ) {
		return ! empty( RMUtils::get_redirection_by_id( $offset ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_ids_from_query() {
		$ids     = [];
		$query   = $this->get_query();
		$queried = ! empty( $query['redirections'] ) ? $query['redirections'] : [];

		if ( empty( $queried ) ) {
			return $ids;
		}

		$ids = array_column( $queried, 'id' );

		// If we're going backwards, we need to reverse the array.
		$args = $this->get_args();
		if ( ! empty( $args['last'] ) ) {
			$ids = array_reverse( $ids );
		}

		return $ids;
	}
}
