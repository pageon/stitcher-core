<?php

namespace Stitcher\Variable;

use Pageon\Html\Image\ImageFactory;
use Pageon\Lib\Markdown\MarkdownParser;
use Stitcher\DynamicFactory;
use Symfony\Component\Yaml\Yaml;
use TypeError;

class VariableFactory extends DynamicFactory
{
    /** @var \Symfony\Component\Yaml\Yaml */
    private $yamlParser;

    /** @var \Pageon\Lib\Markdown\MarkdownParser */
    private $markdownParser;

    /** @var \Pageon\Html\Image\ImageFactory */
    private $imageParser;

    /** @var \Stitcher\Variable\VariableParser */
    private $variableParser;

    public function __construct()
    {
        $this->setJsonRule();
        $this->setYamlRule();
        $this->setMarkdownRule();
        $this->setImageRule();
        $this->setDirectoryRule();
        $this->setHtmlRule();
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

    public function setMarkdownParser(MarkdownParser $markdownParser): VariableFactory
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
            } catch (TypeError $e) {
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
            if (pathinfo($value, PATHINFO_EXTENSION) !== 'json') {
                return null;
            }

            return JsonVariable::make($value);
        });
    }

    private function setYamlRule(): void
    {
        $this->setRule(YamlVariable::class, function (string $value) {
            if (! $this->yamlParser) {
                return null;
            }

            $extension = pathinfo($value, PATHINFO_EXTENSION);

            if (! \in_array($extension, ['yaml', 'yml'])) {
                return null;
            }

            return YamlVariable::make($value, $this->yamlParser, $this->variableParser);
        });
    }

    private function setMarkdownRule(): void
    {
        $this->setRule(MarkdownVariable::class, function (string $value) {
            if (! $this->markdownParser) {
                return null;
            }

            if (pathinfo($value, PATHINFO_EXTENSION) !== 'md') {
                return null;
            }

            return MarkdownVariable::make($value, $this->markdownParser);
        });
    }

    private function setHtmlRule(): void
    {
        $this->setRule(HtmlVariable::class, function (string $value) {
            if (pathinfo($value, PATHINFO_EXTENSION) !== 'html') {
                return null;
            }

            return new HtmlVariable($value);
        });
    }

    private function setImageRule(): void
    {
        $this->setRule(ImageVariable::class, function ($value) {
            if (! $this->imageParser) {
                return null;
            }

            $srcPath = \is_array($value) ? $value['src'] ?? null : $value;

            $extension = pathinfo($srcPath, PATHINFO_EXTENSION);

            if (! \in_array($extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                return null;
            }

            return ImageVariable::make($value, $this->imageParser);
        });
    }

    private function setDirectoryRule(): void
    {
        $this->setRule(DirectoryVariable::class, function ($value) {
            if (!is_string($value) || substr($value, -1) !== '/') {
                return null;
            }

            if (strpos($value, 'http') !== null) {
                return null;
            }

            return new DirectoryVariable($value, $this->variableParser);
        });
    }
}
