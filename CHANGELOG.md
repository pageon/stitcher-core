# Changelog

## [WIP] 1.0.0-alpha5 

- Add PHP 7.0 support
- Add Command tests for Router commands and Generate command.
- Improve meta support.
- Refactor the use of the dependency container, enabling future extensions. (See breaking changes).
- Fix folder parser bug with nested folders.
- Fix with Sass compiler import paths. The Sass compiler can now also look directly in `src/css`.
- Use stable version of `pageon/html-meta`.

#### Breaking changes

A last big refactor has been done to support more extensions in the future. This means both the `Console` and the `DevController`
 now live in a different namespace. Two changes need to be made, which can be performed with the following commands.

```
rm stitcher
rm dev/index.php

cp vendor/Brendt/Stitcher/install/stitcher ./stitcher
cp vendor/Brendt/Stitcher/install/dev/index.php ./dev/index.php
```
 
## 1.0.0-alpha4

Add dynamic .htaccess support. 

This update adds some BC breaking changes, two things need to happen for old projects to work.
   
Regenerate the composer autoload mapping.   

```sh
composer dump-autoload -o
```

Add the `environment` parameter to config files.

```yaml
# config.yml
environment: production

# dev/dev.config.yml
environment: development
```

## 1.0.0-alpha3

- Bump PHP minimum required version to PHP 7.1
