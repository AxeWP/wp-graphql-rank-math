# Filter Hooks

## Table of Contents

- [Filter Hooks](#filter-hooks)
	- [Table of Contents](#table-of-contents)
	- [GraphQL Type Registration](#graphql-type-registration)
		- [`graphql_seo_registered_{type}_classes`](#graphql_seo_registered_type_classes)
			- [Parameters](#parameters)
		- [`graphql_seo_resolved_model`](#graphql_seo_resolved_model)
			- [Parameters](#parameters-1)
		- [`gaphql_seo_resolved_type_name`](#gaphql_seo_resolved_type_name)
			- [Parameters](#parameters-2)
	- [Reference](#reference)

## GraphQL Type Registration

### `graphql_seo_registered_{type}_classes`

Filters the list of classes that are registered as GraphQL Types.

Possible `type` values are `connection`, `enum`, `field`, `input`, `interface`, `mutation` and `object`.

```php
apply_filters( 'graphql_seo_registered_connection_classes', $classes );
apply_filters( 'graphql_seo_registered_enum_classes', $classes );
apply_filters( 'graphql_seo_registered_field_classes', $classes );
apply_filters( 'graphql_seo_registered_input_classes', $classes );
apply_filters( 'graphql_seo_registered_interface_classes', $classes );
apply_filters( 'graphql_seo_registered_mutation_classes', $classes );
apply_filters( 'graphql_seo_registered_object_classes', $classes );
```

#### Parameters

* **`$classes`** _(array)_ : The list of PHP classes that are registered as GraphQL Types. These classes must extend the `WPGraphQL\Seo\Vendor\AxeWP\GraphQL\Interfaces\GraphQLType` interface.


### `graphql_seo_resolved_model`

Filters the SEO model clas used for a given GraphQL `Node`.

This is useful for adding support for your own `SEO` model.

```php
apply_filters( 'graphql_seo_resolved_model', $seo_model, $node_model );
```

#### Parameters

* **`$seo_model`** _(?WPGraphQL\Seo\Model\Seo)_ : The SEO model class to use. This class must extend the `WPGraphQL\Seo\Model\Seo` class.
* **`$node_model`** _(WPGraphQL\Model\Model)_ : The Modeled GraphQL object that needs its SEO model class resolved.

### `gaphql_seo_resolved_type_name`

Filters the GraphQL object type name that should be used by the given SEO model.

```php
apply_filters( 'gaphql_seo_resolved_type_name', $type_name, $model );
```

#### Parameters

* **`$type_name`** _(string)_ : The GraphQL object type name that should be used by the given SEO model.
* **`$model`** _(WPGraphQL\Model\Model)_ : The SEO model that needs its GraphQL object type name resolved. This should (but does not have to) extend the `WPGraphQL\Seo\Model\Seo` class.

## Reference
- [Actions](./actions.md)
- [Filters ( 🎯 You are here )](./filters.md)
- [Queries](./queries.md)
