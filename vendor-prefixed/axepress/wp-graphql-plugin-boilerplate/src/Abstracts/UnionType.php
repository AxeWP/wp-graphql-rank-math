<?php

/**
 * Abstract class to make it easy to register Union types to WPGraphQL.
 *
 * @package \AxeWP\GraphQL\Abstracts
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Helper\Compat;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeResolverTrait;
if (!class_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\UnionType')) {
    /**
     * Class - UnionType
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-type UnionTypeConfig array{
     *   description:callable():string,
     *   eagerlyLoadType: bool,
     *   typeNames: string[],
     *   resolveType: callable,
     * }
     *
     * @extends \WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\Type<UnionTypeConfig>
     *
     * phpcs:enable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    abstract class UnionType extends Type
    {
        use TypeResolverTrait;
        /**
         * Gets the array of possible GraphQL types that can be resolved to.
         *
         * @return string[]
         */
        abstract public static function get_possible_types(): array;
        /**
         * {@inheritDoc}
         */
        public static function register(): void
        {
            /** @todo remove when WPGraphQL > 2.3.0 is required. */
            $config = Compat::resolve_graphql_config(static::get_type_config());
            register_graphql_union_type(static::get_type_name(), $config);
        }
        /**
         * {@inheritDoc}
         *
         * @return UnionTypeConfig
         */
        protected static function get_type_config(): array
        {
            $config = parent::get_type_config();
            $config['typeNames'] = static::get_possible_types();
            $config['resolveType'] = static::get_type_resolver();
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