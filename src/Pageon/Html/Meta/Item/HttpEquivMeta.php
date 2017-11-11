<?php

namespace Pageon\Html\Meta\Item;

use Pageon\Html\Meta\MetaItem;

final class HttpEquivMeta implements MetaItem
{
    /**
     * @var string
     */
    private $httpEquiv;

    /**
     * @var string
     */
    private $content;

    /**
     * @param string $httpEquiv
     * @param string $content
     *
     * @return MetaItem
     */
    public static function create(string $httpEquiv, string $content) : MetaItem {
        return new self($httpEquiv, $content);
    }

    /**
     * @return string
     */
    public function render() : string {
        return "<meta http-equiv=\"{$this->httpEquiv}\" content=\"{$this->content}\">";
    }

    /**
     * HttpEquivMeta constructor.
     *
     * @param string $httpEquiv
     * @param string $content
     */
    public function __construct(string $httpEquiv, string $content) {
        $this->httpEquiv = $httpEquiv;
        $this->content = $content;
    }
}
