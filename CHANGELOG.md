# Changelog

## WIP

- Add Parsedown extension to support classes on `<pre>` tags in fenced code blocks.
- Disable directory listing via .htaccess.
- Add `redirect.www` and `redirect.https` options. Allowing to automatically redirect non-www to www, and http to https.
- Add `redirect` option in site config files to make a route redirect to another page.
- Use `pageon/html-meta` ^2.0 from now on. Lots of tweaks to social meta tags were added.
- Add `async` option which, when `ext-pcntl` is installed, will enable asynchronous page rendering.
- Add Parsedown extension to support `target="_blank"` links by prefixing the URL with `*`.
- Add `sitemap.xml` support. When setting the `sitemap.url` variable, a `sitemap.xml` will be generated.
- Fix bug with Collection Adapters not copying meta tags from the base page for its sub-pages.

## 1.0.0-beta1

- Add empty array fallback in `FilterAdapter` to prevent undefined index error.
- Improved plugin initialisation support. The temporary `init` function isn't required anymore, the constructor can now be used.
- Make the adapter factory extensible.
- Improve the CollectionAdapter by adding the `browse` variable. This variable can be used to browse the detail pages. 
 It has a `next` and `prev` key which contains the next and previous entry, if there are any.
- Moved `Brendt\Stitcher\SiteParser` to `Brendt\Stitcher\Parser\Site\SiteParser` and refactored its service definition.
- Added `Brendt\Stitcher\Parser\Site\PageParser` to parse a single page, which is no longer the responsibility of `SiteParser`.
- Bugfix for general meta configuration overriding other meta values.

## 1.0.0-alpha5 

- Add plugin support!
- Add PHP 7.0 support
- Add Command tests for Router commands and Generate command.
- Improved meta support.
- Improved generate command feedback.
- Refactor the use of the dependency container, enabling future extensions. (See breaking changes).
- Use stable version of `pageon/html-meta`.
- Fix folder parser bug with nested folders.
- Fix with Sass compiler import paths. The Sass compiler can now also look directly in `src/css`. 
  This is useful when doing includes and IDE auto-completion.
- Fix global meta tags not being loaded.
- Fix for meta tags on detail pages not correctly set.

#### Breaking changes

A last big refactor has been done to support more extensions in the future. This means both the `Console` and the `DevController`
 now live in a different namespace. You'll need an updated version of `stitcher` and `index.php`. This can be done with the 
 following commands.

```
rm ./stitcher
rm ./dev/index.php
cp vendor/brendt/stitcher/install/stitcher ./stitcher
cp vendor/brendt/stitcher/install/dev/index.php ./dev/index.php
```
 
## 1.0.0-alpha4

- Add dynamic .htaccess support.
- Add meta support.
- Many image parsing bugfixes.

#### Breaking changes

The `environment` parameter should now be added to config files for the dynamic htaccess support to work.

```yaml
# config.yml
environment: production

# dev/dev.config.yml
environment: development
```
