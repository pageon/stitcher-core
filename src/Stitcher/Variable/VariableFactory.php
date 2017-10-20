<?php

namespace Stitcher\Variable;

use Pageon\Html\Image\ImageFactory;
use Parsedown;
use Stitcher\DynamicFactory;
use Symfony\Component\Yaml\Yaml;

class VariableFactory extends DynamicFactory
{
    private $yamlParser = null;
    private $markdownParser = null;
    private $imageParser = null;
    private $variableParser = null;

    public function __construct()
    {
        $this->setJsonRule();
        $this->setYamlRule();
        $this->setMarkdownRule();
        $this->setImageRule();
    }

    public static function make(): VariableFactory
    {
        return new self();
    }

    public function setYamlParser(Yaml $yamlParser): VariableFactory
    {
        $this->yamlParser = $yamlParser;

        return $this;
    }

    public function setMarkdownParser(Parsedown $markdownParser): VariableFactory
    {
        $this->markdownParser = $markdownParser;

        return $this;
    }

    public function setImageParser(ImageFactory $imageParser): VariableFactory
    {
        $this->imageParser = $imageParser;

        return $this;
    }

    public function setVariableParser(VariableParser $variableParser): VariableFactory
    {
        $this->variableParser = $variableParser;

        return $this;
    }

    public function create($value): AbstractVariable
    {
        foreach ($this->getRules() as $rule) {
            try {
                $variable = $rule($value);
            } catch (\TypeError $e) {
                continue;
            }

            if ($variable instanceof AbstractVariable) {
                return $variable;
            }
        }

        return DefaultVariable::make($value);
    }

    private function setJsonRule(): DynamicFactory
    {
        return $this->setRule(JsonVariable::class, function (string $value) {
            if (is_string($value) && pathinfo($value, PATHINFO_EXTENSION) === 'json') {
                return JsonVariable::make($value);
            }

            return null;
        });
    }

    private function setYamlRule(): void
    {
        $this->setRule(YamlVariable::class, function (string $value) {
            if (! $this->yamlParser) {
                return null;
            }

            $extension = pathinfo($value, PATHINFO_EXTENSION);

            if (in_array($extension, ['yaml', 'yml'])) {
                return YamlVariable::make($value, $this->yamlParser, $this->variableParser);
            }

            return null;
        });
    }

    private function setMarkdownRule(): void
    {
        $this->setRule(MarkdownVariable::class, function (string $value) {
            if ($this->markdownParser && pathinfo($value, PATHINFO_EXTENSION) === 'md') {
                return MarkdownVariable::make($value, $this->markdownParser);
            }

            return null;
        });
    }

    private function setImageRule(): void
    {
        $this->setRule(ImageVariable::class, function ($value) {
            if (! $this->imageParser) {
                return null;
            }

            $srcPath = is_array($value) ? $value['src'] ?? null : $value;

            $extension = pathinfo($srcPath, PATHINFO_EXTENSION);

            // TODO: Let ImageVariable take a config array, and let it parse that array itself.

            if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                return ImageVariable::make($srcPath, $this->imageParser, $value['alt'] ?? null);
            }

            return null;
        });
    }
}
