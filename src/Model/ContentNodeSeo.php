<?php
/**
 * The SEO model for ContentNode objects.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use \GraphQL\Error\Error;

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
	 * @throws Error .
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
	protected function init() {
		if ( empty( $this->fields ) ) {
			parent::init();

			$this->fields = array_merge(
				$this->fields,
				[
					'breadcrumbTitle' => fn() : ?string => $this->get_meta( 'breadcrumb_title', '', get_the_title( $this->database_id ) ) ?: null,
					'isPillarContent' => fn() : bool => ! empty( $this->get_meta( 'pillar_content' ) ),
					'seoScore'        => fn() => [
						'hasFrontendScore' => fn() : bool => rank_math()->frontend_seo_score->score_enabled(),
						'badgeHtml'        => function (): ?string {
							$output = rank_math_get_seo_score();
							$output = ! empty( $output ) ? str_replace( [ "\n", "\t", "\r" ], '', $output ) : null;

							return ! empty( $output ) ? $output : null;
							},
						'rating'           => function() : ?string {
							$score = rank_math()->frontend_seo_score->get_score( $this->database_id );

							return rank_math()->frontend_seo_score->get_rating( (int) $score ) ?: null;
						},
						'score'            => fn() : int => (int) rank_math()->frontend_seo_score->get_score( $this->database_id ),
					],
				]
			);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_rest_url() : string {
		return get_rest_url( null, '/rankmath/v1/getHead' ) . '?url=' . get_permalink( $this->database_id );
	}
}
