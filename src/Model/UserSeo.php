<?php
/**
 * The SEO model for User objects.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use \GraphQL\Error\Error;
use GraphQL\Error\UserError;

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
	 * The settings prefix
	 *
	 * @var string
	 */
	protected string $prefix;


	/**
	 * Constructor.
	 *
	 * @param int $user_id .
	 * @throws Error .
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
	protected function init() {
		if ( empty( $this->fields ) ) {
			parent::init();

			$this->fields = array_merge(
				$this->fields,
				[
					'breadcrumbTitle' => fn() : ?string => $this->get_meta( 'breadcrumb_title', '', $this->data->display_name ) ?: null,
					'ID'              => fn(): int => $this->database_id,
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
		$author_url = get_author_posts_url( $this->database_id );
		return get_rest_url( null, '/rankmath/v1/getHead' ) . '?url=' . $author_url;
	}
}
