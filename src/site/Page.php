<?php

namespace brendt\stitcher\site;

use brendt\stitcher\exception\TemplateNotFoundException;

/**
 * A Page object represents a page entry configured in a YAML file located in the `src/sites/` directory.
 * Constructing a new Page requires a unique ID and an array of data. This array can hold several different arguments:
 *
 *      - `template`: the only required argument. This variable is a path to a template file.
 *              This path is relative to the `directories.src` or `directories.template` configuration entry
 *              @see \brendt\stitcher\Stitcher::loadTemplates
 *
 *
 *      - `data`: an optional array of variables which will be mapped onto the template.
 *              Each of these variables is parsed during compile time.
 *              @see \brendt\stitcher\parser\AbstractParser
 *
 *      - `adapters`: an optional array of Adapters for this page. Adapters are used to adapt a page's configuration
 *              to another one.
 *              @see \brendt\stitcher\adapter\Adapter
 *
 * @package brendt\stitcher\site
 */
class Page {

    /**
     * The page's ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The template path of this page.
     *
     * @var string
     */
    protected $templatePath;

    /**
     * The variables of this page, which will be available in the rendered template.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * The adapters of this page.
     * Adapters will transform a page's variables and/or the page itself into one or more pages.
     *
     * @var Adapter[]
     */
    protected $adapters;

    /**
     * An array containing a list of parsed variables.
     *
     * @see setVariableIsParsed
     *
     * @var array
     */
    protected $parsedVariables = [];

    /**
     * Construct a new page
     *
     * @param string $id
     * @param array  $data
     *
     * @throws TemplateNotFoundException
     */
    public function __construct($id, array $data = []) {
        if (!isset($data['template'])) {
            throw new TemplateNotFoundException("No template was set for page {$id}");
        }

        $this->id = $id;
        $this->templatePath = $data['template'];

        if (isset($data['variables'])) {
            $this->variables += $data['variables'];
        }

        if (isset($data['adapters'])) {
            foreach ($data['adapters'] as $type => $adapterConfig) {
                $this->adapters[$type] = $adapterConfig;
            }
        }
    }

    /**
     * Defines a variable as parsed.
     * Parsed variables will be ignored by Stitcher when compiling the website.
     * Adapters can define parsed variables to indicate Stitcher it should skip parsing that variable during compile time.
     *
     * @param $name
     *
     * @return Page
     *
     * @see \brendt\stitcher\Stitcher::parseVariables
     * @see \brendt\stitcher\adapter\CollectionAdapter::transform
     * @see \brendt\stitcher\adapter\PagincationAdapter::transform
     */
    public function setVariableIsParsed($name) {
        $this->parsedVariables[$name] = true;

        return $this;
    }

    /**
     * Check whether a variable is parsed or not.
     * Parsed variables will be ignored by Stitcher during compile time.
     *
     * @param $name
     *
     * @return bool
     *
     * @see \brendt\stitcher\Stitcher::parseVariables
     */
    public function isParsedVariable($name) {
        return isset($this->parsedVariables[$name]);
    }

    /**
     * Get the ID of this page
     *
     * @return string
     *
     * @see \brendt\stitcher\Stitcher::stitch
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the template path of this page.
     *
     * @return string
     *
     * @see \brendt\stitcher\Stitcher::stitch
     */
    public function getTemplatePath() {
        return $this->templatePath;
    }

    /**
     * Get the variables of this page.
     *
     * @return array
     *
     * @see \brendt\stitcher\Stitcher::stitch
     * @see \brendt\stitcher\Stitcher::parseVariables
     */
    public function getVariables() {
        return $this->variables;
    }

    /**
     * Get the adapters of this page.
     *
     * @return Adapter[]
     *
     * @see \brendt\stitcher\Stitcher::parseAdapters
     */
    public function getAdapters() {
        return $this->adapters;
    }

    /**
     * Get an adapter configuration by name.
     *
     * @param $name
     *
     * @return Adapter|null
     *
     * @see \brendt\stitcher\adapter\CollectionAdapter::transform
     * @see \brendt\stitcher\adapter\PagincationAdapter::transform
     * @see \brendt\stitcher\controller\DevController::run
     */
    public function getAdapterConfig($name) {
        if (!isset($this->adapters[$name])) {
            return null;
        }

        return $this->adapters[$name];
    }

    /**
     * Get a variable by name.
     *
     * @param $name
     *
     * @return mixed|null
     *
     * @see \brendt\stitcher\adapter\CollectionAdapter::transform
     * @see \brendt\stitcher\adapter\PagincationAdapter::transform
     */
    public function getVariable($name) {
        if (!isset($this->variables[$name])) {
            return null;
        }

        return $this->variables[$name];
    }

    /**
     * Set the value of a variable.
     *
     * @param $name
     * @param $value
     *
     * @return Page
     *
     * @see \brendt\stitcher\adapter\CollectionAdapter::transform
     * @see \brendt\stitcher\adapter\PagincationAdapter::transform
     * @see \brendt\stitcher\Stitcher::parseVariables
     */
    public function setVariableValue($name, $value) {
        $this->variables[$name] = $value;

        return $this;
    }

    /**
     * Remove an adapter.
     *
     * @param $name
     *
     * @return Page
     *
     * @see \brendt\stitcher\adapter\CollectionAdapter::transform
     * @see \brendt\stitcher\adapter\PagincationAdapter::transform
     */
    public function removeAdapter($name) {
        if (isset($this->adapters[$name])) {
            unset($this->adapters[$name]);
        }

        return $this;
    }

    /**
     * Set the ID of this page.
     * An page's ID can be re-set after constructing when an adapter is creating other pages based on an existing page.
     *
     * @param string $id
     *
     * @return Page
     *
     * @see \brendt\stitcher\adapter\CollectionAdapter::transform
     * @see \brendt\stitcher\adapter\PagincationAdapter::transform
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

}
