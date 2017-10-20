<?php

namespace Pageon\Html\Meta;

interface SocialMeta
{
    public function __construct(Meta $meta, string $type = 'article');

    public function title(string $title) : SocialMeta;

    public function description(string $description) : SocialMeta;

    public function image(string $image) : SocialMeta;
}
