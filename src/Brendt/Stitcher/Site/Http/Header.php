<?php

namespace Brendt\Stitcher\Site\Http;

class Header
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
     * Create a new header
     *
     * @param string $name
     * @param string $content
     *
     * @return Header
     */
    public static function create(string $name, string $content) : Header {
        return new self($name, $content);
    }

    /**
     * Create a Link header
     *
     * @param string $content
     *
     * @return Header
     */
    public static function link(string $content) : Header {
        return new self('Link', $content);
    }

    /**
     * Header constructor.
     *
     * @param string $name
     * @param string $content
     */
    public function __construct(string $name, string $content) {
        $this->name = $name;
        $this->content = $content;
    }

    /**
     * Get the header as string for .htaccess files
     *
     * @return string
     */
    public function getHtaccessHeader() : string {
        return "{$this->name} {$this->content}";
    }

    /**
     * Het the header as string
     *
     * @return string
     */
    public function __toString() : string {
        return "{$this->name}: {$this->content}";
    }

}
