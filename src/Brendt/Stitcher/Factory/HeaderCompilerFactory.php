<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Site\Http\HeaderCompiler;

class HeaderCompilerFactory
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var HeaderCompiler[]
     */
    private $headerCompilers = [];

    /**
     * HeaderCompilerFactory constructor.
     *
     * @param string $environment
     */
    public function __construct(string $environment) {
        $this->environment = $environment;
    }

    /**
     * @param string $environment
     *
     * @return HeaderCompilerFactory
     */
    public function setEnvironment(string $environment) : HeaderCompilerFactory {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @param string         $environment
     * @param HeaderCompiler $headerCompiler
     *
     * @return HeaderCompilerFactory
     */
    public function addHeaderCompiler(string $environment, HeaderCompiler $headerCompiler) : HeaderCompilerFactory {
        $this->headerCompilers[$environment] = $headerCompiler;

        return $this;
    }

    /**
     * @return HeaderCompiler|null
     */
    public function getHeaderCompilerByEnvironment() {
        return $this->headerCompilers[$this->environment] ?? null;
    }
}
