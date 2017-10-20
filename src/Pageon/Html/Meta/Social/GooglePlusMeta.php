<?php

namespace Pageon\Html\Meta\Social;

use Pageon\Html\Meta\Meta;
use Pageon\Html\Meta\SocialMeta;

final class GooglePlusMeta implements SocialMeta
{
    private $meta;

    public function __construct(Meta $meta, string $type = 'article') {
        $this->meta = $meta;
    }

    public function title(string $title) : SocialMeta {
        $this->meta->itemprop('name', $title);

        return $this;
    }

    public function description(string $description) : SocialMeta {
        $this->meta->itemprop('description', $description);

        return $this;
    }

    public function image(string $image) : SocialMeta {
        $this->meta->itemprop('image', $image);

        return $this;
    }
}
