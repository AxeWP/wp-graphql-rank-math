<?php
/**
 * The SEO model for ContentType objects.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use GraphQL\Error\Error;
use GraphQL\Error\UserError;
use WPGraphQL;

/**
 * Class - ContentTypeSeo
 */
class ContentTypeSeo extends Seo {
	/**
	 * Stores the incoming post type data.
	 *
	 * @var \WP_Post_Type $data
	 */
	protected $data;

	/**
	 * The settings prefix.
	 *
	 * @var string
	 */
	protected string $prefix;

	/**
	 * Constructor.
	 *
	 * @param string $post_type .
	 * @throws \GraphQL\Error\Error .
	 */
	public function __construct( string $post_type ) {
		$object = get_post_type_object( $post_type );
		if ( null === $object ) {
			throw new Error(
				sprintf(
					// translators: post type .
					__( 'Invalid post type %s passed to ContentTypeSeo model.', 'wp-graphql-rank-math' ),
					$post_type,
				)
			);
		}

		$capability = isset( $object->cap->edit_posts ) ? $object->cap->edit_posts : 'edit_posts';

		$allowed_fields = [ 'breadcrumbTitle' ];

		global $wp_query;

		$wp_query->parse_query( [ 'post_type' => $post_type ] );

		parent::__construct( $object, $capability, $allowed_fields );
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
					'breadcrumbTitle' => fn (): ?string => ! empty( $this->data->labels->singular_name ) ? $this->data->labels->singular_name : null,

				]
			);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_object_type(): string {
		$post_types = WPGraphQL::get_allowed_post_types( 'objects' );

		return $post_types[ $this->data->name ]->graphql_single_name;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \GraphQL\Error\UserError If no archive URI.
	 */
	protected function get_object_url(): string {
		$term_link = get_post_type_archive_link( $this->data->name );

		if ( false === $term_link ) {
			throw new UserError( __( 'There is no archive URI for the provided post type', 'wp-graphql-rank-math' ) );
		}

		return $term_link;
	}
}
