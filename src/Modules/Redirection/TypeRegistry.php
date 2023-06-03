<?php
/**
 * The module registry.
 *
 * @package WPGraphQL\RankMath\Modules\Redirection
 * @since   @todo
 */

namespace WPGraphQL\RankMath\Modules\Redirection;

use WPGraphQL\RankMath\Modules\Redirection\Fields\GeneralSettings;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionBehaviorEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\Enum\RedirectionTypeEnum;
use WPGraphQL\RankMath\Modules\Redirection\Type\WPObject\RedirectionSettings;
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
		add_filter( 'graphql_seo_registered_object_classes', [ self::class, 'objects' ] );
		add_filter( 'graphql_seo_registered_field_classes', [ self::class, 'fields' ] );
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
			RedirectionTypeEnum::class,
			RedirectionBehaviorEnum::class,
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
			RedirectionSettings::class,
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
			GeneralSettings::class,
		];

		return array_merge( $existing_classes, $classes_to_register );
	}
}
