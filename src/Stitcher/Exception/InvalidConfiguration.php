<?php

namespace Stitcher\Exception;

use Pageon\Config;
use Stitcher\File;

class InvalidConfiguration extends StitcherException
{
    public static function siteConfigurationFileNotFound(): InvalidConfiguration
    {
        return new self(
            'No site configuration file was found.',
            <<<MD
All static pages should be configured in a site configuration file.
The path to this file can be configured in `./config/config.php`

```php
return [
    // ...

    'configurationFile' => File::path('src/site.yaml'),
];
```

This `site.yaml` file contains a list of routes and their configuration.

```yaml
/:
    template: home.twig
    # ...

/blog/page-{page}:
    template: blog/overview.twig
    # ...

/blog/{id}:
    template: blog/detail.twig
    # ...
```
MD

        );
    }

    public static function pageTemplateMissing(string $pageId): InvalidConfiguration
    {
        $templateDirectory = File::relativePath(Config::get('templateDirectory'));

        return new self(
            'A page requires a `template` value.',
            <<<MD
A template file should be saved in the `$templateDirectory` folder. 
Its path is relative to this template direcotry path. 

```yaml
$pageId:
    template: template.twig
```

The `templateDirectory` path can be overridden in a `php` config file which lives in the `./config` directory.

```php
return [
    // ...
    
    'templateDirectory' => File::path('resources/view'),
];
```
MD

        );
    }

    public static function pageIdMissing(): InvalidConfiguration
    {
        return new self('A page requires an `id` value.');
    }

    public static function fileNotFound(string $path): InvalidConfiguration
    {
        return new self("File with path `{$path}` could not be found.");
    }

    public static function templateNotFound(string $path): InvalidConfiguration
    {
        return new self("Template with path `{$path}` could not be found.");
    }

    public static function invalidAdapterConfiguration(string $adapter, string $fields): InvalidConfiguration
    {
        return new self("The `{$adapter}` adapter requires following configuration: {$fields}");
    }

    public static function dotEnvNotFound(string $directory): InvalidConfiguration
    {
        return new self("Could not find `.env` file. Looked in {$directory}");
    }

    public static function missingParameter(string $parameter): InvalidConfiguration
    {
        return new self("Missing parameter `{$parameter}`, did you add it in your config file?");
    }
}
