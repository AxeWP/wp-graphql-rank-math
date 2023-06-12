<?php
/**
 * The Redirection model
 *
 * @package \WPGraphQL\RankMath\Modules\Redirection\Model
 */

namespace WPGraphQL\RankMath\Modules\Redirection\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use WPGraphQL\RankMath\Utils\RMUtils;

/**
 * Class - Redirection
 *
 * @property ?int $databaseId The redirection database ID.
 * @property ?string $dateCreated The date the redirection was created.
 * @property ?string $dateCreatedGmt The GMT date the redirection was created.
 * @property ?string $dateModified The date the redirection was last modified.
 * @property ?string $dateModifiedGmt The GMT date the redirection was last modified.
 * @property ?string $dateLastAccessed The date the redirection was last accessed.
 * @property ?string $dateLastAccessedGmt The GMT date the redirection was last accessed.
 * @property ?int $hits The number of hits for this redirection.
 * @property string $id The global ID of the redirection.
 * @property ?string $redirectToUrl The URL to redirect to.
 * @property ?array $sources The sources for this redirection.
 * @property ?string $status The status of the redirection.
 * @property ?int $type The type of redirection.
 */
class Redirection extends Model {
	/**
	 * Stores the incoming redirection to be modeled.
	 *
	 * @var array<string,mixed>
	 */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param array<string,mixed> $data The redirection data.
	 */
	public function __construct( array $data ) {
		$this->data = $data;

		$allowed_restricted_fields = [
			'databaseId',
			'id',
			'redirectToUrl',
			'sources',
			'status',
			'type',
		];

		parent::__construct( 'rank_math_redirections', $allowed_restricted_fields );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function is_private(): bool {
		return 'active' !== $this->data['status'] && ! RMUtils::has_cap( 'redirections' );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function init() {
		if ( empty( $this->fields ) ) {
			$this->fields = [
				'databaseId'          => fn (): ?int => ! empty( $this->data['id'] ) ? (int) $this->data['id'] : null,
				'dateCreated'         => fn (): ?string => ! empty( $this->data['created'] ) ? $this->data['created'] : null,
				'dateCreatedGmt'      => function (): ?string {
					$date_created = $this->data['created'] ?: null;

					return ! empty( $date_created ) ? get_gmt_from_date( $date_created ) : null;
				},
				'dateModified'        => fn (): ?string => ! empty( $this->data['updated'] ) ? $this->data['updated'] : null,
				'dateModifiedGmt'     => function (): ?string {
					$date_modified = $this->data['updated'] ?: null;

					return ! empty( $date_modified ) ? get_gmt_from_date( $date_modified ) : null;
				},
				'dateLastAccessed'    => fn (): ?string => ! empty( $this->data['last_accessed'] ) && '0000-00-00 00:00:00' !== $this->data['last_accessed'] ? $this->data['last_accessed'] : null,
				'dateLastAccessedGmt' => function (): ?string {
					$date_last_accessed = ! empty( $this->data['last_accessed'] ) && '0000-00-00 00:00:00' !== $this->data['last_accessed'] ? $this->data['last_accessed'] : null;

					return ! empty( $date_last_accessed ) ? get_gmt_from_date( $date_last_accessed ) : null;
				},
				'hits'                => fn (): ?int => isset( $this->data['hits'] ) ? (int) $this->data['hits'] : null,
				'id'                  => function (): string {
					return Relay::toGlobalId( 'redirection', (string) $this->data['id'] );
				},
				'redirectToUrl'       => fn () => ! empty( $this->data['url_to'] ) ? $this->data['url_to'] : null,
				'sources'             => function (): ?array {
					$serialized_sources = $this->data['sources'];

					return ! empty( $serialized_sources ) ? maybe_unserialize( $serialized_sources ) : null;
				},
				'status'              => fn (): ?string => ! empty( $this->data['status'] ) ? $this->data['status'] : null,
				'type'                => fn (): ?int => ! empty( $this->data['header_code'] ) ? $this->data['header_code'] : null,
			];
		}
	}
}
