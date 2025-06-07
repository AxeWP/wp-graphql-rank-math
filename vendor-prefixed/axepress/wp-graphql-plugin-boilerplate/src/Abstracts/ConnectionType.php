<?php

/**
 * Abstract class to make it easy to register Connection types to WPGraphQL.
 *
 * @package \AxeWP\GraphQL\Abstracts
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\GraphQLType;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\Registrable;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Traits\TypeNameTrait;
if (!class_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\ConnectionType')) {
    /**
     * Class - ConnectionType
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-type ConnectionArgsConfig array{
     *  type: string|array<string,string|array<string,string>>,
     *  description: callable():string,
     *  defaultValue?: mixed
     * }
     *
     * @phpstan-type ConnectionFieldConfig array{
     *   type: string|array<string,string | array<string,string>>,
     *   description: callable():string,
     *   args?: array<string,ConnectionArgsConfig>,
     *   resolve?: callable,
     *   deprecationReason?: callable():string,
     * }
     *
     * @phpstan-type ConnectionConfig array{
     *   fromType: string,
     *   fromFieldName: string,
     *   resolve: callable,
     *   oneToOne?: bool,
     *   toType?: string,
     *   connectionArgs?: array<string,ConnectionArgsConfig>,
     *   connectionFields?: array<string,ConnectionFieldConfig>,
     * }
     *
     * phpcs:enable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    abstract class ConnectionType implements GraphQLType, Registrable
    {
        use TypeNameTrait;
        /**
         * {@inheritDoc}
         */
        public static function init(): void
        {
            add_action('graphql_register_types', [static::class, 'register']);
        }
        /**
         * Defines all possible connection args for the GraphQL type.
         *
         * @return array<string,ConnectionArgsConfig>
         */
        abstract protected static function connection_args(): array;
        /**
         * Gets the $config array used to register the connection to the GraphQL type.
         *
         * @param ConnectionConfig $config The connection config array.
         *
         * @return ConnectionConfig
         */
        protected static function get_connection_config($config): array
        {
            return array_merge(['toType' => static::get_type_name()], $config);
        }
        /**
         * Returns a filtered array of connection args.
         *
         * @param ?string[] $filter_by an array of specific connections to return.
         *
         * @return array<string,ConnectionArgsConfig>
         */
        final public static function get_connection_args(?array $filter_by = null): array
        {
            $connection_args = static::connection_args();
            if (empty($filter_by)) {
                return $connection_args;
            }
            $filtered_args = [];
            foreach ($filter_by as $filter) {
                $filtered_args[$filter] = $connection_args[$filter];
            }
            return $filtered_args;
        }
    }
}