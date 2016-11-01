<?php

namespace brendt\stitcher\element;

use brendt\stitcher\exception\TemplateNotFoundException;

class Page {

    /** @var string */
    protected $id;

    /** @var string */
    protected $template;

    /** @var array */
    protected $variables = [];

    /** @var Adapter[] */
    protected $adapters;

    /** @var array */
    protected $parsedFields = [];

    /**
     * Page constructor.
     *
     * @param $id
     * @param $data
     *
     * @throws TemplateNotFoundException
     */
    public function __construct($id, $data) {
        if (!isset($data['template'])) {
            throw new TemplateNotFoundException("No template was set for page {$id}");
        }

        $this->id = $id;
        $this->template = $data['template'];

        if (isset($data['data'])) {
            $this->variables = $data['data'];
        }

        if (isset($data['adapters'])) {
            foreach ($data['adapters'] as $type => $adapterConfig) {
                $this->adapters[$type] = $adapterConfig;
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getVariables() {
        return $this->variables;
    }

    /**
     * @return Adapter[]
     */
    public function getAdapters() {
        return $this->adapters;
    }

    /**
     * @param $name
     *
     * @return Adapter|null
     */
    public function getAdapter($name) {
        if (!isset($this->adapters[$name])) {
            return null;
        }

        return $this->adapters[$name];
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getVariable($name) {
        if (!isset($this->variables[$name])) {
            return null;
        }

        return $this->variables[$name];
    }

    /**
     * @param $name
     * @param $data
     *
     * @return Page
     */
    public function setVariable($name, $data) {
        $this->variables[$name] = $data;

        return $this;
    }

    /**
     * @param $name
     *
     * @return Page
     */
    public function clearAdapter($name) {
        if (isset($this->adapters[$name])) {
            unset($this->adapters[$name]);
        }

        return $this;
    }

    /**
     * @param string $id
     *
     * @return Page
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function isParsedField($name) {
        return isset($this->parsedFields[$name]);
    }

    /**
     * @param $name
     *
     * @return Page
     */
    public function setParsedField($name) {
        $this->parsedFields[$name] = true;

        return $this;
    }

}
