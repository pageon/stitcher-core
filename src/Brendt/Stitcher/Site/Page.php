<?php

namespace Brendt\Stitcher\Site;

use Pageon\Html\Meta\Meta;
use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Site\Http\Header;

/**
 * A Page object represents a page entry configured in a YAML file located in the `src/sites/` directory.
 * Constructing a new Page requires a unique ID and an array of data. This array can hold several different arguments:
 *
 *      - `template`: the only required argument. This variable is a path to a template file.
 *              This path is relative to the `directories.src` or `directories.template` configuration entry
 * @see     \Brendt\Stitcher\Stitcher::loadTemplates
 *
 *
 *      - `data`: an optional array of variables which will be mapped onto the template.
 *              Each of these variables is parsed during compile time.
 * @see     \Brendt\Stitcher\Parser\AbstractParser
 *
 *      - `adapters`: an optional array of Adapters for this page. Adapters are used to adapt a page's configuration
 *              to another one.
 * @see     \Brendt\Stitcher\Adapter\Adapter
 *
 * @package Brendt\Stitcher\Site
 */
class Page
{
    protected $meta;
    protected $data;
    protected $id;
    protected $templatePath;
    protected $variables = [];
    protected $adapters;
    protected $parsedVariables = [];
    /** @var Header[] */
    private $headers = [];

    public function __construct($id, array $data = [], Meta $meta = null)
    {
        if (!isset($data['template'])) {
            throw new TemplateNotFoundException("No template was set for page {$id}");
        }

        $this->id = $id;
        $this->data = $data;
        $this->templatePath = $data['template'];
        $this->meta = $meta ?? new Meta();

        if (isset($data['variables'])) {
            $this->variables += $data['variables'];
        }

        if (isset($data['adapters'])) {
            foreach ($data['adapters'] as $type => $adapterConfig) {
                $this->adapters[$type] = $adapterConfig;
            }
        }
    }

    public static function copy(Page $page) : Page
    {
        $copy = new Page($page->id, $page->data);

        foreach ($page->variables as $key => $value) {
            $copy->setVariableValue($key, $value);

            if ($page->isParsedVariable($key)) {
                $copy->setVariableIsParsed($key);
            }
        }

        return $copy;
    }

    /**
     * Defines a variable as parsed.
     * Parsed variables will be ignored by Stitcher when compiling the website.
     * Adapters can define parsed variables to indicate Stitcher it should skip parsing that variable during compile
     * time.
     *
     * @param $name
     *
     * @return Page
     *
     * @see \Brendt\Stitcher\Stitcher::parseVariables
     * @see \Brendt\Stitcher\adapter\CollectionAdapter::transform
     * @see \Brendt\Stitcher\adapter\PagincationAdapter::transform
     */
    public function setVariableIsParsed($name)
    {
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
     * @see \Brendt\Stitcher\Stitcher::parseVariables
     */
    public function isParsedVariable($name)
    {
        return isset($this->parsedVariables[$name]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * Get an adapter configuration by name.
     *
     * @param $name
     *
     * @return array
     *
     * @see \Brendt\Stitcher\adapter\CollectionAdapter::transform
     * @see \Brendt\Stitcher\adapter\PagincationAdapter::transform
     * @see \Brendt\Stitcher\Application\DevController::run
     */
    public function getAdapterConfig($name)
    {
        if (!isset($this->adapters[$name])) {
            return [];
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
     * @see \Brendt\Stitcher\adapter\CollectionAdapter::transform
     * @see \Brendt\Stitcher\adapter\PagincationAdapter::transform
     */
    public function getVariable($name)
    {
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
     * @see \Brendt\Stitcher\adapter\CollectionAdapter::transform
     * @see \Brendt\Stitcher\adapter\PagincationAdapter::transform
     * @see \Brendt\Stitcher\Stitcher::parseVariables
     */
    public function setVariableValue($name, $value)
    {
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
     * @see \Brendt\Stitcher\adapter\CollectionAdapter::transform
     * @see \Brendt\Stitcher\adapter\PagincationAdapter::transform
     */
    public function removeAdapter($name)
    {
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
     * @see \Brendt\Stitcher\adapter\CollectionAdapter::transform
     * @see \Brendt\Stitcher\adapter\PagincationAdapter::transform
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function addHeader(Header $header)
    {
        $this->headers[] = $header;
    }

    /**
     * @return Header[]
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getMeta() : Meta
    {
        return $this->meta;
    }
}
