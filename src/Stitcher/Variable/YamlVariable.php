<?php

namespace Stitcher\Variable;

use Stitcher\File;
use Symfony\Component\Yaml\Yaml;

class YamlVariable extends AbstractVariable
{
    private $parser;
    private $variableParser;

    public function __construct(
        string $unparsed,
        Yaml $parser,
        VariableParser $variableParser
    ) {
        parent::__construct($unparsed);

        $this->parser = $parser;
        $this->variableParser = $variableParser;
    }

    public static function make(
        string $value,
        Yaml $parser,
        VariableParser $variableParser
    ): YamlVariable {
        return new self($value, $parser, $variableParser);
    }

    public function parse(): AbstractVariable
    {
        $this->parsed = $this->parser::parse(File::read($this->unparsed));

        foreach ($this->parsed as $id => $parsedItem) {
            if (! \is_array($parsedItem) || isset($parsedItem['id'])) {
                continue;
            }

            $parsedItem['id'] = $id;

            $this->parsed[$id] = $parsedItem;
        }

        $this->parsed = $this->parseRecursive($this->parsed);

        return $this;
    }

    private function parseRecursive($unparsedValue)
    {
        $unparsedValue = $this->variableParser->getVariable($unparsedValue);

        if ($unparsedValue instanceof DefaultVariable) {
            $parsedValue = $unparsedValue->getParsed();

            if (is_array($parsedValue)) {
                foreach ($parsedValue as $key => &$property) {
                    $property = $this->parseRecursive($property);
                }
            }
        } else {
            $parsedValue = $unparsedValue->getParsed();
        }

        return $parsedValue;
    }
}
