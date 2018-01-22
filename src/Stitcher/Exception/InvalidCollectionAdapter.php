<?php

namespace Stitcher\Exception;

class InvalidCollectionAdapter extends StitcherException
{
    public static function create(): InvalidCollectionAdapter
    {
        return new self(
            "The `collection` adapter requires following configuration: `variable` and `parameter`",
            <<<MD
Take a look at the following example.

```yaml
/blog/{id}:
    # ...
    variables:
        post: src/blog.yaml
    config:
        collection:
            variable: post
            parameter: id
```
MD
        );
    }
}
