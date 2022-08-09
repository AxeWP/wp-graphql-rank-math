<?php
/**
 * Extends the WPGraphQL Post model.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use RankMath\Helper as RMHelper;
use GraphQL\Error\Error;
use GraphQL\Error\UserError;
use WPGraphQL\Model\Model;
use RankMath\Paper\Paper;

/**
 * Class - Seo
 */
abstract class Seo extends Model {
	/**
	 * Stores the incoming post data
	 *
	 * @var \WP_Post|\WP_Term|\WP_User|\WP_Post_Type $data
	 */
	protected $data;

	/**
	 * The database id for the current object.
	 *
	 * @var integer
	 */
	protected int $database_id;

	/**
	 * The current RankMath paper helper.
	 *
	 * @var object|Paper;
	 */
	protected $helper;

	/**
	 * The settings prefix
	 *
	 * @var string
	 */
	protected string $prefix;

	/**
	 * The head markup.
	 *
	 * It's stored here to avoid having to query it multiple times.
	 *
	 * A `false` value is used to determine whether an attempt has already been made to fetch it. 
	 * 
	 * @var string|false|null
	 */
	protected $full_head;

	/**
	 * Constructor.
	 *
	 * @param \WP_User|\WP_Term|\WP_Post|\WP_Post_Type $object .
	 * @param string                                   $capability .
	 * @param string[]                                 $allowed_fields .
	 */
	public function __construct( $object, $capability = '', $allowed_fields = [] ) {
		$this->full_head = false;
		$this->data      = $object;

		rank_math()->variables->setup();
		Paper::reset();
		$this->helper = Paper::get();

		$allowed_fields = array_merge(
			[
				'title',
				'description',
				'robots',
				'fullHead',
				'jsonLd',
				'openGraph',
			],
			$allowed_fields
		);

		parent::__construct( $capability, $allowed_fields );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			/** @var Paper $helper */
			$helper = $this->helper;

			$this->fields = [
				'title'         => fn() : ?string => $helper->get_title() ?: null,
				'description'   => fn() : ?string => $helper->get_description() ?: null,
				'robots'        => fn() : ?array => $helper->get_robots() ?: null,
				'canonicalUrl'  => fn() : ?string => $helper->get_canonical() ?: null,
				'focusKeywords' => function() use ( $helper ) : ?array {
					$keywords = $helper->get_keywords();

					return ! empty( $keywords ) ? explode( ',', $keywords ) : null;
				},
				'fullHead'      => fn() : ?string => $this->get_head() ?: null,
				'jsonLd'        => function() {
					ob_start();
					$json = new \RankMath\Schema\JsonLD();
					$json->setup();
					$json->json_ld();
					$output = ob_get_clean();

					return [ 'raw' => $output ?: null ];
				},
				'openGraph'     => function() {
					$head = $this->get_head();

					return ! empty( $head ) ? $this->parse_og_tags( $head ) : null;
				},
			];
		}
	}

	/**
	 * Gets the hydrated meta, falling back to default settings.
	 *
	 * @param string $key The local meta key.
	 * @param string $fallback Optional. The settings meta key.
	 * @param string $default Optional. The default value.
	 *
	 * @return mixed|null
	 */
	protected function get_meta( string $key, string $fallback = '', string $default = '' ) {
		$value = null;
		if ( $this->data instanceof \WP_Post ) {
			$value = RMHelper::get_post_meta( $key, $this->database_id );
		} elseif ( $this->data instanceof \WP_Term ) {
			$value = RMHelper::get_term_meta( $key, $this->database_id );
		} elseif ( $this->data instanceof \WP_User ) {
			$value = RMHelper::get_user_meta( $key, $this->database_id );
		}

		if ( empty( $value ) && ! empty( $fallback ) ) {
			$value = RMHelper::get_settings( "titles.{$fallback}", $default );
			if ( ! empty( $value ) ) {
				$value = RMHelper::replace_vars( $value, $this->data );
			}
		}

		return ( ! empty( $value ) ? $value : $default ) ?: null;
	}

	/**
	 * Gets the object-specific url to use for the REST API RankMath url param.
	 */
	abstract protected function get_rest_url_param() : string;

	/**
	 * Gets the head using a REST API request.
	 *
	 * @throws Error     When the REST request is invalid.
	 * @throws UserError When REST response fails.
	 */
	protected function get_head() : ?string {
		if ( false !== $this->full_head ) {
			return $this->full_head;
		}

		$url_param = $this->get_rest_url_param();

		$rest_url = get_rest_url( null, '/rankmath/v1/getHead?url=' . $url_param );
		$request  = \WP_REST_Request::from_url( $rest_url );

		codecept_debug( $request );

		if ( false === $request ) {
			throw new Error(
				sprintf(
					// translators: %s the URL for the getHead endpoint.
					__( 'Invalid rest request from %s', 'wp-graphql-rank-math' ),
					$rest_url
				)
			);
		}

		// todo: fix PHP notice https://support.rankmath.com/ticket/fetching-rankmath-v1-gethead-with-rest_do_request-logs-a-php-notice/
		$response = rest_do_request( $request );

		if ( $response->is_error() ) {
			/** @var \WP_Error $error */
			$error = $response->as_error();
			throw new UserError(
				// translators: the url.
				sprintf( __( 'The request for the URL %s could not be retrieved. Error Message: ', 'wp-graphql-rank-math' ), $url_param, $error->get_error_message() ),
			);
		}
		$data = $response->get_data();

		$this->full_head = ! empty( $data['head'] ) ? $data['head'] : null;

		return $this->full_head;
	}

	/**
	 * Parses the Open Graph tags from the head.
	 *
	 * @param string $head The head.
	 */
	protected function parse_og_tags( string $head ) : ?array {
		$tags = [];

		if ( preg_match_all( '/<meta (property|name)="([^"]+):([^"]+)" content="([^"]+)" \/>/', $head, $matches ) ) {
			$this->save_tags_from_matches( $matches, $tags );
		}
		codecept_debug( $head );
		codecept_debug( $tags );

		return $tags ?: null;
	}

	/**
	 * Saves the tags from the matches.
	 *
	 * @param array $matches The matches.
	 * @param array $tags The tags array reference.
	 */
	private function save_tags_from_matches( array $matches, array &$tags ) : void {
		// $matches[2] contains the OpenGraph prefix (og, article, twitter, etc ).
		codecept_debug( $matches );
		foreach ( $matches[2] as $key => $prefix ) {
			$property = $matches[3][ $key ];
			$value    = $matches[4][ $key ];

			// If meta tag already exists, save the values as an array.
			if ( isset( $tags[ $prefix ][ $property ] ) ) {
				if ( ! is_array( $tags[ $prefix ][ $property ] ) ) {
					$tags[ $prefix ][ $property ] = [ $tags[ $prefix ][ $property ] ];
				}
				$tags[ $prefix ][ $property ][] = $value;
			} else {
				$tags[ $prefix ][ $property ] = $value;
			}
		}
	}
}
