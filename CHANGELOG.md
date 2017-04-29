# Changelog

## [WIP] 1.0.0-alpha5 

- Add PHP 7.0 support
- Add Command tests for Router commands and Generate command.
- Add plugin support!
- Improved meta support.
- Improved generate command feedback.
- Refactor the use of the dependency container, enabling future extensions. (See breaking changes).
- Fix folder parser bug with nested folders.
- Fix with Sass compiler import paths. The Sass compiler can now also look directly in `src/css`. This is useful when 
- Fix global meta tags not being loaded.
 doing includes and IDE auto-completion.
- Fix for meta tags on detail pages not correctly set. 
- Use stable version of `pageon/html-meta`.

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
