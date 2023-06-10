<?php
/**
 * Adds filters that modify core schema.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection
 */

namespace WPGraphQL\RankMath\Modules\Redirection;

use WPGraphQL\AppContext;
use WPGraphQL\RankMath\Modules\Redirection\Data\Cursor\RedirectionCursor;
use WPGraphQL\RankMath\Modules\Redirection\Data\Loader\RedirectionsLoader;
use WPGraphQL\RankMath\Utils\RMUtils;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class - CoreSchemaFilters
 */
class CoreSchemaFilters implements Registrable {
	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		// Bail if the module is not active.
		if ( ! RMUtils::is_module_active( TypeRegistry::MODULE_NAME ) ) {
			return;
		}

		add_filter( 'graphql_data_loaders', [ self::class, 'register_loaders' ], 10, 2 );

		add_filter( 'rank_math_clauses_order_by', [ self::class, 'dedupe_pagination_clauses' ] );
		add_filter( 'rank_math_clauses_where', [ self::class, 'dedupe_pagination_clauses' ] );
		add_action( 'rank_math/redirection/get_redirections_query', [ self::class, 'add_redirection_pagination_support' ], 10, 2 );
	}

	/**
	 * Registers loaders to AppContext.
	 *
	 * @param array<string, \WPGraphQL\Data\Loader\AbstractDataLoader> $loaders Data loaders.
	 * @param \WPGraphQL\AppContext                                    $context App context.
	 *
	 * @return array<string, \WPGraphQL\Data\Loader\AbstractDataLoader>
	 */
	public static function register_loaders( array $loaders, AppContext $context ): array {
		$loaders[ RedirectionsLoader::$name ] = new RedirectionsLoader( $context );

		return $loaders;
	}

	/**
	 * Adds pagination support to the redirections query.
	 *
	 * @param \MyThemeShop\Database\Query_Builder $table The redirections table.
	 * @param array<string,mixed>                 $args  The query args passed to the query.
	 */
	public static function add_redirection_pagination_support( &$table, array $args ): void {
		// Return early if its not a GraphQL request.
		if ( true !== is_graphql_request() ) {
			return;
		}

		$where = [];

		// Get a copy of the table, not the reference.
		$current_table = clone $table;

		// Apply the after cursor to the query.
		if ( ! empty( $args['graphql_after_cursor'] ) ) {
			$after_cursor = new RedirectionCursor( $args, 'after' );
			$where        = $after_cursor->get_where();

			// Modify the table to include the where.
			if ( ! empty( $where ) ) {
				$current_table->where( $where['column'], $where['operator'], $where['value'], 'AND' );
			}
		}

		// Apply the before cursor to the query.
		if ( ! empty( $args['graphql_before_cursor'] ) ) {
			$before_cursor = new RedirectionCursor( $args, 'before' );
			$where         = $before_cursor->get_where();

			// Modify the table to include the where.
			if ( ! empty( $where ) ) {
				$current_table->where( $where['column'], $where['operator'], $where['value'], 'AND' );
			}
		}

		// // Add cursor stabilization
		$orderby_dir = isset( $args['graphql_cursor_compare'] ) && '>' === $args['graphql_cursor_compare'] ? 'ASC' : 'DESC';
		$current_table->orderBy( 'id', $orderby_dir );

		// Set the table back to the reference.
		$table = $current_table;
	}

	/**
	 * Deduplicates clauses on the Rank Math table instance.
	 *
	 * These are caused when paginating, since the $table is a static instance.
	 *
	 * @param string[] $clauses The clauses to dedupe.
	 *
	 * @return string[]
	 */
	public static function dedupe_pagination_clauses( array $clauses ): array {
		// Return early if its not a GraphQL request.
		if ( true !== is_graphql_request() ) {
			return $clauses;
		}

		// Dedupe the clauses.
		return array_unique( $clauses );
	}
}
