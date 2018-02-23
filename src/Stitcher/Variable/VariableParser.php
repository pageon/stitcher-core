<?php

namespace Stitcher\Variable;

class VariableParser
{
    private $factory;

    public function __construct(VariableFactory $factory)
    {
        $this->factory = $factory;
        $this->factory->setVariableParser($this);
    }

    public static function make(VariableFactory $factory): VariableParser
    {
        return new self($factory);
    }

    public function parse($unparsedValue)
    {
        $variable = $this->factory->create($unparsedValue);

        if ($variable) {
            $parsedValue = $variable->getParsed();
        } else {
            $parsedValue = $unparsedValue;
        }

        return $parsedValue;
    }

    public function getVariable($unparsedValue): AbstractVariable
    {
        return $this->factory->create($unparsedValue);
    }
}
