<?php
/**
 * The SEO model for ContentType objects.
 *
 * @package \WPGraphQL\RankMath\Model
 */

declare( strict_types = 1 );

namespace WPGraphQL\RankMath\Model;

use GraphQL\Error\Error;
use GraphQL\Error\UserError;
use RankMath\Helper as RMHelper;
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
					esc_html__( 'Invalid post type %s passed to ContentTypeSeo model.', 'wp-graphql-rank-math' ),
					esc_html( $post_type ),
				)
			);
		}

		$capability = isset( $object->cap->edit_posts ) ? $object->cap->edit_posts : 'edit_posts';

		$allowed_fields = [ 'breadcrumbTitle' ];

		parent::__construct( $object, $capability, $allowed_fields );
	}

	/**
	 * {@inheritDoc}
	 */
	public function setup(): void {
		global $wp_query, $post;

		// Store the global post before overriding.
		$this->global_post = $post;

		if ( $this->data instanceof \WP_Post_Type ) {
			/**
			 * Reset global post
			 */
			$GLOBALS['post'] = get_post( 0 ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}

		// Store the global post before overriding.
		$this->global_post = $post;

		/**
		 * Parse the query to tell WordPress how to setup the global state.
		 */
		$wp_query->parse_query( [ 'post_type' => $this->data->name ] );

		$wp_query->queried_object_id = $this->data->name;
		$wp_query->queried_object    = $this->data;

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
					'breadcrumbTitle' => fn (): ?string => ! empty( $this->data->labels->singular_name ) ? $this->data->labels->singular_name : null,

				]
			);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_breadcrumbs(): ?array {
		$breadcrumbs = parent::get_breadcrumbs();

		// For non posts, we return the breadcrumbs as-is.
		if ( empty( $breadcrumbs ) || 'post' !== $this->data->name ) {
			return $breadcrumbs;
		}

		/**
		 * @todo This is a workaround since WPGraphQL doesnt support an archive type.
		 */

		$blog_id = get_option( 'page_for_posts' );

		if ( ! $blog_id ) {
			return $breadcrumbs;
		}

		$should_show_blog = RMHelper::get_settings( 'general.breadcrumbs_blog_page' );

		if ( ! $should_show_blog || 'page' !== get_option( 'show_on_front' ) ) {
			return $breadcrumbs;
		}

		$breadcrumb_title = RMHelper::get_post_meta( 'breadcrumb_title', $blog_id ) ?: get_the_title( $blog_id );
		$permalink        = get_permalink( $blog_id );

		$breadcrumbs[] = [
			'text'     => $breadcrumb_title ?: null,
			'url'      => $permalink ?: null,
			'isHidden' => false,
		];

		return $breadcrumbs;
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
			throw new UserError( esc_html__( 'There is no archive URI for the provided post type', 'wp-graphql-rank-math' ) );
		}

		return $term_link;
	}
}
