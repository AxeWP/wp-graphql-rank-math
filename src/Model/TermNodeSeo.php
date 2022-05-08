<?php
/**
 * The SEO model for TermNode objects.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use \GraphQL\Error\Error;
use GraphQL\Error\UserError;
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
	 * @throws Error .
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
	protected function init() {
		if ( empty( $this->fields ) ) {
			parent::init();

			$this->fields = array_merge(
				$this->fields,
				[
					'breadcrumbTitle' => fn() : ?string => $this->get_meta( 'breadcrumb_title', '', $this->data->name ) ?: null,

				]
			);
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws UserError If no valid term link.
	 */
	protected function get_rest_url() : string {
		$term_link = get_term_link( $this->database_id );

		if ( is_wp_error( $term_link ) ) {
			throw new UserError( $term_link->get_error_message() );
		}
		return get_rest_url( null, '/rankmath/v1/getHead' ) . '?url=' . $term_link;
	}
}
