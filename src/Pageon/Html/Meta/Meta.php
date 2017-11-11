<?php

namespace Pageon\Html\Meta;

use Pageon\Html\Meta\Item\CharsetMeta;
use Pageon\Html\Meta\Item\HttpEquivMeta;
use Pageon\Html\Meta\Item\ItemPropMeta;
use Pageon\Html\Meta\Item\LinkMeta;
use Pageon\Html\Meta\Item\NameMeta;
use Pageon\Html\Meta\Item\PropertyMeta;
use Pageon\Html\Meta\Social\GooglePlusMeta;
use Pageon\Html\Meta\Social\OpenGraphMeta;
use Pageon\Html\Meta\Social\TwitterMeta;

class Meta
{
    /** @var MetaItem[] */
    private $meta = [];
    /** @var SocialMeta[] */
    private $socialMeta = [];

    final public function __construct(string $charset = 'UTF-8') {
        $this->charset($charset);
        $this->name('viewport', 'width=device-width, initial-scale=1');

        $this->socialMeta = [
            new GooglePlusMeta($this),
            new TwitterMeta($this),
            new OpenGraphMeta($this),
        ];
    }

    public static function create(string $charset = 'UTF-8') : Meta {
        return new self($charset);
    }

    public function render() : string {
        $html = '';

        /**
         * @var string     $type
         * @var MetaItem[] $metaItems
         */
        foreach ($this->meta as $type => $metaItems) {
            foreach ($metaItems as $metaItem) {
                $html .= $metaItem->render() . "\n";
            }
        }

        return $html;
    }

    public function charset(string $charset) : Meta {
        $item = CharsetMeta::create($charset);
        $this->meta['charset'][] = $item;

        return $this;
    }

    public function name(string $name, ?string $content) : Meta {
        if (!$content) {
            return $this;
        }

        $item = NameMeta::create($name, $content);
        $this->meta['name'][$name] = $item;

        return $this;
    }

    public function itemprop(string $name, ?string $content) : Meta {
        if (!$content) {
            return $this;
        }

        $item = ItemPropMeta::create($name, $content);
        $this->meta['itemprop'][$name] = $item;

        return $this;
    }

    public function property(string $property, ?string $content) : Meta {
        if (!$content) {
            return $this;
        }

        $item = PropertyMeta::create($property, $content);
        $this->meta['property'][$property] = $item;

        return $this;
    }

    public function httpEquiv(string $httpEquiv, ?string $content) : Meta {
        if (!$content) {
            return $this;
        }

        $item = HttpEquivMeta::create($httpEquiv, $content);
        $this->meta['httpEquiv'][$httpEquiv] = $item;

        return $this;
    }

    public function link(string $rel, ?string $href) : Meta {
        if (!$href) {
            return $this;
        }

        $item = LinkMeta::create($rel, $href);
        $this->meta['link'][$rel] = $item;

        return $this;
    }

    public function title(?string $content) : Meta {
        if (!$content) {
            return $this;
        }

        $this->name('title', $content);

        foreach ($this->socialMeta as $socialMeta) {
            $socialMeta->title($content);
        }

        return $this;
    }

    public function description(?string $content) : Meta {
        if (!$content) {
            return $this;
        }

        $this->name('description', $content);

        foreach ($this->socialMeta as $socialMeta) {
            $socialMeta->description($content);
        }

        return $this;
    }

    public function image(?string $content) : Meta {
        if (!$content) {
            return $this;
        }

        $this->name('image', $content);

        foreach ($this->socialMeta as $socialMeta) {
            $socialMeta->image($content);
        }

        return $this;
    }
}
