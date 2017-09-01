<?php

namespace Brendt\Stitcher\Template;

use Symfony\Component\Finder\SplFileInfo;

/**
 * This interface is used as a bridge between different template engine's API and Stitcher.
 *
 * @see \Brendt\Stitcher\Stitcher::stitch()
 */
interface TemplateEngine
{
    /**
     * Render the template and return output HTML.
     *
     * @param SplFileInfo $path
     *
     * @return string
     */
    public function renderTemplate(SplFileInfo $path);

    /**
     * Add an array of template variables.
     *
     * @param array $variables
     *
     * @return TemplateEngine
     */
    public function addTemplateVariables(array $variables);

    /**
     * Add a template variable.
     *
     * @param $name
     * @param $value
     *
     * @return TemplateEngine
     */
    public function addTemplateVariable($name, $value);

    /**
     * Check whether a template variable is set
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasTemplateVariable(string $name) : bool;

    /**
     * Clear all template variables.
     *
     * @return TemplateEngine
     */
    public function clearTemplateVariables();

    /**
     * Clear a template variable.
     *
     * @param $variable
     *
     * @return TemplateEngine
     */
    public function clearTemplateVariable($variable);

    public function getTemplateExtensions() : array;
}
