# Changelog

## Unreleased

## v0.0.7
- fix: prevent type prefixes clashing with other AxeWP plugins.
- chore: Update composer dependencies.

## v0.0.6 - Better support for Head meta
- feat: setup WP globals in GraphQL models
- chore: update Composer deps.
- ci: use `STEP_DEBUG` flag on integration tests.
- tests: Use functional tests for `openGraph` and `fullHead` queries.
- docs: Add instructions for installing with Composer.

## v0.0.5 - Sitemap Support
- feat: Add support for `Sitemap` module.
- chore: Update Composer deps.

## v0.0.4 - OpenGraph Support
- feat: Add `openGraph` data to `BaseSeoFields`.
- chore: Update Composer deps. 

## v0.0.3
- fix: Ensure `Model\Seo::focus_keywords` callback returns an array.
- fix: Keep `composer.lock` and production `vendor` deps in repository.
- dev!: Rename `Model\Seo::get_rest_url()` to `Model\Seo::get_rest_url_param()`
- dev!: Remove `Utils\Paper` class in favor of `RankMath\Paper::reset()`
- dev!: Bump minimum version of RankMath to `v1.0.90`
- dev: Replace `wp_remote_get()` call with `rest_do_request()` when querying `seo.fullHead`.
- dev: Update `composer.json` meta.
- ci: Update WP & PHP versions used for tests.
- chore: Update composer deps.
- chore: Remove unnecessary PHPStan `ignore` rule.
- chore: fix PHPCompatibilityWP `testVersion` when linting with `phpcs`.

## v0.0.2
- feat: Add `breadcrumbs` trail to `BaseSeoFields`.

### Breaking schema changes
- dev: Field `RankMathGeneral.breadcrumbs` changed type from `RankMathBreadcrumbs` to `RankMathBreadcrumbsConfig`.

## v0.0.1
- Initial Release.
