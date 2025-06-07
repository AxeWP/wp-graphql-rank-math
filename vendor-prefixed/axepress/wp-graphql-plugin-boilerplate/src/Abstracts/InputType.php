<?php

/**
 * Abstract class to make it easy to register Input types to WPGraphQL.
 *
 * @package \AxeWP\GraphQL\Abstracts
 */
declare (strict_types=1);
namespace WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts;

use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Helper\Compat;
use WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Interfaces\TypeWithInputFields;
if (!class_exists('\WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\InputType')) {
    /**
     * Class - InputType
     *
     * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation -- PHPStan formatting.
     *
     * @phpstan-import-type InputFieldConfig from \AxeWP\GraphQL\Interfaces\TypeWithInputFields
     *
     * @phpstan-type InputTypeConfig array{
     *  description: callable():string,
     *  eagerlyLoadType: bool,
     *  fields: array<string,InputFieldConfig>,
     * }
     *
     * @extends \WPGraphQL\RankMath\Vendor\AxeWP\GraphQL\Abstracts\Type<InputTypeConfig>
     *
     * phpcs:enable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation
     */
    abstract class InputType extends Type implements TypeWithInputFields
    {
        /**
         * {@inheritDoc}
         */
        public static function register(): void
        {
            /** @todo remove when WPGraphQL > 2.3.0 is required. */
            $config = Compat::resolve_graphql_config(static::get_type_config());
            register_graphql_input_type(static::get_type_name(), $config);
        }
        /**
         * {@inheritDoc}
         *
         * @return InputTypeConfig
         */
        protected static function get_type_config(): array
        {
            $config = parent::get_type_config();
            $config['fields'] = static::get_fields();
            return $config;
        }
    }
}