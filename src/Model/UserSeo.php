<?php
/**
 * The SEO model for User objects.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use GraphQL\Error\Error;

/**
 * Class - UserSeo
 *
 * @property int $ID the database ID.
 */
class UserSeo extends Seo {
	/**
	 * Stores the incoming post data
	 *
	 * @var \WP_User $data
	 */
	protected $data;

	/**
	 * The global authordata at time of Model generation
	 *
	 * @var ?\WP_User
	 */
	protected $global_authordata;

	/**
	 * The settings prefix
	 *
	 * @var string
	 */
	protected string $prefix;

	/**
	 * Constructor.
	 *
	 * @param int $user_id .
	 * @throws \GraphQL\Error\Error .
	 */
	public function __construct( int $user_id ) {
		$object = get_user_by( 'id', $user_id );
		if ( false === $object ) {
			throw new Error(
				sprintf(
					// translators: post id .
					__( 'Invalid user id %s passed to UserSeo model.', 'wp-graphql-rank-math' ),
					$user_id,
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
		global $wp_query, $post, $author, $authordata;

		// Store variables for resetting at tear down.
		$this->global_post       = $post;
		$this->global_authordata = $authordata;

		if ( $this->data instanceof \WP_User ) {

			// Reset postdata.
			$wp_query->reset_postdata();

			// Parse the query to setup global state.
			$wp_query->parse_query(
				[
					'author_name' => $this->data->user_nicename,
				]
			);

			// Setup globals.
			$wp_query->is_author         = true;
			$GLOBALS['authordata']       = $this->data; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$author                      = $this->data->ID; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound,SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
			$wp_query->queried_object    = $this->data;
			$wp_query->queried_object_id = $this->data->ID;
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
		global $author;
		if ( $this->data instanceof \WP_User ) {
			$author                = isset( $this->global_authordata ) ? $this->global_authordata->ID : null; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
			$GLOBALS['authordata'] = $this->global_authordata; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$GLOBALS['post']       = $this->global_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}

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
						$title = $this->get_meta( 'breadcrumb_title', '', $this->data->display_name );

						return ! empty( $title ) ? html_entity_decode( $title, ENT_QUOTES ) : null;
					},
					'ID'              => fn (): int => $this->database_id,
				]
			);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_object_type(): string {
		return 'User';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \GraphQL\Error\UserError If no valid term link.
	 */
	protected function get_object_url(): string {
		return get_author_posts_url( $this->database_id );
	}
}
