<?php

/**
 * Abstract class to make it easy to register Enum types to WPGraphQL.
 *
 * @package \AxeWP\GraphQL\Abstracts
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Helper\Compat;
if (!class_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\EnumType')) {
    /**
     * Class - EnumType
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-type EnumValueConfig array{
     *   description:callable():string,
     *   value:mixed,
     *   deprecationReason?:callable():string
     * }
     *
     * @phpstan-type EnumTypeConfig array{
     *  description: callable():string,
     *  eagerlyLoadType: bool,
     *  values:array<string,EnumValueConfig>,
     * }
     *
     * @extends \WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\Type<EnumTypeConfig>
     *
     * phpcs:enable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    abstract class EnumType extends Type
    {
        /**
         * Gets the Enum values configuration array.
         *
         * @return array<string,EnumValueConfig>
         */
        abstract public static function get_values(): array;
        /**
         * {@inheritDoc}
         */
        public static function register(): void
        {
            /** @todo remove when WPGraphQL > 2.3.0 is required. */
            $config = Compat::resolve_graphql_config(static::get_type_config());
            register_graphql_enum_type(static::get_type_name(), $config);
        }
        /**
         * {@inheritDoc}
         *
         * @return EnumTypeConfig
         */
        protected static function get_type_config(): array
        {
            $config = parent::get_type_config();
            $config['values'] = static::get_values();
            return $config;
        }
    }
}