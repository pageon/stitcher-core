# Meta compiling

Stitcher support different kinds of pages, each of those pages requiring different kind of meta tags. The 
 `Brendt\Html\Meta` module adds support for managing and rendering meta tags. From different places in the application,
 meta tags can be configured for a page. Once this page is rendered, all its meta tag will be read and made available via 
 the `meta` template function. Furthermore, this function allows overriding specific meta values before rendering the 
 meta tags. Finally, general meta tags can be configured in config files. These tags are automatically added to all pages.
 
## Template integration

The `meta` function can be called in the `<head>` of a template. This function doesn't require any additional parameters,
 but can take an optional parameter called `extra`. This parameter expects an array of key/value pairs and will generate
 named meta tags: `<meta name="key" content="value">`.
 
## Adaptor support

Two adapters will add custom meta tags: the `pagination` adapter and `collection` adapter.

The `pagination` adapter will add `link` meta tags pointing to the next and previous pages if any exist.

The `collection` adapter will parse the given entry on detail pages. There are three fields which can be configured: 
 `title`, `description` and `image`. Not only normal meta tags will be added, but also `og:` and `twitter:` tags.

A separate `meta` field can be specified in entries to add more meta tags. These tags will be generated as named meta tags.
 This fields expects an array of key/value pairs, same like the `extra` parameter in the `meta` function.

## Page variables

Any page can add a `meta` variable, which works exactly the same way as the `meta` variable in entries. 

## General configuration

General config files can also specify a `meta` entry. This configuration also works like above examples.
