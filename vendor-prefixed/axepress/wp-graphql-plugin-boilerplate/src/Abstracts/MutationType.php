<?php

/**
 * Abstract class to make it easy to register Mutation types to WPGraphQL.
 *
 * @package \AxeWP\GraphQL\Abstracts
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Helper\Compat;
if (!class_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\MutationType')) {
    /**
     * Class - MutationType
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-type MutationInputFieldConfig array{
     *   type:string|array<string,string|array<string,string>>,
     *   description:callable():string,
     *   defaultValue?:string
     * }
     *
     * @phpstan-type MutationOutputFieldConfig array{
     *   type:string|array<string,string|array<string,string>>,
     *   description:callable():string,
     *   args?:array<string,array{
     *     type:string|array<string,string|array<string,string>>,
     *     description:callable():string,
     *     defaultValue?:mixed
     *   }>,
     *   resolve?:callable,
     *   deprecationReason?:callable():string
     * }
     *
     * @phpstan-type MutationTypeConfig array{
     *   description:callable():string,
     *   eagerlyLoadType:bool,
     *   inputFields:array<string,MutationInputFieldConfig>,
     *   outputFields:array<string,MutationOutputFieldConfig>,
     *   mutateAndGetPayload:callable,
     * }
     *
     * @extends \WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\Type<MutationTypeConfig>
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    abstract class MutationType extends Type
    {
        /**
         * Gets the input fields for the mutation.
         *
         * @return array<string,MutationInputFieldConfig>
         */
        abstract public static function get_input_fields(): array;
        /**
         * Gets the fields for the type.
         *
         * @return array<string,MutationOutputFieldConfig>
         */
        abstract public static function get_output_fields(): array;
        /**
         * Defines the mutation data modification closure.
         */
        abstract public static function mutate_and_get_payload(): callable;
        /**
         * Register mutations to the GraphQL Schema.
         */
        public static function register(): void
        {
            /** @todo remove when WPGraphQL > 2.3.0 is required. */
            $config = Compat::resolve_graphql_config(static::get_type_config());
            register_graphql_mutation(static::get_type_name(), $config);
        }
        /**
         * {@inheritDoc}
         */
        public static function get_description(): string
        {
            return '';
        }
        /**
         * {@inheritDoc}
         *
         * @return MutationTypeConfig
         */
        protected static function get_type_config(): array
        {
            $config = parent::get_type_config();
            $config['inputFields'] = static::get_input_fields();
            $config['outputFields'] = static::get_output_fields();
            $config['mutateAndGetPayload'] = static::mutate_and_get_payload();
            return $config;
        }
    }
}