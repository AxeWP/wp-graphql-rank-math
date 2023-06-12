# Action Hooks

## Table of Contents
  - [Activation / Deactivation](#activation--deactivation)
    - [`graphql_seo_activate`](#graphql_seo_activate)
    - [`graphql_seo_deactivate`](#graphql_seo_deactivate)
    - [`graphql_seo_delete_data`](#graphql_seo_delete_data)
      - [Parameters](#parameters)
    - [`graphql_seo_before_register_types`](#graphql_seo_before_register_types)
    - [`graphql_seo_after_register_types`](#graphql_seo_after_register_types)

## Activation / Deactivation
### `graphql_seo_activate`

Runs when the plugin is activated.

```php
do_action( 'graphql_seo_activate' );
```

### `graphql_seo_deactivate`

Runs when the plugin is deactivated.

```php
do_action( 'graphql_seo_deactivate' );
```

### `graphql_seo_delete_data`

Runs after the plugin deletes its data on deactivate.

```php
do_action( 'graphql_seo_delete_data' );


## Lifecycle
### `graphql_seo_init`

Runs when the plugin is initialized.

```php
do_action( 'graphql_seo_init', $instance );
```

#### Parameters

* **`$instance`** _(WPGraphQL\Seo\Main)_ : The instance of the plugin.

### `graphql_seo_before_register_types`

Runs before the plugin registers any GraphQL types to the schema.

```php
do_action( 'graphql_seo_before_register_types' );
```

### `graphql_seo_after_register_types`

Runs after the plugin finishes registering all GraphQL types to the schema.

```php
do_action( 'graphql_seo_after_register_types' );
```

## Reference
- [Actions ( 🎯 You are here )](./actions.md)
- [Filters](./filters.md)
- [Queries](./queries.md)
