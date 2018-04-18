<?php

namespace Pageon\Html\Meta\Item;

use Pageon\Html\Meta\MetaItem;

final class ItemPropMeta implements MetaItem
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $content;

    /**
     * @param string $name
     * @param string $content
     *
     * @return MetaItem
     */
    public static function create(string $name, string $content) : MetaItem {
        return new self($name, $content);
    }

    /**
     * @return string
     */
    public function render(array $extra = []) : string {
        $content = $this->content;

        if ($this->isTitle() && isset($extra['title']['suffix'])) {
            $content = "{$content}{$extra['title']['suffix']}";
        }

        return "<meta itemprop=\"{$this->name}\" content=\"{$content}\">";
    }

    /**
     * NamedMeta constructor.
     *
     * @param string $name
     * @param string $content
     */
    public function __construct(string $name, string $content) {
        $this->name = $name;
        $this->content = htmlentities($content);
    }

    private function isTitle(): bool
    {
        return str_contains($this->name, 'title') || $this->name === 'name';
    }
}
