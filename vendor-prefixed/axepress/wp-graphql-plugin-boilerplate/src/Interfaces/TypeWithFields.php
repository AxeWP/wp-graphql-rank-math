<?php

/**
 * Interface for for classes that register a GraphQL type with fields to the GraphQL schema.
 *
 * @package \AxeWP\GraphQL\Interfaces
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces;

if (!interface_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithFields')) {
    /**
     * Interface - TypeWithFields.
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-type FieldConfigArgs array{
     *   type:string|array<string,string|array<string,string>>,
     *   description:callable(): string,
     *   defaultValue?:mixed
     * }
     *
     * @phpstan-type FieldConfig array{
     *   type:string|array<string,string|array<string,string>>,
     *   description:callable(): string,
     *   args?:array<string,FieldConfigArgs>,
     *   resolve?:callable,
     *   deprecationReason?:callable(): string,
     * }
     *
     * phpcs:enable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    interface TypeWithFields extends GraphQLType
    {
        /**
         * Gets the fields for the type.
         *
         * @return array<string,FieldConfig>
         */
        public static function get_fields(): array;
    }
}