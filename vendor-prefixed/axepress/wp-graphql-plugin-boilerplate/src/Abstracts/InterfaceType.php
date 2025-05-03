<?php

/**
 * Abstract class to make it easy to register Interface types to WPGraphQL.
 *
 * @package \AxeWP\GraphQL\Abstracts
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Helper\Compat;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithFields;
if (!class_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InterfaceType')) {
    /**
     * Class - InterfaceType
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-import-type FieldConfig from \AxeWP\GraphQL\Interfaces\TypeWithFields
     *
     * @phpstan-type InterfaceTypeConfig array{
     *  description:callable():string,
     *  eagerlyLoadType: bool,
     *  fields: array<string,FieldConfig>,
     *  resolveType?: callable,
     *  interfaces?: string[],
     * }
     *
     * @extends \WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\Type<InterfaceTypeConfig>
     *
     * phpcs:enable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    abstract class InterfaceType extends Type implements TypeWithFields
    {
        /**
         * {@inheritDoc}
         */
        public static function register(): void
        {
            /** @todo remove when WPGraphQL > 2.3.0 is required. */
            $config = Compat::resolve_graphql_config(static::get_type_config());
            register_graphql_interface_type(static::get_type_name(), $config);
        }
        /**
         * {@inheritDoc}
         */
        protected static function get_type_config(): array
        {
            $config = parent::get_type_config();
            $config['fields'] = static::get_fields();
            if (method_exists(static::class, 'get_type_resolver')) {
                $config['resolveType'] = static::get_type_resolver();
            }
            if (method_exists(static::class, 'get_interfaces')) {
                $config['interfaces'] = static::get_interfaces();
            }
            return $config;
        }
        /**
         * {@inheritDoc}
         */
        public static function should_load_eagerly(): bool
        {
            return true;
        }
    }
}