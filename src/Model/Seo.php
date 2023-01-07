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
			],
			$allowed_fields
		);

		parent::__construct( $capability, $allowed_fields );
	}

	/**
	 * {@inheritDoc}
	 */
	public function setup() : void {
		rank_math()->variables->setup();

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
				'fullHead'      => function() : ?string {
					$head = $this->get_head();
					return $head ?: null;
				},
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

		$url = $this->get_rest_url_param();

		$this->setup_post_head( $url );

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
		wp();

		if ( $headless->is_home ) {
			$GLOBALS['wp_query']->is_home = true;
		}

		remove_filter( 'option_rewrite_rules', [ $headless, 'fix_query_notice' ] );

		// Setup Rank Math.
		rank_math()->variables->setup();
		new \RankMath\Frontend\Frontend();
	}
}
