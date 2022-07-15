<?php
/**
 * A shim for RankMath's Paper class that lets us set the current object.
 *
 * @see https://support.rankmath.com/ticket/provide-way-to-reset-paper-instance/
 *
 * @package \WPGraphQL\RankMath\Utils;
 */

namespace WPGraphQL\RankMath\Utils;

use RankMath\Post;
use RankMath\Helper;
use RankMath\Paper\IPaper;
use RankMath\Sitemap\Router;
use RankMath\Traits\Hooker;
use MyThemeShop\Helpers\Str;
use MyThemeShop\Helpers\Url;
use RankMath\Helpers\Security;

/**
 * Paper class.
 *
 * @codeCoverageIgnore
 */
class Paper {

	use Hooker;

	/**
	 * Hold the class instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Hold current paper object.
	 *
	 * @var IPaper of object
	 */
	private $paper = null;

	/**
	 * Hold title.
	 *
	 * @var ?string
	 */
	private ?string $title;

	/**
	 * Hold description.
	 *
	 * @var ?string
	 */
	private ?string $description;

	/**
	 * Hold robots.
	 *
	 * @var ?array
	 */
	private $robots;

	/**
	 * Hold canonical.
	 *
	 * @var ?array{
	 *   'canonical'?: ?string,
	 *   'canonical_unpaged'?: ?string,
	 *   'canonical_no_override'?: ?string,
	 * }
	 */
	private $canonical;

	/**
	 * Hold keywords.
	 *
	 * @var ?array
	 */
	private ?array $keywords;

	/**
	 * Holds the current post object
	 *
	 * @var object
	 */
	private object $object;

	/**
	 * Constructor
	 *
	 * @param object $object .
	 */
	public function __construct( $object ) {
		$this->object = $object;
		// @phpstan-ignore-next-line
		rank_math()->variables->setup();
		$this->setup();
		self::$instance = $this;
	}

	/**
	 * Initialize object
	 */
	public static function get() : Paper {
		return self::$instance;
	}

	/**
	 * Setup paper.
	 */
	private function setup() : void {
		foreach ( $this->get_papers() as $class_name => $is_valid ) {
			if ( $this->do_filter( 'paper/is_valid/' . strtolower( $class_name ), $is_valid ) ) {
				/** @var class-string<IPaper> $class_name */
				$class_name  = '\\RankMath\\Paper\\' . $class_name;
				$this->paper = new $class_name();
				break;
			}
		}

		if ( ! method_exists( $this->paper, 'set_object' ) ) {
			return;
		}

		$this->paper->set_object( $this->object );
	}

	/**
	 * Get papers types.
	 *
	 * @return array
	 */
	private function get_papers() {
		return $this->do_filter(
			'paper/hash',
			[
				'Search'    => is_search(),
				'Shop'      => Post::is_shop_page(),
				'Singular'  => Post::is_home_static_page() || Post::is_simple_page(),
				'Blog'      => Post::is_home_posts_page(),
				'Author'    => is_author() || ( Helper::is_module_active( 'bbpress' ) && function_exists( 'bbp_is_single_user' ) && bbp_is_single_user() ),
				'Date'      => is_date(),
				'Taxonomy'  => is_category() || is_tag() || is_tax(),
				'Archive'   => is_archive(),
				'Error_404' => is_404(),
				'Misc'      => true,
			]
		);
	}

	/**
	 * Get title after sanitization.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( isset( $this->title ) ) {
			return $this->title;
		}

		/**
		 * Allow changing the title.
		 *
		 * @param string $title The page title being put out.
		 */
		$this->title = $this->do_filter( 'frontend/title', $this->paper->title() );

		// Early Bail!!
		if ( '' === $this->title ) {
			return $this->title;
		}

		// Remove excess whitespace.
		$this->title = preg_replace( '[\s\s+]', ' ', $this->title );

		// Capitalize Titles.
		if ( Helper::get_settings( 'titles.capitalize_titles' ) ) {
			$this->title = ucwords( $this->title );
		}

		$this->title = wp_strip_all_tags( stripslashes( $this->title ), true );
		$this->title = esc_html( $this->title );
		$this->title = convert_smilies( $this->title );

		return $this->title;
	}

	/**
	 * Get description after sanitization.
	 *
	 * @return string
	 */
	public function get_description() {
		if ( isset( $this->description ) ) {
			return $this->description;
		}

		/**
		* Allow changing the meta description sentence.
		*
		* @param string $description The description sentence.
		*/
		$this->description = $this->do_filter( 'frontend/description', trim( $this->paper->description() ) );

		// Early Bail!!
		if ( '' === $this->description ) {
			return $this->description;
		}

		$this->description = wp_strip_all_tags( stripslashes( $this->description ), true );
		$this->description = esc_attr( $this->description );

		return $this->description;
	}

	/**
	 * Get robots after sanitization.
	 */
	public function get_robots() : array {
		if ( isset( $this->robots ) ) {
			return $this->robots;
		}

		$this->robots = (array) $this->paper->robots();

		if ( empty( $this->robots ) ) {
			$this->robots = self::robots_combine( Helper::get_settings( 'titles.robots_global' ) );
		}

		$this->validate_robots();
		$this->respect_settings_for_robots();

		/**
		 * Allows filtering of the meta robots.
		 *
		 * @param array $robots The meta robots directives to be echoed.
		 */
		$this->robots = (array) $this->do_filter( 'frontend/robots', array_unique( (array) $this->robots ) );
		$this->advanced_robots();

		return (array) $this->robots;
	}

	/**
	 * Validate robots.
	 */
	private function validate_robots() : void {
		if ( empty( $this->robots ) || ! is_array( $this->robots ) ) {
			$this->robots = [
				'index'  => 'index',
				'follow' => 'follow',
			];
			return;
		}

		$this->robots = array_intersect_key(
			$this->robots,
			[
				'index'        => '',
				'follow'       => '',
				'noarchive'    => '',
				'noimageindex' => '',
				'nosnippet'    => '',
			]
		);

		// Add Index and Follow.
		if ( ! isset( $this->robots['index'] ) ) {
			$this->robots = [ 'index' => 'index' ] + $this->robots;
		}
		if ( ! isset( $this->robots['follow'] ) ) {
			$this->robots = [ 'follow' => 'follow' ] + $this->robots;
		}
	}

	/**
	 * Add Advanced robots.
	 */
	private function advanced_robots() : void {
		// Early Bail if robots is set to noindex or nosnippet!
		if ( ( isset( $this->robots['index'] ) && 'noindex' === $this->robots['index'] ) || ( isset( $this->robots['nosnippet'] ) && 'nosnippet' === $this->robots['nosnippet'] ) ) {
			return;
		}

		$advanced_robots = $this->paper->advanced_robots();
		if ( ! is_array( $advanced_robots ) ) {
			$advanced_robots = wp_parse_args(
				Helper::get_settings( 'titles.advanced_robots_global' ),
				[
					'max-snippet'       => -1,
					'max-video-preview' => -1,
					'max-image-preview' => 'large',
				]
			);

			$advanced_robots = self::advanced_robots_combine( $advanced_robots );
		}

		$advanced_robots = array_intersect_key(
			$advanced_robots,
			[
				'max-snippet'       => '',
				'max-video-preview' => '',
				'max-image-preview' => '',
			]
		);

		/**
		 * Allows filtering of the advanced meta robots.
		 *
		 * @param array $robots The meta robots directives to be echoed.
		 */
		$advanced_robots = $this->do_filter( 'frontend/advanced_robots', array_unique( $advanced_robots ) );

		$this->robots = ! empty( $advanced_robots ) ? $this->robots + $advanced_robots : $this->robots;
	}

	/**
	 * Get focus keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		if ( isset( $this->keywords ) ) {
			return $this->keywords;
		}

		/** @var string|array $keywords */
		$keywords = $this->paper->keywords();

		if ( ! empty( $keywords ) ) {
			$keywords = ! is_array( $keywords ) ? [ $keywords ] : (array) $keywords;
		}

		$keywords = $keywords ?: [];

		/**
		 * Allows filtering of the meta keywords.
		 *
		 * @param array $keywords The meta keywords to be echoed.
		 */
		$this->keywords = $this->do_filter( 'frontend/keywords', $keywords );

		return $this->keywords;
	}

	/**
	 * Respect some robots settings.
	 */
	private function respect_settings_for_robots() : void {
		// Force override to respect the WP settings.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 0 === absint( get_option( 'blog_public' ) ) || isset( $_GET['replytocom'] ) ) {
			$this->robots['index']  = 'noindex';
			$this->robots['follow'] = 'nofollow';
		}

		// Noindex for sub-pages.
		if ( is_paged() && Helper::get_settings( 'titles.noindex_archive_subpages' ) ) {
			$this->robots['index'] = 'noindex';
		}
	}

	/**
	 * Get canonical after sanitization.
	 *
	 * @param bool $un_paged    Whether or not to return the canonical with or without pagination added to the URL.
	 * @param bool $no_override Whether or not to return a manually overridden canonical.
	 *
	 * @return ?string
	 */
	public function get_canonical( $un_paged = false, $no_override = false ) {
		if ( empty( $this->canonical ) ) {
			$this->generate_canonical();
		}

		$canonical = $this->canonical['canonical'] ?? null;
		if ( $un_paged ) {
			$canonical = $this->canonical['canonical_unpaged'] ?? null;
		} elseif ( $no_override ) {
			$canonical = $this->canonical['canonical_no_override'] ?? null;
		}

		return $canonical;
	}

	/**
	 * Generate canonical URL parts.
	 */
	private function generate_canonical() : void {
		$this->canonical = wp_parse_args(
			$this->paper->canonical(),
			[
				'canonical'          => null,
				'canonical_unpaged'  => null,
				'canonical_override' => null,
			]
		);

		$canonical          = $this->canonical['canonical'] ?? null;
		$canonical_unpaged  = $this->canonical['canonical_unpaged'] ?? null;
		$canonical_override = $this->canonical['canonical_override'] ?? null;

		if ( is_front_page() || ( function_exists( 'ampforwp_is_front_page' ) && ampforwp_is_front_page() ) ) {
			$canonical = user_trailingslashit( home_url() );
		}

		// If not singular than we can have pagination.
		if ( ! is_singular() ) {
			$canonical_unpaged = $canonical;
			$canonical         = $this->get_canonical_paged( $canonical );
		}

		$this->canonical['canonical_unpaged']     = $canonical_unpaged;
		$this->canonical['canonical_no_override'] = $canonical;

		// Force canonical links to be absolute, relative is NOT an option.
		$canonical = Str::is_non_empty( $canonical ) && true === Url::is_relative( $canonical ) ? $this->base_url( $canonical ) : $canonical;
		$canonical = Str::is_non_empty( $canonical_override ) ? $canonical_override : $canonical;

		/**
		 * Allow filtering of the canonical URL.
		 *
		 * @param string $canonical The canonical URL.
		 */
		$this->canonical['canonical'] = apply_filters( 'rank_math/frontend/canonical', $canonical );
	}

	/**
	 * Get canonical paged
	 *
	 * @param string $canonical Canonical URL.
	 *
	 * @return string
	 */
	private function get_canonical_paged( $canonical ) {
		global $wp_rewrite;

		if ( ! $canonical || get_query_var( 'paged' ) < 2 ) {
			return $canonical;
		}

		if ( ! $wp_rewrite->using_permalinks() ) {
			return Security::add_query_arg_raw(
				'paged',
				get_query_var( 'paged' ),
				is_front_page() ? trailingslashit( $canonical ) : $canonical
			);
		}

		return user_trailingslashit(
			trailingslashit( is_front_page() ? Router::get_base_url( '' ) : $canonical ) .
			trailingslashit( $wp_rewrite->pagination_base ) .
			get_query_var( 'paged' )
		);
	}

	/**
	 * Parse the home URL setting to find the base URL for relative URLs.
	 *
	 * @param  string $path Optional path string.
	 * @return string
	 */
	private function base_url( $path = null ) {
		return Utils::base_url( $path ?? '' );
	}

	/**
	 * Simple function to use to pull data from $options.
	 *
	 * All titles pulled from options will be run through the Helper::replace_vars function.
	 *
	 * @param string       $id      Name of the page to get the title from the settings for.
	 * @param object|array $source  Possible object to pull variables from.
	 * @param string       $default Default value if nothing found.
	 *
	 * @return string
	 */
	public static function get_from_options( $id, $source = [], $default = '' ) {
		$value = Helper::get_settings( "titles.$id" );
		// Break loop.
		if ( ! Str::ends_with( 'default_snippet_name', $value ) && ! Str::ends_with( 'default_snippet_desc', $value ) ) {
			$value = \str_replace(
				[ '%seo_title%', '%seo_description%' ],
				[ '%title%', '%excerpt%' ],
				$value
			);
		}

		return Helper::replace_vars( '' !== $value ? $value : $default, $source );
	}

	/**
	 * Make robots values as keyed array.
	 *
	 * @param array $robots  Main instance.
	 * @param bool  $default Append default.
	 *
	 * @return array
	 */
	public static function robots_combine( $robots, $default = false ) {
		if ( empty( $robots ) || ! is_array( $robots ) ) {
			return ! $default ? [] : [
				'index'  => 'index',
				'follow' => 'follow',
			];
		}

		$robots = array_combine( $robots, $robots );

		// Fix noindex key to index.
		if ( isset( $robots['noindex'] ) ) {
			$robots = [ 'index' => $robots['noindex'] ] + $robots;
			unset( $robots['noindex'] );
		}

		// Fix nofollow key to follow.
		if ( isset( $robots['nofollow'] ) ) {
			$robots = [ 'follow' => $robots['nofollow'] ] + $robots;
			unset( $robots['nofollow'] );
		}

		return $robots;
	}

	/**
	 * Make robots values as keyed array.
	 *
	 * @param array $advanced_robots  Main instance.
	 *
	 * @return array
	 */
	public static function advanced_robots_combine( $advanced_robots ) {
		if ( empty( $advanced_robots ) ) {
			return [];
		}

		$robots = [];
		foreach ( $advanced_robots as $key => $data ) {
			if ( $data ) {
				$robots[ $key ] = $key . ':' . $data;
			}
		}
		return $robots;
	}

	/**
	 * Should apply shortcode on content.
	 *
	 * @return bool
	 */
	public static function should_apply_shortcode() {
		if (
			Post::is_woocommerce_page() ||
			( function_exists( 'is_wcfm_page' ) && is_wcfm_page() )
		) {
			return false;
		}

		return apply_filters( 'rank_math/paper/auto_generated_description/apply_shortcode', false );
	}
}
