<?php
/**
 * Extends the WPGraphQL Post model.
 *
 * @package \WPGraphQL\RankMath\Model
 */

namespace WPGraphQL\RankMath\Model;

use \RankMath\Helper as RMHelper;
use GraphQL\Error\UserError;
use WPGraphQL\Model\Model;
use WPGraphQL\RankMath\Utils\Paper;

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
	 * @var Paper;
	 */
	protected $helper;

	/**
	 * The settings prefix
	 *
	 * @var string
	 */
	protected string $prefix;

	/**
	 * Constructor.
	 *
	 * @param \WP_User|\WP_Term|\WP_Post|\WP_Post_Type $object .
	 * @param string                                   $capability .
	 * @param string[]                                 $allowed_fields .
	 */
	public function __construct( $object, $capability = '', $allowed_fields = [] ) {
		$this->data = $object;

		$this->helper = new Paper( $object );

		$allowed_fields = array_merge(
			[
				'title',
				'description',
				'robots',
				'fullHead',
				'jsonLd',
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
			$this->fields = [
				'title'         => fn() : ?string => $this->helper::get()->get_title() ?: null,
				'description'   => fn() : ?string => $this->helper::get()->get_description() ?: null,
				'robots'        => fn() : ?array => $this->helper::get()->get_robots() ?: null,
				'canonicalUrl'  => fn() : ?string => $this->helper::get()->get_canonical() ?: null,
				'focusKeywords' => fn() : ?array => $this->helper::get()->get_keywords() ?: null,
				'fullHead'      => fn() : ?string => $this->get_head() ?: null,
				'jsonLd'        => function() {
						ob_start();
						$json = new \RankMath\Schema\JsonLD();
						$json->setup();
						$json->json_ld();
						$output = ob_get_clean();
						return [
							'raw' => function() use ( $output ) {
								return $output;
							},
						];
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
	abstract protected function get_rest_url() : string;

	/**
	 * Gets the head using a REST API request.
	 *
	 * @throws UserError When REST response fails.
	 */
	protected function get_head() : ?string {
		$uri      = $this->get_rest_url();
		$response = wp_remote_get( $uri );

		if ( is_wp_error( $response ) ) {
			throw new UserError(
				// translators: the url.
				sprintf( __( 'The request for the URL %s could not be retrieved. Error Message: ', 'wp-graphql-rank-math' ), $uri, $response->get_error_message() ),
			);
		}
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return ! empty( $data['head'] ) ? $data['head'] : null;
	}
}
