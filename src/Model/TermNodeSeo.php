<?php
/**
 * The SEO model for TermNode objects.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use GraphQL\Error\Error;
use GraphQL\Error\UserError;
use WPGraphQL;
use WP_Term;

/**
 * Class - TermNodeSeo
 */
class TermNodeSeo extends Seo {
	/**
	 * Stores the incoming post data
	 *
	 * @var \WP_Term $data
	 */
	protected $data;

	/**
	 * The settings prefix
	 *
	 * @var string
	 */
	protected string $prefix;

	/**
	 * Constructor.
	 *
	 * @param int $term_id .
	 * @throws \GraphQL\Error\Error .
	 */
	public function __construct( int $term_id ) {
		/** @var ?\WP_Term $object */
		$object = get_term( $term_id );
		if ( null === $object ) {
			throw new Error(
				sprintf(
					// translators: post id .
					__( 'Invalid term id %s passed to TermNodeSeo model.', 'wp-graphql-rank-math' ),
					$term_id,
				)
			);
		}

		$this->database_id = $object->term_id;

		parent::__construct( $object );
	}

	/**
	 * {@inheritDoc}
	 */
	public function setup(): void {
		global $wp_query, $post;

		/**
		 * Store the global post before overriding
		 */
		$this->global_post = $post;

		if ( $this->data instanceof WP_Term ) {

			/**
			 * Reset global post
			 */
			$GLOBALS['post'] = get_post( 0 ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride

			/**
			 * Parse the query to tell WordPress
			 * how to setup global state
			 */
			if ( 'category' === $this->data->taxonomy ) {
				$wp_query->parse_query(
					[
						'category_name' => $this->data->slug,
					]
				);
			} elseif ( 'post_tag' === $this->data->taxonomy ) {
				$wp_query->parse_query(
					[
						'tag' => $this->data->slug,
					]
				);
			}

			$wp_query->queried_object_id = $this->data->term_id;
			$wp_query->queried_object    = get_term( $this->data->term_id, $this->data->taxonomy );
		}

		parent::setup();
	}

	/**
	 * Reset global state after the model fields
	 * have been generated
	 *
	 * @return void
	 */
	public function tear_down() {
		$GLOBALS['post'] = $this->global_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		wp_reset_postdata();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			parent::init();

			$this->fields = array_merge(
				$this->fields,
				[
					'breadcrumbTitle' => function (): ?string {
						$title = $this->get_meta( 'breadcrumb_title', '', $this->data->name );

						return ! empty( $title ) ? html_entity_decode( $title, ENT_QUOTES ) : null;
					},
				]
			);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_object_type(): string {
		$taxonomies = WPGraphQL::get_allowed_taxonomies( 'objects' );

		return $taxonomies[ $this->data->taxonomy ]->graphql_single_name;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \GraphQL\Error\UserError If no valid term link.
	 */
	protected function get_object_url(): string {
		$term_link = get_term_link( $this->database_id );

		if ( is_wp_error( $term_link ) ) {
			throw new UserError( $term_link->get_error_message() );
		}
		return $term_link;
	}
}
