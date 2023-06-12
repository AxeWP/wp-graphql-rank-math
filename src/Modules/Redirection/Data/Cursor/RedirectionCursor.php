<?php
/**
 * The Redirection cursor.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection\Data\Cursor;
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Data\Cursor;

use WPGraphQL\RankMath\Utils\RMUtils;

/**
 * Class - RedirectionCursor
 */
class RedirectionCursor {
	/**
	 * Our current cursor offset.
	 * For example, the term, post, user, or comment ID.
	 *
	 * @var int
	 */
	public $cursor_offset;

	/**
	 * @var string|null
	 */
	public $cursor;

	/**
	 * The WP object instance for the cursor.
	 *
	 * @var mixed
	 */
	public $cursor_node;

	/**
	 * Copy of query_vars so we can modify them safely
	 *
	 * @var array<string,mixed>
	 */
	public $query_vars = [];

	/**
	 * Constructor
	 *
	 * @param array<string,mixed> $query_args The query args.
	 * @param string              $cursor The cursor.
	 */
	public function __construct( $query_args, $cursor = 'after' ) {
		$this->query_vars = $query_args;
		$this->cursor     = $cursor;

		// Get the cursor offset if any.
		$offset_key = 'graphql_' . $cursor . '_cursor';
		$offset     = $this->get_query_var( $offset_key );

		$this->cursor_offset = absint( $offset );

		$this->cursor_node = $this->get_cursor_node();
	}

	/**
	 * Get the WP Object instance for the cursor.
	 *
	 * This is cached internally so it should not generate additionl queries.
	 *
	 * @return ?array<string,mixed>
	 */
	public function get_cursor_node() {
		if ( ! $this->cursor_offset ) {
			return null;
		}

		// We don't want to reset the sql clauses.
		return RMUtils::get_redirection_from_db( $this->cursor_offset );
	}

	/**
	 * Get the direction pagination is going in.
	 *
	 * @return string
	 */
	public function get_cursor_compare() {
		return 'DESC' === $this->query_vars['order'] ? '<' : '>';
	}

	/**
	 * Ensure the cursor_offset is a positive integer and we have a valid object for our cursor node.
	 *
	 * @return bool
	 */
	protected function is_valid_offset_and_node() {
		if (
			! is_int( $this->cursor_offset ) ||
			0 >= $this->cursor_offset ||
			! $this->cursor_node
		) {
			return false;
		}

		return true;
	}

	/**
	 * Return the additional AND operators for the where statement
	 *
	 * @return array<string,mixed>
	 */
	public function get_where() {
		// If we have a bad cursor, just return an empty array.
		if ( ! $this->is_valid_offset_and_node() ) {
			return [];
		}

		$orderby          = $this->get_query_var( 'orderby' );
		$compare          = $this->get_cursor_compare();
		$comparison_value = $this->cursor_node[ $orderby ];

		if ( 'id' === $orderby ) {
			$comparison_value = (int) $comparison_value;
		}

		return [
			'column'   => $orderby,
			'operator' => $compare,
			'value'    => $comparison_value,
		];
	}

	/**
	 * Get the query variable for the provided name.
	 *
	 * @param string $name .
	 *
	 * @return mixed|null
	 */
	public function get_query_var( string $name ) {
		return ! empty( $this->query_vars[ $name ] ) ? $this->query_vars[ $name ] : null;
	}
}
