<?php

namespace Stitcher\Variable;

abstract class AbstractVariable
{
    protected $unparsed;
    protected $parsed = null;

    public abstract function parse(): AbstractVariable;

    public function __construct($unparsed)
    {
        $this->unparsed = $unparsed;
    }

    /**
     * @return mixed
     */
    public function unparsed()
    {
        return $this->unparsed;
    }

    /**
     * @return mixed
     */
    public function parsed()
    {
        if (! $this->parsed) {
            $this->parse();
        }

        return $this->parsed;
    }
}
