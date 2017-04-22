# Changelog

## [WIP] 1.0.0-alpha5 

- Improve meta support.
- Fix folder parser bug with nested folders.

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
