<?php

namespace Pageon\Html\Meta\Item;

use Pageon\Html\Meta\MetaItem;

final class NameMeta implements MetaItem
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
    public function render() : string {
        return "<meta name=\"{$this->name}\" content=\"{$this->content}\">";
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
