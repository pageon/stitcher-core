<?php

namespace Stitcher\Page;

use Stitcher\Exception\InvalidConfiguration;
use Stitcher\Variable\VariableParser;

class PageFactory
{
    private $variableParser;

    public function __construct(VariableParser $variableParser)
    {
        $this->variableParser = $variableParser;
    }

    public static function make(VariableParser $variableParser): PageFactory
    {
        return new self($variableParser);
    }

    public function create($value): Page
    {
        $id = $value['id'] ?? null;

        if (! $id) {
            throw InvalidConfiguration::pageIdMissing();
        }

        $template = $value['template'] ?? null;
        $variables = $value['variables'] ?? [];

        if (! $template) {
            throw InvalidConfiguration::pageTemplateMissing($id);
        }

        foreach ($variables as $key => $variable) {
            $variables[$key] = $this->variableParser->parse($variable);
        }

        $page = Page::make($id, $template, $variables);

        return $page;
    }
}
