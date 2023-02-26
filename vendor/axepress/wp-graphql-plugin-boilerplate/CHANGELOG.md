# Changelog

## v0.0.8
* feat: Deprecate `AXEWP_PB_HOOK_PREFIX` constant in favor of `Helper::set_hook_prefix()`.
* dev: recommend installation via Strauss.
* chore: exclude `assets` and `bin` from distribution.
* chore: use FQCN in docblocks.
* chore: fix various code smells.

## v0.0.7
* fix: ConnectionType::get_connection_args() should call ::connection_args().

## v0.0.6
* feat: only create classes if not already available.
* fix: scope type_prefix filter to plugin with `AXEWP_PB_HOOK_PREFIX` constant.
* chore: update composer deps
* ci: test library and plugin scaffold separately.

## v0.0.5
* feat: move explicit 3rd party dependencies to doc-blocks.
* chore: update composer deps.
* ci: update GH workflows. 

## v0.0.4
* dev!: Renames the Hookable interface to `Registrable.
* feat!: Use Registrable when registering GraphQL types.

## v0.0.3
* chore: Update composer deps.
* chore: Exclude `wp-graphql-plugin-name` when installing as composer dep.

## v0.0.2
* dev: Build composer deps on PHP 7.4.
* chore: Update composer deps.
* chore: Remove unused PHPStan ignored error.
* ci: Update PHP version used for CodeQuality.

## v0.0.1
* Initial Release
