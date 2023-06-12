<?php
/**
 * The Rank Math redirection connection resolver
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Data\Connection;
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Data\Connection;

use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQL\RankMath\Modules\Redirection\Data\Loader\RedirectionsLoader;
use WPGraphQL\RankMath\Utils\RMUtils;

/**
 * Class RedirectionConnectionResolver
 */
class RedirectionConnectionResolver extends AbstractConnectionResolver {
	/**
	 * {@inheritDoc}
	 *
	 * @var ?array
	 */
	protected $query;

	/**
	 * {@inheritDoc}
	 */
	public function get_loader_name() {
		return RedirectionsLoader::$name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_query_args() {
		/**
		 * Prepare for later use
		 */
		$last  = ! empty( $this->args['last'] ) ? $this->args['last'] : null;
		$first = ! empty( $this->args['first'] ) ? $this->args['first'] : null;

		$query_args = [];

		if ( ! empty( $this->args['where']['search'] ) ) {
			$query_args['search'] = $this->args['where']['search'];
		}

		$query_args['status'] = ! empty( $this->args['where']['status'] ) ? $this->args['where']['status'] : 'active';

		if ( ! empty( $this->args['where']['orderby']['field'] ) ) {
			$query_args['orderby'] = $this->args['where']['orderby']['field'];
		}

		$query_args['order'] = ! empty( $this->args['where']['orderby']['order'] ) ? $this->args['where']['orderby']['order'] : 'DESC';

		// If $last is set, we need to reverse the order.
		if ( ! empty( $last ) ) {
			$query_args['order'] = 'DESC' === $query_args['order'] ? 'ASC' : 'DESC';
		}

		/**
		 * Set limit the highest value of $first and $last, with a (filterable) max of 100
		 */
		$query_args['limit'] = $this->one_to_one ? 1 : min( max( absint( $first ), absint( $last ), 10 ), $this->query_amount ) + 1;

		/**
		 * Set the before and after cursors. This will modify the query in CoreSchemaFilters::add_redirection_pagination_support()
		 */
		$query_args['graphql_cursor_compare'] = ! empty( $last ) ? '>' : '<';

		if ( ! empty( $this->args['after'] ) ) {
			$query_args['graphql_after_cursor'] = $this->get_after_offset();
		}
		
		if ( ! empty( $this->args['before'] ) ) {
			$query_args['graphql_before_cursor'] = $this->get_before_offset();
		}

		return $query_args;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_query() {
		if ( ! isset( $this->query ) ) {
			$query = RMUtils::get_redirections( $this->query_args );

			// Prime the cache for each of the queried redirections.
			$loader = $this->getLoader();
			foreach ( $query['redirections'] as $redirection ) {
				$loader->prime( $redirection['id'], $redirection );
			}

			$this->query = $query;
		}

		return $this->query;
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_execute() {
		if ( 'active' === $this->query_args['status'] ) {
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
		$queried = $this->query['redirections'] ?? [];

		if ( empty( $queried ) ) {
			return $ids;
		}

		$ids = array_column( $queried, 'id' );

		// If we're going backwards, we need to reverse the array.
		if ( ! empty( $this->args['last'] ) ) {
			$ids = array_reverse( $ids );
		}

		return $ids;
	}
}
