<?php

namespace Stitcher\Exception;

class InvalidOrderAdapter extends StitcherException
{
    public static function create(): InvalidOrderAdapter
    {
        return new self(
            'The `order` adapter requires following configuration: `variable`, `field` and `direction` optionally.',
            <<<MD
Take a look at the following example.

```yaml
/page
    # ...
    variables:
        posts: src/blog.yaml
    config:
        order:
            variable: posts
            field: date
            direction: desc|asc
```
MD
        );
    }
}
