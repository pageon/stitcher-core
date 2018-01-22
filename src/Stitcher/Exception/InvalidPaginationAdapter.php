<?php

namespace Stitcher\Exception;

class InvalidPaginationAdapter extends StitcherException
{
    public static function create(): InvalidPaginationAdapter
    {
        return new self(
            "The `pagination` adapter requires following configuration: `variable`, `parameter` and `perPage`",
            <<<MD
Take a look at the following example.

```yaml
/page
    # ...
    variables:
        posts: src/blog.yaml
    config:
        pagination:
            variable: posts
            perPage: 7
            parameter: page
```
MD
        );
    }
}
