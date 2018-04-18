<?php

namespace Pageon\Html\Meta\Item;

use Pageon\Html\Meta\MetaItem;

final class LinkMeta implements MetaItem
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var string
     */
    private $href;

    /**
     * @param string $rel
     * @param string $href
     *
     * @return MetaItem
     */
    public static function create(string $rel, string $href) : MetaItem {
        return new self($rel, $href);
    }

    /**
     * @return string
     */
    public function render(array $extra = []) : string {
        return "<link rel=\"{$this->rel}\" href=\"{$this->href}\">";
    }

    /**
     * LinkMeta constructor.
     *
     * @param string $rel
     * @param string $href
     */
    public function __construct(string $rel, string $href) {
        $this->rel = $rel;
        $this->href = $href;
    }
}
