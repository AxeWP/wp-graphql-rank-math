<?php

/**
 * Abstract class to make it easy to register Object types to WPGraphQL.
 *
 * @package \AxeWP\GraphQL\Abstracts
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Helper\Compat;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithFields;
if (!class_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ObjectType')) {
    /**
     * Class - ObjectType
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-import-type FieldConfig from \AxeWP\GraphQL\Interfaces\TypeWithFields
     * @phpstan-import-type ConnectionConfig from \AxeWP\GraphQL\Interfaces\TypeWithConnections
     *
     * @phpstan-type ObjectTypeConfig array{
     *   description:callable():string,
     *   eagerlyLoadType: bool,
     *   fields: array<string,FieldConfig>,
     *   connections?: array<string,ConnectionConfig>,
     *   resolveType?: callable,
     *   interfaces?: string[],
     * }
     *
     * @extends \WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\Type<ObjectTypeConfig>
     *
     * phpcs:enable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    abstract class ObjectType extends Type implements TypeWithFields
    {
        /**
         * {@inheritDoc}
         */
        public static function register(): void
        {
            /** @todo remove when WPGraphQL > 2.3.0 is required. */
            $config = Compat::resolve_graphql_config(static::get_type_config());
            register_graphql_object_type(static::get_type_name(), $config);
        }
        /**
         * {@inheritDoc}
         *
         * @return ObjectTypeConfig
         */
        protected static function get_type_config(): array
        {
            $config = parent::get_type_config();
            $config['fields'] = static::get_fields();
            if (method_exists(static::class, 'get_connections')) {
                $config['connections'] = static::get_connections();
            }
            if (method_exists(static::class, 'get_interfaces')) {
                $config['interfaces'] = static::get_interfaces();
            }
            return $config;
        }
    }
}