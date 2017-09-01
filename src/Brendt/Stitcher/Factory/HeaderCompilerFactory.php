<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Site\Http\HeaderCompiler;

class HeaderCompilerFactory
{
    private $environment;
    /**
     * @var HeaderCompiler[]
     */
    private $headerCompilers = [];

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    public function setEnvironment(string $environment) : HeaderCompilerFactory
    {
        $this->environment = $environment;

        return $this;
    }

    public function addHeaderCompiler(string $environment, HeaderCompiler $headerCompiler) : HeaderCompilerFactory
    {
        $this->headerCompilers[$environment] = $headerCompiler;

        return $this;
    }

    public function getHeaderCompilerByEnvironment()
    {
        return $this->headerCompilers[$this->environment] ?? null;
    }
}
