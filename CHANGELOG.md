# Changelog

## Unreleased

- feat: Add plugin dependencies header.
- fix: Plugin versions in dependency check logic is now in sync with the version requirements.
- fix: Update the return type of the `type` field in the `Redirection` model to correctly return a `?string`.
- chore!: Add `WPGraphQL/RankMath` namespace to root-level files ( `activation.php`, `deactivation.php`, `wp-graphql-rank-math.php` ).
- chore: Declare `strict_types` in all PHP files.
- chore: Refactor autoloader logic to `Autoloader` class.
- chore: Update Composer dev-deps and fix newly-surfaced PHPCS smells.
- chore: Implement PHPStan strict rules.
- ci: Update GitHub Actions to latest versions.
- ci: Test plugin compatibility with WordPress 6.5.0.
- ci: Update Strauss to v0.17.0

## v0.1.1

This _minor_ release adds support for more social fields in the `RankMathSocialMetaSettings` and `RankMathUserSeo` types. Additionally, it fixes a bug where the `seo.openGraph.image` field would fail to resolve if provided a string image.

There are **no breaking changes**.

### What's changed

- feat: Expose `additionalProfiles` field on `RankMathSocialMetaSettings`. props: @colis 🙌
- feat: Expose `facebookProfileUrl`, `twitterUserName`, and `additionalProfiles` social fields on `RankMathUserSeo`. props: @colis 🙌
- fix: Correctly resolve `seo.openGraph.image` field when parsed value is a string.

## v0.1.0

This _minor_ release bumps the plugin version to 0.1.0! However, there are **no breaking changes** in this release.

Additionally, we fixed a few bugs regarding `seo.openGraph` resolution, and deprecated a setting that was removed in RankMath v1.0.211.

The reason for the version bump is to make it easier to update future releases in accordance with our [versioning policy](README.md#updating-and-versioning). While the plugin version number is indicative of the (projected) schema maturity and not the underlying code (which has been used in enteprise production environments for almost two years), the new features and improvements that would warrant major changes to the schema are currently blocked upstream. By bumping to v0.1.0, we can continue to push patch releases in the meantime without users having to worry about breaking changes.

### What's changed

- fix: Deprecate `rankMathSettings.sitemaps.general.canPingSearchEngines`, as it was removed in RankMath v1.0.211.
- fix: Improve SEO `head` data fetching to load Rank Math modules more consistently.
- fix: Correctly parse OG product meta data when resolving `seo.OpenGraph`. H/t @joanpzen
- chore: Pin WPBrowser to v3.4.x to avoid breaking changes in v3.5+.
- ci: Test plugin compatibility against WordPress 6.4.2

This release was sponsored by [Red Rocks Web Development](https://redrockswebdevelopment.com/) 😍.

## v0.0.16

- fix: Correctly parse excluded Post/Term IDs when returning nodes for Sitemap. Props @marcinkrzeminski
- chore: Update Composer dev-dependencies.
- chore!: Bump minimum supported WPGraphQL version to v1.14.0.
- chore!: Bump minimum supported RankMath version to v1.0.201.
- chore!: Bump minimum supported WordPress version to v6.0.
- ci: Test Plugin Compatibility with WP 6.3.2 and PHP 8.2.

## v0.0.15

- chore: Update Composer dev-dependencies.
- chore: Update WPGraphQL Coding Standards to v2.0.0-beta and lint.
- chore: Fix minimum supported WordPress version to be 5.6, which is the minimum requirement for RankMath 1.0.90.
- ci: Test Plugin compatibility with WordPress 6.3.

## v0.0.14

- fix: Fetch the correct SEO data when resolving custom taxonomy terms. Props @lucguerraz
- dev!: Move `SEO::$global_authordata` property to the `UserSeo` model and make nullable.
- dev: Move `seo.breadcrumbs` resolution from the `RankMathSeo` interface to the `SEO` model.
- chore: Update Composer dev-dependencies.

## v0.0.13

- feat: Expose Redirections to the GraphQL schema.
- dev: Convert HTML entities for `breadcrumbTitle`, `description`, and `title` fields to their corresponding characters. H/t @sdegetaus
- chore: Implement `axepress/wp-graphql-cs` ruleset for PHP_CodeSniffer.
- chore: Update Composer dependencies.
- docs: Relocate query docs to `docs/reference/queries.md`, and add docs on querying redirections, and included WordPress actions and filters.

## v0.0.12

- fix: Use correct post type when querying for `ContentNodeSeo` on revisions. Props @idflood
- dev: Show admin notice when conflicting `wp-graphql-yoast-seo` is installed.
- chore: Update Strauss and Composer dev-dependencies to latest versions.
- ci: Test plugin compatibility with WordPress 6.2

## v0.0.11

- fix: Pass necessary data to resolve `OpenGraphMeta.image` field.
- chore: Update Composer dev-dependencies.

## v0.0.10

- dev: Check plugin dependency versions.
- dev: Namespace Composer dependencies with Strauss.
- dev: Wrap global functions in `function_exists()` checks.
- chore: Update WPGraphQL Plugin Boilerplate dependency to `v0.0.8`.
- ci: Add coverage reports to CodeClimate.
- tests: Regenerate `_support` classes.

## v0.0.9

- chore: update WPGraphQL Plugin Boilerplate dependency to v0.0.7.

## v0.0.8

- feat!: Rename `RankMathBaseSeoFields` interface to `RankMathSeo`.
- feat!: Change `seo` field type to `RankMathSeo` interface and implement with `NodeWithRankMathSeo` interface.
- feat!: Change `RankMathCommentNodeSeo` from GraphQL object to interface.
- fix!: Rename `playerStreamContentTypee` to `playerStreamContentType`.
- fix: Prevent duplicate OpenGraph meta tags by clearing `RankMath` hooks before fetching.
- fix: Allow `OpenGraphTwitter.appCountry` to resolve to `null`.
- fix: Set object globals for head in Model constructor.
- dev!: Rename `Seo::get_rest_url_param()` to `Seo::get_object_url()`
- dev: Add the following WordPress filters: `graphql_seo_model_class`, `graphql_seo_resolved_type_name`, `graphql_seo_types_with_seo`.
- dev: Locally generate `<head>` instead using RankMath's REST route.
- chore!: Bump minimum WPGraphQL version to v1.8.1.
- chore: Add explicit PHP 8.1 support.
- chore: Update composer dependencies.
- ci: Update GitHub Actions to latest versions.
- ci: Fix Xdebug version for PHP 7.4.
- ci: Update readme shields.
- tests: Set category when testing `ContentNodeSeoQueryCept` so `articleMeta.section` returns a value.

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

- feat: Add `breadcrumbs` trail to `Seo`.

### Breaking schema changes

- dev: Field `RankMathGeneral.breadcrumbs` changed type from `RankMathBreadcrumbs` to `RankMathBreadcrumbsConfig`.

## v0.0.1

- Initial Release.
