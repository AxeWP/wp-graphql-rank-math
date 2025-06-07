<?php

/**
 * Interface for for classes that register a GraphQL type with input fields to the GraphQL schema.
 *
 * @package \AxeWP\GraphQL\Interfaces
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces;

if (!interface_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInputFields')) {
    /**
     * Interface - TypeWithInputFields.
     *
     * @phpstan-type InputFieldConfig array{
     *   type:string|array<string,string|array<string,string>>,
     *   description: callable(): string,
     *   defaultValue?:string
     * }
     */
    interface TypeWithInputFields extends GraphQLType
    {
        /**
         * Gets the input fields for the type.
         *
         * @return array<string,InputFieldConfig>
         */
        public static function get_fields(): array;
    }
}