# GraphQL Queries

## Table of Contents

  - [Querying SEO data](#querying-seo-data)
    - [Post Objects](#post-objects)
    - [Post Type Archives](#post-type-archives)
    - [Term Archives](#term-archives)
    - [Users](#users)
    - [Querying with `nodeByUri`](#querying-with-nodebyuri)
  - [Querying Sitemaps](#querying-sitemaps)
  - [Querying Redirections](#querying-redirections)
  - [Reference](#reference)

## Querying SEO data

SEO data is made available on the `seo` field, which is made available using the `RankMathNodeWithSeo` interface.

> **Note** This is not a complete list of GraphQL fields and types added to the schema. Please refer to the WPGraph_i_QL IDE for more queries and their documentation.

### Post Objects

```graphql
query MyPosts {
  posts {
    nodes {
      databaseId
      title
      seo { # The SEO data for the current post object.
        breadcrumbs {
          title
          url
          isHidden
        }
        breadcrumbTitle
        canonicalUrl
        description
        focusKeywords
        fullHead
        jsonLd {
          raw
        }
        openGraph {
          articleMeta {
            section
          }
          description
          locale
          siteName
          title
          type
          url
          slackEnhancedData {
            data
            label
          }
          twitterMeta {
            card
            description
            title
          }
        }
        robots
        title
        ... on RankMathContentNodeSeo { # Only available on `contentNode` types.
          isPillarContent
          seoScore {
            badgeHtml
            hasFrontendScore
            rating
            score
          }
        }
      }
      author {
        node {
          seo { # The SEO Data for the post object's author.
            breadcrumbs {
              title
              url
              isHidden
            }
            breadcrumbTitle
            canonicalUrl
            description
            focusKeywords
            fullHead
            robots
            title
          }
        }
      }
      categories {
        nodes {
          name
          seo { # The SEO Data for the post's associated terms.
            breadcrumbs {
              title
              url
              isHidden
            }
            breadcrumbTitle
            canonicalUrl
            description
            focusKeywords
            fullHead
            jsonLd {
              raw
            }
            openGraph {
              locale
              siteName
              type
              url
              twitterMeta {
                card
              }
            }
            robots
            title
          }
        }
      }
    }
  }
}
```

### Post Type Archives

``` graphql
{
  contentTypes {
    nodes {
      name
      seo { # The SEO data for the post type archive
        breadcrumbTitle
        canonicalUrl
        description
        focusKeywords
        fullHead
        jsonLd {
          raw
        }
        openGraph {
          locale
          siteName
          type
          url
          twitterMeta {
            card
          }
        }
        robots
        title
      }
      contentNodes {
        nodes {
          seo { # The SEO data for the associated posts.
            breadcrumbs {
              title
              url
              isHidden
            }
            breadcrumbTitle
            canonicalUrl
            description
            focusKeywords
            fullHead
            jsonLd {
              raw
            }
            openGraph {
              articleMeta {
                section
              }
              description
              locale
              siteName
              title
              type
              url
              slackEnhancedData {
                data
                label
              }
              twitterMeta {
                card
                description
                title
              }
            }
            robots
            title
            ... on RankMathContentNodeSeo { # Only available on `contentNode` types.
              isPillarContent
              seoScore {
                badgeHtml
                hasFrontendScore
                rating
                score
              }
            }
          }
        }
      }
    }
  }
}
```

### Term Archives

```graphql
query MyCategories {
  categories {
    nodes {
      name
      seo { # The SEO data for the current term archive
        breadcrumbs {
          title
          url
          isHidden
        }
        breadcrumbTitle
        canonicalUrl
        description
        focusKeywords
        fullHead
        jsonLd {
          raw
        }
        openGraph {
          locale
          siteName
          type
          url
          twitterMeta {
            card
          }
        }
        robots
        title
      }
      contentNodes {
        nodes {
          databaseId
          title
          seo { ## The SEO data for the posts associated with this term.
            breadcrumbs {
              title
              url
              isHidden
            }
            breadcrumbTitle
            canonicalUrl
            description
            focusKeywords
            fullHead
            jsonLd {
              raw
            }
            openGraph {
              articleMeta {
                section
              }
              description
              locale
              siteName
              title
              type
              url
              slackEnhancedData {
                data
                label
              }
              twitterMeta {
                card
                description
                title
              }
            }
            robots
            title
            ... on RankMathContentNodeSeo { # Only available on `contentNode` types.
              isPillarContent
              seoScore {
                badgeHtml
                hasFrontendScore
                rating
                score
              }
            }
          }
        }
      }
    }
  }
}
```

### Users

```graphql
{
  users {
    nodes {
      name
      seo { # The SEO data for the User profile page.
        breadcrumbs {
          title
          url
          isHidden
        }
        breadcrumbTitle
        canonicalUrl
        description
        focusKeywords
        fullHead
        jsonLd {
          raw
        }
        robots
        title
      }
    }
  }
}
```

### Querying with `nodeByUri`

> **Note**: Currently, `nodeByUri` does not detect links associated with a Rank Math Redirection. This will be addressed [in a future release](https://github.com/AxeWP/wp-graphql-rank-math/issues/53).

```graphql
query MyNodeByUriQuery( $uri: String ) {
  nodeByUri( uri: $uri ) {
    ... on NodeWithRankMathSeo {
      seo {
        breadcrumbs {
          title
          url
          isHidden
        }
        breadcrumbTitle
        canonicalUrl
        description
        focusKeywords
        fullHead
        jsonLd {
          raw
        }
        robots
        title
        ... on RankMathContentNodeSeo {
          isPillarContent
          seoScore {
            score
          }
        }
      }
    }
  }
}
```

## Querying Sitemaps

The Sitemap Module must be enabled in the [Rank Math settings](https://rankmath.com/kb/configure-sitemaps/) for the sitemap data to be available.

```graphql
{
  rankMathSettings {
    sitemap {
      author {
        excludedRoles
        excludedUserDatabaseIds
        sitemapUrl
        connectedAuthors {
          nodes {
            id
          }
        }
      }
      contentTypes {
        customImageMetaKeys
        isInSitemap
        sitemapUrl
        type
        connectedContentNodes {
          nodes {
            uri
          }
        }
      }
      general {
        canPingSearchEngines
        excludedPostDatabaseIds
        excludedTermDatabaseIds
        hasFeaturedImage
        hasImages
        linksPerSitemap
      }
      indexUrl
      taxonomies {
        hasEmptyTerms
        isInSitemap
        sitemapUrl
        type
        connectedAuthors {
          nodes {
            uri
          }
        }
      }
    }
  }
}
```

## Querying Redirections

The Redirections Module [must be enabled] in the [Rank Math settings](https://rankmath.com/kb/setting-up-redirections/) for the sitemap data to be available.

> **Note**: Currently, `nodeByUri` does not detect links associated with a Rank Math Redirection. This will be addressed [in a future release](https://github.com/AxeWP/wp-graphql-rank-math/issues/53).
>
> In the interim, we recommend handling redirects in your frontend app, such as with the [NextJS `redirects` config key](https://nextjs.org/docs/pages/api-reference/next-config-js/redirects).

```graphql
{
  redirections {
    nodes {
      databaseId
      dateCreated
      dateCreatedGmt
      dateLastAccessed
      dateLastAccessedGmt
      dateModified
      dateModifiedGmt
      hits
      id
      redirectToUrl # the URL to redirect to
      sources { # The rules that trigger the redirect
        comparison
        ignore
        pattern
      }
      status
      type
    }
  }
}
```

## Reference
- [Actions](./actions.md)
- [Filters](./filters.md)
- [Queries ( 🎯 You are here )](./queries.md)
