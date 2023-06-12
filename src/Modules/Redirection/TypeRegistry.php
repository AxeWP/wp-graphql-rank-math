<?php
/**
 * The module registry.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection
 * @since 0.0.13
 */

namespace WPGraphQL\RankMath\Modules\Redirection;

use WPGraphQL\RankMath\Modules\Redirection\Connection;
use WPGraphQL\RankMath\Modules\Redirection\Fields;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Input;
use WPGraphQL\RankMath\Modules\Redirection\Type\WPObject;
use WPGraphQL\RankMath\Utils\RMUtils;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\Registrable;

/**
 * Class - TypeRegistry
 */
class TypeRegistry implements Registrable {
	public const MODULE_NAME = 'redirections';

	/**
	 * {@inheritDoc}
	 */
	public static function init(): void {
		// Bail if the module is not active.
		if ( ! RMUtils::is_module_active( self::MODULE_NAME ) ) {
			return;
		}

		// Register the types.
		add_filter( 'graphql_seo_registered_enum_classes', [ self::class, 'enums' ] );
		add_filter( 'graphql_seo_registered_input_classes', [ self::class, 'inputs' ] );
		add_filter( 'graphql_seo_registered_object_classes', [ self::class, 'objects' ] );
		add_filter( 'graphql_seo_registered_field_classes', [ self::class, 'fields' ] );
		add_filter( 'graphql_seo_registered_connection_classes', [ self::class, 'connections' ] );
	}

	/**
	 * List of Enum classes to register.
	 *
	 * @param class-string[] $existing_classes The existing classes.
	 *
	 * @return class-string[]
	 */
	public static function enums( array $existing_classes ): array {
		$classes_to_register = [
			Enum\RedirectionBehaviorEnum::class,
			Enum\RedirectionComparisonTypeEnum::class,
			Enum\RedirectionConnectionOrderByEnum::class,
			Enum\RedirectionStatusEnum::class,
			Enum\RedirectionTypeEnum::class,
		];

		return array_merge( $existing_classes, $classes_to_register );
	}

	/**
	 * List of Input classes to register.
	 *
	 * @param class-string[] $existing_classes The existing classes.
	 *
	 * @return class-string[]
	 */
	public static function inputs( array $existing_classes ): array {
		$classes_to_register = [
			Input\RedirectionConnectionOrderbyInput::class,
		];

		return array_merge( $existing_classes, $classes_to_register );
	} 

	/**
	 * List of Object classes to register.
	 *
	 * @param class-string[] $existing_classes The existing classes.
	 *
	 * @return class-string[]
	 */
	public static function objects( array $existing_classes ): array {
		$classes_to_register = [
			WPObject\RedirectionSettings::class,
			WPObject\RedirectionSource::class,
			WPObject\Redirection::class,
		];

		return array_merge( $existing_classes, $classes_to_register );
	}

	/**
	 * List of Field classes to register.
	 *
	 * @param class-string[] $existing_classes The xisting classes.
	 *
	 * @return class-string[]
	 */
	public static function fields( array $existing_classes ): array {
		$classes_to_register = [
			Fields\GeneralSettings::class,
			Fields\RootQuery::class,
		];

		return array_merge( $existing_classes, $classes_to_register );
	}

	/**
	 * List of Connection classes to register.
	 *
	 * @param class-string[] $existing_classes The xisting classes.
	 *
	 * @return class-string[]
	 */
	public static function connections( array $existing_classes ): array {
		$classes_to_register = [
			Connection\RedirectionConnection::class,
		];

		return array_merge( $existing_classes, $classes_to_register );
	}
}
