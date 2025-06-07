<?php

/**
 * Interface for for classes that register a GraphQL type with connections to the GraphQL schema.
 *
 * @package \AxeWP\GraphQL\Interfaces
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces;

if (!interface_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithConnections')) {
    /**
     * Interface - TypeWithConnections
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-type ConnectionConfigArgs array{
     *   type: string|array<string,string|array<string,string>>,
     *   description: callable(): string,
     *   defaultValue?: mixed
     * }
     *
     * @phpstan-type ConnectionConfig array{
     *   toType: string,
     *   description: callable():string,
     *   args?: array<string,ConnectionConfigArgs>,
     *   connectionInterfaces?: string[],
     *   oneToOne?: bool,
     *   resolve?: callable
     * }
     *
     * phpcs:enable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    interface TypeWithConnections extends GraphQLType
    {
        /**
         * Gets the properties for the type.
         *
         * @return array<string,ConnectionConfig>
         */
        public static function get_connections(): array;
    }
}