<?php
/**
 * The SEO model for ContentNode objects.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use GraphQL\Error\Error;
use GraphQL\Error\UserError;
use WPGraphQL;

/**
 * Class - ContentNodeSeo
 */
class ContentNodeSeo extends Seo {
	/**
	 * Stores the incoming post data.
	 *
	 * @var \WP_Post $data
	 */
	protected $data;

	/**
	 * The database id for the current object.
	 *
	 * @var integer
	 */
	protected int $database_id;

	/**
	 * The settings prefix.
	 *
	 * @var string
	 */
	protected string $prefix;

	/**
	 * Constructor.
	 *
	 * @param int $post_id .
	 * @throws \GraphQL\Error\Error .
	 */
	public function __construct( int $post_id ) {
		$object = get_post( $post_id );
		if ( null === $object ) {
			throw new Error(
				sprintf(
					// translators: post id .
					__( 'Invalid post id %s passed to ContentNodeSeo model.', 'wp-graphql-rank-math' ),
					$post_id,
				)
			);
		}

		$this->database_id = $object->ID;

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

		// Bail early if this is not a post.
		if ( ! $this->data instanceof \WP_Post ) {
			return;
		}

		/**
		 * Set the resolving post to the global $post. That way any filters that
		 * might be applied when resolving fields can rely on global post and
		 * post data being set up.
		 */
		$id        = $this->data->ID;
		$post_type = $this->data->post_type;
		$post_name = $this->data->post_name;
		$data      = $this->data;

		if ( 'revision' === $this->data->post_type ) {
			$id     = $this->data->post_parent;
			$parent = get_post( $this->data->post_parent );
			if ( empty( $parent ) ) {
				$this->fields = [];
				return;
			}
			$post_type = $parent->post_type;
			$post_name = $parent->post_name;
			$data      = $parent;
		}

		/**
		 * Clear out existing postdata
		 */
		$wp_query->reset_postdata();

		/**
		 * Parse the query to tell WordPress how to
		 * setup global state
		 */
		switch ( $post_type ) {
			case 'post':
				$wp_query->parse_query(
					[
						'page' => '',
						'p'    => $id,
					]
				);
				break;
			case 'page':
				$wp_query->parse_query(
					[
						'page'     => '',
						'pagename' => $post_name,
					]
				);
				break;
			case 'attachment':
				$wp_query->parse_query( [ 'attachment' => $post_name ] );
				break;
			default:
				$wp_query->parse_query(
					[
						$post_type  => $post_name,
						'post_type' => $post_type,
						'name'      => $post_name,
					]
				);
				break;
		}

		$wp_query->setup_postdata( $data );
		$GLOBALS['post']             = $data; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$wp_query->queried_object    = get_post( $this->data->ID );
		$wp_query->queried_object_id = $this->data->ID;

		parent::setup();
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
						$title = $this->get_meta( 'breadcrumb_title', '', get_the_title( $this->database_id ) );

						return ! empty( $title ) ? html_entity_decode( $title, ENT_QUOTES ) : null;
					},
					'isPillarContent' => fn (): bool => ! empty( $this->get_meta( 'pillar_content' ) ),
					'seoScore'        => fn () => [
						'hasFrontendScore' => static fn (): bool => rank_math()->frontend_seo_score->score_enabled(),
						'badgeHtml'        => static function (): ?string {
							$output = rank_math_get_seo_score();
							$output = ! empty( $output ) ? str_replace( [ "\n", "\t", "\r" ], '', $output ) : null;

							return ! empty( $output ) ? $output : null;
							},
						'rating'           => function (): ?string {
							$score = rank_math()->frontend_seo_score->get_score( $this->database_id );

							return rank_math()->frontend_seo_score->get_rating( (int) $score ) ?: null;
						},
						'score'            => fn (): int => (int) rank_math()->frontend_seo_score->get_score( $this->database_id ),
					],
				]
			);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_object_type(): string {
		$post_types        = WPGraphQL::get_allowed_post_types( 'objects' );
		$current_post_type = $this->data->post_type;

		// If this is a revision, get the post type of the parent.
		if ( 'revision' === $current_post_type ) {
			$current_post_type = get_post_type( $this->data->post_parent );
		}

		return $post_types[ $current_post_type ]->graphql_single_name;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \GraphQL\Error\UserError If no post permalink.
	 */
	protected function get_object_url(): string {
		$permalink = get_permalink( $this->database_id );

		if ( false === $permalink ) {
			throw new UserError( __( 'There is no URI for the provided content node', 'wp-graphql-rank-math' ) );
		}

		return $permalink;
	}
}
