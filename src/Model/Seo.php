<?php
/**
 * The abstract SEO model.
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
 *
 * @property string $type The type of object.
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
	 * @var \RankMath\Paper\Paper;
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
	 * The Global Post at time of Model generation
	 *
	 * @var \WP_Post
	 */
	protected $global_post;

	/**
	 * The global authordata at time of Model generation
	 *
	 * @var \WP_User
	 */
	protected $global_authordata;

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

		$allowed_fields = array_merge(
			[
				'title',
				'description',
				'robots',
				'fullHead',
				'jsonLd',
				'openGraph',
				'type',
			],
			$allowed_fields
		);

		parent::__construct( $capability, $allowed_fields );

		rank_math()->variables->setup();
		// Seat up RM Globals.
		$url = $this->get_object_url();

		$this->setup_post_head( $url );
	}

	/**
	 * {@inheritDoc}
	 */
	public function setup() : void {
		Paper::reset();
		/** @var \RankMath\Paper\Paper $paper */
		$paper        = Paper::get();
		$this->helper = $paper;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'title'         => function() : ?string {
					return $this->helper->get_title() ?: null;
				},
				'description'   => function() : ?string {
					return $this->helper->get_description() ?: null;
				},
				'robots'        => function() : ?array {
					return $this->helper->get_robots() ?: null;
				},
				'canonicalUrl'  => function() : ?string {
					return $this->helper->get_canonical() ?: null; 
				},
				'focusKeywords' => function() : ?array {
					$keywords = $this->helper->get_keywords();

					return ! empty( $keywords ) ? explode( ',', $keywords ) : null;
				},
				'fullHead'      => fn() : ?string => $this->get_head() ?: null,
				'jsonLd'        => fn() : ?array => $this->get_json_ld() ?: null,
				'openGraph'     => function() {
					$head = $this->get_head();

					return ! empty( $head ) ? $this->parse_og_tags( $head ) : null;
				},
				'type'          => function() : string {
					return $this->get_object_type();
				},
			];
		}
	}

	/**
	 * Gets the JsonLD data for the node.
	 *
	 * @throws UserError If JsonLD data cannot be parsed.
	 */
	protected function get_json_ld() : array {
		ob_start();
		$json = new \RankMath\Schema\JsonLD();
		$json->setup();
		$json->json_ld();
		$raw_output = ob_get_clean();

		preg_match( '/>(.*?)<\/script>/s', $raw_output ?: '', $matches );
		
		$json_ld = json_decode( $matches[1], true );

		if ( ! empty( json_last_error() ) ) {
			throw new UserError(
				sprintf(
					// translators: json error.
					__( 'There was an error generating the JSON-LD output. Message %s', 'wp-graphql-rank-math' ),
					json_last_error_msg(),
				)
			);
		}

		return [
			'raw'     => $raw_output ?: null,
			'context' => $json_ld['@context'] ?? null,
			'graph'   => ! empty( $json_ld['@graph'] ) ? 
				array_map(
					fn( $graph_item ) => $this->get_json_ld_graph_data( $graph_item ),
					$json_ld['@graph']
				) :
				null,
		];
	}

	/**
	 * Populates the graph data for the jsonld Object.
	 *
	 * @param array $json_ld The JSonLD graph object.
	 */
	protected function get_json_ld_graph_data( array $json_ld ) : array {
		return [
			'author'        => $json_ld['author'] ?? null,
			'dateModified'  => $json_ld['date_modified'] ?? null,
			'datePublished' => $json_ld['date_published'] ?? null,
			'description'   => $json_ld['description'] ?? null,
			'keywords'      => ! empty( $json_ld['keywords'] ) ? explode( ',', $json_ld['keywords'] ) : null,
			'publisher'     => $json_ld['publisher'] ?? null,
			'type'          => $json_ld['@type'] ?? null,
			'id'            => $json_ld['@id'] ?? null,
			'name'          => $json_ld['name'] ?? null,
		];
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
	 * Gets the object type used to determine how the GraphQL interface should resolve.
	 */
	abstract public function get_object_type() : string;

	/**
	 * Gets the object-specific url to use for generating the RankMath <head>.
	 */
	abstract protected function get_object_url() : string;

	/**
	 * Gets the object-specific url to use for generating the RankMath <head>.
	 *
	 * @deprecated 0.0.8
	 */
	protected function get_rest_url_param() : string {
		_deprecated_function( __FUNCTION__, '0.0.8', __NAMESPACE__ . '::get_object_url()' );
		return $this->get_object_url();
	}

	/**
	 * Gets all the tags that go in the <head>.
	 *
	 * Shims the `RankMath\Rest\Headless::get_html_head() private method to avoid a REST Call.
	 *
	 * @throws Error     When the REST request is invalid.
	 * @throws UserError When REST response fails.
	 */
	protected function get_head() : ?string {
		if ( false !== $this->full_head ) {
			return $this->full_head;
		}

		ob_start();
		do_action( 'wp' );
		do_action( 'rank_math/head' );

		$head = ob_get_clean();

		$this->full_head = $head ?: null;

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

	/**
	 * Prepare head output for a URL.
	 *
	 * Shims the RankMath\Rest\Headless::setup_post_head() private method to avoid a REST call.
	 *
	 * @param string $url The URL.
	 */
	private function setup_post_head( string $url ) : void {
		$headless = new \RankMath\Rest\Headless();
		// Setup WordPress.
		$_SERVER['REQUEST_URI'] = esc_url_raw( $headless->generate_request_uri( $url ) );
		remove_all_actions( 'wp' );
		remove_all_actions( 'parse_request' );
		remove_all_actions( 'rank_math/head' );
		remove_all_actions( 'rank_math/json_ld' );
		remove_all_actions( 'rank_math/opengraph/facebook' );
		remove_all_actions( 'rank_math/opengraph/twitter' );
		remove_all_actions( 'rank_math/opengraph/slack' );

		if ( $headless->is_home ) {
			$GLOBALS['wp_query']->is_home = true;
		}

		remove_filter( 'option_rewrite_rules', [ $headless, 'fix_query_notice' ] );

		// Setup Rank Math.
		rank_math()->variables->setup();
		new \RankMath\Frontend\Frontend();
	}
}
