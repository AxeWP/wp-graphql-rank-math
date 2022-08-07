# Changelog

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
- Feat: Add `breadcrumbs` trail to `BaseSeoFields`.

### Breaking schema changes
- Field `RankMathGeneral.breadcrumbs` changed type from `RankMathBreadcrumbs` to `RankMathBreadcrumbsConfig`.

## v0.0.1
- Initial Release.
