<?php

namespace Stitcher\Variable;

use Pageon\Lib\Markdown\MarkdownParser;
use Stitcher\Exception\InvalidConfiguration;
use Stitcher\File;

class MarkdownVariable extends AbstractVariable
{
    private $parser;

    public function __construct(string $unparsed, MarkdownParser $parser)
    {
        parent::__construct($unparsed);

        $this->parser = $parser;
    }

    public static function make(
        string $value,
        MarkdownParser $parser
    ): MarkdownVariable {
        return new self($value, $parser);
    }

    public function parse(): AbstractVariable
    {
        $contents = File::read($this->unparsed);

        if (! $contents) {
            throw InvalidConfiguration::fileNotFound($this->unparsed);
        }

        $this->parsed = $this->parser->parse($contents);

        return $this;
    }
}
