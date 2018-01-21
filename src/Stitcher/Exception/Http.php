<?php

namespace Stitcher\Exception;

use Pageon\Config;
use Stitcher\File;

class Http extends StitcherException
{
    protected $statusCode;

    public function __construct(string $title, string $body, int $statusCode = 500)
    {
        parent::__construct($title, $body);

        $this->statusCode = $statusCode;
    }

    public static function notFound(string $uri): Http
    {
        $siteConfigurationFile = File::relativePath(Config::get('configurationFile'));

        $body = <<<MD
The URI `$uri` could not be find. 

Please check in `$siteConfigurationFile` for static pages or `./src/routes.php` for dynamic routes.

```
$uri:
    template: # ...
    variables: 
        # ...
    config:
        # ...
```
MD;

        return new self("`{$uri}` was not found.", $body, 404);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
