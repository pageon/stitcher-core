<?php

namespace brendt\stitcher\engine;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Interface TemplateEngine
 * @package brendt\stitcher\engine
 */
interface TemplateEngine {

    /**
     * Render the template and return output HTML
     *
     * @param SplFileInfo $path
     *
     * @return string
     */
    public function renderTemplate(SplFileInfo $path);

    /**
     * Add an array of template variables
     *
     * @param array $variables
     *
     * @return TemplateEngine
     */
    public function addTemplateVariables(array $variables);

    /**
     * @param $name
     * @param $value
     *
     * @return TemplateEngine
     */
    public function addTemplateVariable($name, $value);

    /**
     * Clear all template variables
     *
     * @return TemplateEngine
     */
    public function clearTemplateVariables();

    /**
     * @param $variable
     *
     * @return TemplateEngine
     */
    public function clearTemplateVariable($variable);

    /**
     * @return mixed
     */
    public function getTemplateExtension();
}
