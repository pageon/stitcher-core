<?php

namespace Pageon\Html\Meta\Item;

use Pageon\Html\Meta\MetaItem;

final class PropertyMeta implements MetaItem
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
     * @param string $property
     * @param string $content
     *
     * @return MetaItem
     */
    public static function create(string $property, string $content) : MetaItem {
        return new self($property, $content);
    }

    /**
     * @return string
     */
    public function render() : string {
        return "<meta property=\"{$this->name}\" content=\"{$this->content}\">";
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

}
