<?php

namespace Pageon\Html\Meta\Item;

use Pageon\Html\Meta\MetaItem;

final class CharsetMeta implements MetaItem
{
    /**
     * @var string
     */
    private $charset;

    /**
     * @param string $charset
     *
     * @return MetaItem
     */
    public static function create($charset) : MetaItem {
        return new self($charset);
    }

    /**
     * @return string
     */
    public function render() : string {
        return "<meta charset=\"{$this->charset}\">";
    }

    /**
     * CharsetMeta constructor.
     *
     * @param string $charset
     */
    public function __construct(string $charset) {
        $this->charset = $charset;
    }
}
