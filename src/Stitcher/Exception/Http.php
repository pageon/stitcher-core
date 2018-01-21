<?php

namespace Stitcher\Exception;

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
        $body = <<<MD
The URI `$uri` could not be find. 

Please check in `site.yaml` or `routes.php`.

```
developmentServer:
    class: Stitcher\Application\DevelopmentServer
    arguments:
        - '%publicDirectory%'
        - '@parsePartial'
    calls:
        - ['setRouter', ['@router']]
        - ['setMarkdownParser', ['@markdownParser']]
productionServer:
    class: Stitcher\Application\ProductionServer
    arguments:
        - '%publicDirectory%'
    calls:
        - ['setRouter', ['@router']]
        - ['setMarkdownParser', ['@markdownParser']]
```
MD;

        return new self("`{$uri}` was not found.", $body, 404);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
